<?php

namespace App\Services;

use App\Result;
use App\ResultSemester;
use App\ResultSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Turns a jntuhconnect payload into rows in results / result_semesters /
 * result_subjects.
 *
 * The upstream JSON is read structurally rather than by fixed key names: find
 * the semester nodes (keys like "1-1"), then read each subject dict by what its
 * keys mean. That survives the small naming differences between the API's
 * academicresult / allresult views and any later rename, which matters because
 * a wrong guess here would silently write empty mark sheets.
 */
class JntuhResultImporter
{
    /**
     * Flatten a payload into
     *   ['student_name','father_name','college_code','regulation',
     *    'cgpa','total_credits','total_backlogs','semesters' => [...]]
     */
    public function normalize(array $payload)
    {
        $out = [
            'student_name'   => '',
            'father_name'    => '',
            'college_code'   => '',
            'regulation'     => '',
            'cgpa'           => null,
            'total_credits'  => null,
            'total_backlogs' => null,
            'semesters'      => [],
        ];

        $this->readDetails($payload, $out);
        $this->readTotals($payload, $out);

        $semesters = [];
        $this->collectSemesters($payload, $semesters);

        // "1-1" before "1-2" before "2-1".
        uksort($semesters, function ($a, $b) {
            return strnatcasecmp($a, $b);
        });

        foreach ($semesters as $code => $node) {
            $subjects = $this->readSubjects($node);
            if (empty($subjects)) {
                continue;
            }
            $out['semesters'][] = [
                'sem_code'        => $code,
                'sgpa'            => $this->firstNumeric($node, ['sgpa']),
                'credits_earned'  => $this->firstNumeric($node, ['credits', 'totalcredits', 'creditsobtained']),
                'exam_month_year' => (string) $this->firstString($node, ['examdate', 'exammonthyear', 'month', 'date']),
                'subjects'        => $subjects,
            ];
        }

        return $out;
    }

    /**
     * Write a normalized payload against a hall ticket number.
     *
     * Locked (admin-verified) semesters are never touched — the same rule
     * result_save follows — so an auto-fetch can top up a result without
     * undoing a manual correction.
     *
     * @return Result
     */
    public function store(array $normalized, $hallTicket, $userId = null, $universityId = null)
    {
        $hall = strtoupper(trim((string) $hallTicket));

        return DB::transaction(function () use ($normalized, $hall, $userId, $universityId) {
            $result = Result::where('hall_ticket_no', $hall)->first();
            if (!$result) {
                $result = new Result();
                $result->hall_ticket_no = $hall;
                $result->is_public      = 0;
                $result->locked         = 0;
                $result->share_token    = Str::random(24);
            }

            if ($userId && !$result->user_id) {
                $result->user_id = $userId;
            }
            if ($universityId && !$result->university_id) {
                $result->university_id = $universityId;
            }
            if ($normalized['student_name'] !== '') {
                $result->student_name = $normalized['student_name'];
            }
            if ($normalized['regulation'] !== '' && !$result->regulation) {
                $result->regulation = $normalized['regulation'];
            }
            // Fetched straight from the university feed, so it counts as verified.
            $result->source   = 'jntuh';
            $result->verified = 1;
            $result->save();

            $lockedCodes = [];
            foreach ($result->semesters()->get() as $sem) {
                if ((int) $sem->locked === 1) {
                    $lockedCodes[strtolower(trim($sem->sem_code))] = true;
                }
            }

            $unlockedIds = ResultSemester::where('result_id', $result->id)->where('locked', 0)->pluck('id')->toArray();
            if (!empty($unlockedIds)) {
                ResultSubject::whereIn('result_semester_id', $unlockedIds)->delete();
                ResultSemester::whereIn('id', $unlockedIds)->delete();
            }

            foreach ($normalized['semesters'] as $s) {
                $code = trim((string) $s['sem_code']);
                if ($code !== '' && isset($lockedCodes[strtolower($code)])) {
                    continue;
                }

                $sem = new ResultSemester();
                $sem->result_id       = $result->id;
                $sem->sem_code        = $code;
                $sem->exam_month_year = $s['exam_month_year'];
                $sem->verified        = 1;   // came from the university feed
                $sem->locked          = 0;   // an admin still has to lock it
                $sem->save();

                $semGp = 0.0;
                $semCr = 0.0;
                foreach ($s['subjects'] as $sub) {
                    $row = new ResultSubject();
                    $row->result_semester_id = $sem->id;
                    $row->subject_code = $sub['subject_code'];
                    $row->subject_name = $sub['subject_name'];
                    $row->internal     = $sub['internal'];
                    $row->external     = $sub['external'];
                    $row->total        = $sub['total'];
                    $row->grade        = $sub['grade'];
                    $row->credits      = $sub['credits'];
                    $row->grade_points = function_exists('jntuh_subject_grade_points')
                        ? jntuh_subject_grade_points($sub['grade'], $sub['credits'])
                        : null;
                    $row->is_backlog   = $sub['is_backlog'];
                    $row->save();

                    $cr = ($row->credits !== null) ? (float) $row->credits : 0.0;
                    if ($cr > 0) {
                        $semGp += (($row->grade_points !== null) ? (float) $row->grade_points : 0.0) * $cr;
                        $semCr += $cr;
                    }
                }

                // Trust our own arithmetic over the feed's, except when we have
                // no credited subjects to work from.
                $computed = ($semCr > 0) ? round($semGp / $semCr, 2) : null;
                $sem->sgpa = $computed !== null ? $computed : $s['sgpa'];
                $sem->credits_earned = ($semCr > 0) ? $semCr : $s['credits_earned'];
                $sem->save();
            }

            $grandGp = 0.0;
            $grandCr = 0.0;
            $backlogs = 0;
            foreach (ResultSemester::where('result_id', $result->id)->get() as $sem) {
                foreach ($sem->subjects()->get() as $sub) {
                    $cr = ($sub->credits !== null) ? (float) $sub->credits : 0.0;
                    if ($cr > 0) {
                        $grandGp += (($sub->grade_points !== null) ? (float) $sub->grade_points : 0.0) * $cr;
                        $grandCr += $cr;
                    }
                    if ((int) $sub->is_backlog === 1) {
                        $backlogs++;
                    }
                }
            }
            $result->current_cgpa   = ($grandCr > 0) ? round($grandGp / $grandCr, 2) : $normalized['cgpa'];
            $result->total_credits  = ($grandCr > 0) ? $grandCr : $normalized['total_credits'];
            $result->backlogs_count = $backlogs;
            $result->save();

            return $result;
        });
    }

    // ------------------------------------------------------------- reading

    protected function readDetails(array $payload, array &$out)
    {
        $details = null;
        foreach ($payload as $key => $value) {
            if (is_array($value) && preg_match('/detail|student|profile|info/i', (string) $key)) {
                $details = $value;
                break;
            }
        }
        // Some responses put the identity fields at the top level instead.
        $sources = array_filter([$details, $payload], 'is_array');

        foreach ($sources as $src) {
            foreach ($src as $key => $value) {
                if (!is_scalar($value)) {
                    continue;
                }
                $k = strtolower(preg_replace('/[^a-z]/i', '', (string) $key));
                $v = trim((string) $value);
                if ($v === '') {
                    continue;
                }
                if ($out['father_name'] === '' && strpos($k, 'father') !== false) {
                    $out['father_name'] = $v;
                } elseif ($out['college_code'] === '' && strpos($k, 'college') !== false) {
                    $out['college_code'] = $v;
                } elseif ($out['regulation'] === '' && strpos($k, 'regulation') !== false) {
                    $out['regulation'] = $v;
                } elseif ($out['student_name'] === '' && in_array($k, ['name', 'studentname'], true)) {
                    $out['student_name'] = $v;
                }
            }
        }
    }

    protected function readTotals(array $payload, array &$out)
    {
        $walk = function ($node) use (&$walk, &$out) {
            if (!is_array($node)) {
                return;
            }
            foreach ($node as $key => $value) {
                $k = strtolower(preg_replace('/[^a-z]/i', '', (string) $key));
                if (is_scalar($value) && $value !== '') {
                    if ($out['cgpa'] === null && $k === 'cgpa') {
                        $out['cgpa'] = (float) $value;
                    } elseif ($out['total_credits'] === null && in_array($k, ['totalcredits', 'totalobtainedcredits'], true)) {
                        $out['total_credits'] = (float) $value;
                    } elseif ($out['total_backlogs'] === null && $k === 'totalbacklogs') {
                        $out['total_backlogs'] = (int) $value;
                    }
                } elseif (is_array($value) && !$this->isSemesterCode((string) $key)) {
                    $walk($value);
                }
            }
        };
        $walk($payload);
    }

    /** Depth-first hunt for nodes keyed like "1-1", "2-2". */
    protected function collectSemesters($node, array &$found)
    {
        if (!is_array($node)) {
            return;
        }
        foreach ($node as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            if ($this->isSemesterCode((string) $key)) {
                $code = $this->normalizeSemCode((string) $key);
                if (!isset($found[$code])) {
                    $found[$code] = $value;
                }
                continue;
            }
            // The other layout: a list of semester objects that name themselves,
            // e.g. [{"semester":"2-1","sgpa":8.1,"subjects":[...]}].
            $declared = $this->declaredSemCode($value);
            if ($declared !== null) {
                if (!isset($found[$declared])) {
                    $found[$declared] = $value;
                }
                continue;
            }
            $this->collectSemesters($value, $found);
        }
    }

    /** A semester code carried as a field of the node rather than as its key. */
    protected function declaredSemCode($node)
    {
        if (!is_array($node)) {
            return null;
        }
        foreach ($node as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $k = strtolower(preg_replace('/[^a-z]/i', '', (string) $key));
            if (strpos($k, 'sem') === 0 && $this->isSemesterCode((string) $value)) {
                return $this->normalizeSemCode((string) $value);
            }
        }
        return null;
    }

    protected function isSemesterCode($key)
    {
        return (bool) preg_match('/^\s*[1-5]\s*[-_ ]\s*[1-2]\s*$/', $key);
    }

    protected function normalizeSemCode($key)
    {
        preg_match('/([1-5])\s*[-_ ]\s*([1-2])/', $key, $m);
        return $m ? $m[1] . '-' . $m[2] : trim($key);
    }

    /**
     * A semester node holds its subjects under a list key ("subjects", "result"),
     * as its own numeric children, or one level deeper under each exam attempt.
     * Rather than encode those layouts, collect every dict that reads like a
     * subject and let dedupe() keep the best attempt per subject.
     */
    protected function readSubjects($node)
    {
        $rows = [];
        $this->gatherSubjectDicts($node, $rows);

        $mapped = [];
        foreach ($rows as $row) {
            $subject = $this->mapSubject($row);
            if ($subject !== null) {
                $mapped[] = $subject;
            }
        }

        return $this->dedupe($mapped);
    }

    protected function gatherSubjectDicts($node, array &$rows, $depth = 0)
    {
        if (!is_array($node) || $depth > 5) {
            return;
        }
        if ($this->looksLikeSubject($node)) {
            $rows[] = $node;
            return;
        }
        foreach ($node as $value) {
            if (is_array($value)) {
                $this->gatherSubjectDicts($value, $rows, $depth + 1);
            }
        }
    }

    protected function looksLikeSubject($row)
    {
        if (!is_array($row)) {
            return false;
        }
        $hasName = false;
        $hasGrade = false;
        foreach ($row as $key => $value) {
            if (!is_scalar($value) && $value !== null) {
                return false;
            }
            $k = strtolower((string) $key);
            if (strpos($k, 'name') !== false || strpos($k, 'code') !== false) {
                $hasName = true;
            }
            if (strpos($k, 'grade') !== false || strpos($k, 'credit') !== false || strpos($k, 'total') !== false) {
                $hasGrade = true;
            }
        }
        return $hasName && $hasGrade;
    }

    protected function mapSubject($row)
    {
        $code = '';
        $name = '';
        $internal = null;
        $external = null;
        $total = null;
        $grade = '';
        $credits = null;

        foreach ($row as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $k = strtolower(preg_replace('/[^a-z]/i', '', (string) $key));
            $v = trim((string) $value);

            if ($code === '' && strpos($k, 'code') !== false) {
                $code = $v;
            } elseif ($name === '' && strpos($k, 'name') !== false) {
                $name = $v;
            } elseif ($internal === null && strpos($k, 'internal') !== false) {
                $internal = $this->intOrNull($v);
            } elseif ($external === null && strpos($k, 'external') !== false) {
                $external = $this->intOrNull($v);
            } elseif ($credits === null && strpos($k, 'credit') !== false) {
                $credits = is_numeric($v) ? (float) $v : null;
            } elseif ($grade === '' && strpos($k, 'grade') !== false && strpos($k, 'point') === false) {
                $grade = strtoupper($v);
            } elseif ($total === null && strpos($k, 'total') !== false) {
                $total = $this->intOrNull($v);
            }
        }

        if ($code === '' && $name === '') {
            return null;
        }
        if ($total === null && ($internal !== null || $external !== null)) {
            $total = (int) $internal + (int) $external;
        }

        return [
            'subject_code' => $code,
            'subject_name' => $name,
            'internal'     => $internal,
            'external'     => $external,
            'total'        => $total,
            'grade'        => $grade,
            'credits'      => $credits,
            'is_backlog'   => $this->isFail($grade) ? 1 : 0,
        ];
    }

    protected function isFail($grade)
    {
        $g = strtoupper(trim((string) $grade));
        return $g === 'F' || $g === 'AB' || $g === 'ABSENT' || $g === '-F';
    }

    protected function intOrNull($v)
    {
        return is_numeric($v) ? (int) $v : null;
    }

    /** Same subject can appear twice when a payload nests exams; keep the best. */
    protected function dedupe(array $subjects)
    {
        $byKey = [];
        foreach ($subjects as $s) {
            $key = strtoupper($s['subject_code'] !== '' ? $s['subject_code'] : $s['subject_name']);
            if (!isset($byKey[$key])) {
                $byKey[$key] = $s;
                continue;
            }
            // A pass always beats a fail; otherwise the higher total wins.
            $old = $byKey[$key];
            if ($old['is_backlog'] === 1 && $s['is_backlog'] === 0) {
                $byKey[$key] = $s;
            } elseif ($old['is_backlog'] === $s['is_backlog'] && (int) $s['total'] > (int) $old['total']) {
                $byKey[$key] = $s;
            }
        }
        return array_values($byKey);
    }

    protected function firstNumeric($node, array $names)
    {
        foreach ($node as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $k = strtolower(preg_replace('/[^a-z]/i', '', (string) $key));
            if (in_array($k, $names, true) && is_numeric($value)) {
                return (float) $value;
            }
        }
        return null;
    }

    protected function firstString($node, array $names)
    {
        foreach ($node as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $k = strtolower(preg_replace('/[^a-z]/i', '', (string) $key));
            if (in_array($k, $names, true) && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }
        return '';
    }
}
