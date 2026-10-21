<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The university a category is for ("JNTUH B.TECH BOOKS" -> JNTU Hyderabad).
 * Null means every university (Text Books, Government). Students see only
 * their own university's categories, and the books in them.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('categories', 'university_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedInteger('university_id')->nullable()->after('id');
                $table->index('university_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('categories', 'university_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropIndex(['university_id']);
                $table->dropColumn('university_id');
            });
        }
    }
};
