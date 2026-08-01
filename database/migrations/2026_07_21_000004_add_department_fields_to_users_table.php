<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'university')) {
                $table->string('university')->nullable();
            }
            if (!Schema::hasColumn('users', 'college')) {
                $table->string('college')->nullable();
            }
            if (!Schema::hasColumn('users', 'branch')) {
                $table->string('branch')->nullable();
            }
            if (!Schema::hasColumn('users', 'rollnumber')) {
                $table->string('rollnumber')->nullable();
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department_id']);
        });
    }
};
