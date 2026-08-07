<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'registered_via')) {
                $table->string('registered_via', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_via')) {
                $table->string('last_login_via', 20)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['registered_via', 'last_login_at', 'last_login_via']);
        });
    }
};
