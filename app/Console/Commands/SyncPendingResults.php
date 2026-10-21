<?php

namespace App\Console\Commands;

use App\Services\StudentResults;
use Illuminate\Console\Command;

/**
 * Retry students whose results the JNTU feed was still fetching when they
 * signed up (it answers "queued" for a hall ticket it has not seen yet).
 */
class SyncPendingResults extends Command
{
    protected $signature = 'results:sync-pending';

    protected $description = 'Import JNTU results for students still waiting on the feed';

    public function handle()
    {
        $done = (new StudentResults())->syncPending();
        foreach ($done as $userId => $state) {
            $this->line("user $userId: $state");
        }
        $this->info(count($done) . ' checked, ' . count(StudentResults::pending()) . ' still pending');
        return 0;
    }
}
