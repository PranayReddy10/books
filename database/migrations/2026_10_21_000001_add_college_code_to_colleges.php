<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The university's code for a college (JNTUH: the two characters after the
 * year in a hall ticket, e.g. "R0" in 22R01A0501). The hall ticket lookup uses
 * it to fill in the student's college.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('colleges', 'college_code')) {
            Schema::table('colleges', function (Blueprint $table) {
                $table->string('college_code', 10)->nullable()->after('college_name');
                $table->index('college_code');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('colleges', 'college_code')) {
            Schema::table('colleges', function (Blueprint $table) {
                $table->dropIndex(['college_code']);
                $table->dropColumn('college_code');
            });
        }
    }
};
