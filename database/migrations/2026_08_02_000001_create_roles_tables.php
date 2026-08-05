<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('description')->nullable();
                $table->tinyInteger('is_system')->default(0); // seeded roles (protected)
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('role_id');
                $table->string('permission', 100); // e.g. "books.edit"
                $table->timestamps();

                $table->unique(['role_id', 'permission']);
                $table->index('role_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedInteger('role_id')->nullable()->after('usertype');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropColumn('role_id');
            }
        });
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
    }
};
