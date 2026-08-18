<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sign-up now collects the father's name along with the rest of the identity
 * the hall-ticket lookup returns, so it needs somewhere to live.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'father_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('father_name')->nullable()->after('name');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'father_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('father_name');
            });
        }
    }
};
