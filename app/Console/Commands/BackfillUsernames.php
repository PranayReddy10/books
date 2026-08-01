<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class BackfillUsernames extends Command
{
    protected $signature = 'users:backfill-usernames';
    protected $description = 'Generate usernames for any users that do not have one yet';

    public function handle()
    {
        $users = User::whereNull('username')->orWhere('username', '')->get();
        $count = 0;

        foreach ($users as $user) {
            $user->username = generate_username($user->name, $user->email);
            $user->save();
            $count++;
        }

        $this->info("Backfilled usernames for {$count} user(s).");
        return 0;
    }
}
