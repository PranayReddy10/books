<?php

namespace App\Services;

use App\Result;
use App\University;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Connects a student's JNTU results to their account as soon as the account has
 * a JNTU university and a hall ticket — at sign-up, on completing a Google
 * profile, or when an admin adds them — so nobody has to tap "fetch" first.
 *
 * The university feed answers "queued" while it scrapes a hall ticket it has
 * not seen, so a student whose results are not ready yet goes on a pending
 * list that the scheduler retries (results:sync-pending).
 */
class StudentResults
{
    const PENDING_KEY = 'jntuh:pending_users';

    /** Retries are every 5 minutes; give the feed about a day before giving up. */
    const MAX_ATTEMPTS = 288;

    /**
     * Run sync() for this user after the response has gone out, so sign-up
     * never waits on the university feed.
     */
    public static function syncAfterResponse($userId)
    {
        app()->terminating(function () use ($userId) {
            $user = User::find($userId);
            if ($user) {
                (new static())->sync($user);
            }
        });
    }

    /** Does this account's university use the JNTU results feed? */
    public static function eligible(User $user)
    {
        return config('jntuh.enabled')
            && stripos((string) $user->university, 'JNTU') !== false
            && JntuhConnect::validRoll($user->rollnumber);
    }

    /**
     * Fetch, store and link this user's results.
     *
     * @return string ready | queued | linked | skipped | conflict | error
     */
    public function sync(User $user)
    {
        if (!self::eligible($user)) {
            self::forget($user->id);
            return 'skipped';
        }
        $hall = strtoupper(trim($user->rollnumber));

        // Results already belong to another account: leave both alone.
        $existing = Result::where('hall_ticket_no', $hall)->first();
        if ($existing && $existing->user_id && (int) $existing->user_id !== (int) $user->id) {
            self::forget($user->id);
            Log::warning('Result sync: hall ticket already linked to another user', ['hall' => $hall, 'user' => $user->id]);
            return 'conflict';
        }

        // An admin imported this hall ticket before the student signed up.
        if ($existing && !$existing->user_id) {
            $existing->user_id = $user->id;
            $existing->save();
        }

        try {
            $out = (new JntuhConnect())->academicResult($hall);
        } catch (\Exception $e) {
            Log::warning('Result sync fetch failed: ' . $e->getMessage(), ['user' => $user->id]);
            self::remember($user->id);
            return 'error';
        }

        if ($out['state'] === 'queued') {
            self::remember($user->id);
            return 'queued';
        }
        if ($out['state'] !== 'ready') {
            // The feed has nothing for this hall ticket; keep trying for a while
            // in case results are just not published yet.
            self::remember($user->id);
            return $existing ? 'linked' : 'error';
        }

        $importer = new JntuhResultImporter();
        $normalized = $importer->normalize($out['data']);

        $this->fillProfile($user, $normalized);

        if (empty($normalized['semesters'])) {
            // Identity found but no marks published yet (first-years): done for now.
            self::forget($user->id);
            return $existing ? 'linked' : 'skipped';
        }

        try {
            $importer->store($normalized, $hall, $user->id, $this->universityId($user));
        } catch (\Exception $e) {
            Log::error('Result sync import failed: ' . $e->getMessage(), ['user' => $user->id]);
            self::remember($user->id);
            return 'error';
        }

        self::forget($user->id);
        return 'ready';
    }

    /** Retry everyone still waiting on the feed. Returns [user_id => state]. */
    public function syncPending()
    {
        $done = [];
        foreach (self::pending() as $userId => $attempts) {
            $user = User::find($userId);
            if (!$user) {
                self::forget($userId);
                continue;
            }
            $done[$userId] = $this->sync($user);
        }
        return $done;
    }

    /** [user_id => attempts] still waiting. */
    public static function pending()
    {
        $list = Cache::get(self::PENDING_KEY, []);
        return is_array($list) ? $list : [];
    }

    protected static function remember($userId)
    {
        $list = self::pending();
        $attempts = isset($list[$userId]) ? $list[$userId] + 1 : 1;
        if ($attempts > self::MAX_ATTEMPTS) {
            unset($list[$userId]);
        } else {
            $list[$userId] = $attempts;
        }
        Cache::forever(self::PENDING_KEY, $list);
    }

    protected static function forget($userId)
    {
        $list = self::pending();
        if (isset($list[$userId])) {
            unset($list[$userId]);
            Cache::forever(self::PENDING_KEY, $list);
        }
    }

    /**
     * Fill blanks on the profile from the university record. Nothing the
     * student typed is overwritten.
     */
    protected function fillProfile(User $user, array $normalized)
    {
        $changed = false;
        foreach (['branch' => 'branch', 'regulation' => 'regulation', 'father_name' => 'father_name'] as $from => $col) {
            if ($col === 'father_name' && !Schema::hasColumn('users', 'father_name')) {
                continue;
            }
            if (trim((string) $user->$col) === '' && trim((string) $normalized[$from]) !== '') {
                $user->$col = $normalized[$from];
                $changed = true;
            }
        }
        if ($changed) {
            $user->save();
        }
    }

    protected function universityId(User $user)
    {
        $id = University::where('university_name', $user->university)->value('id');
        return $id ?: null;
    }
}
