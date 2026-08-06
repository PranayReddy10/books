<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'content_type')) {
                // 'book' (default) or 'video'
                $table->string('content_type', 20)->default('book')->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'content_type')) {
                $table->dropColumn('content_type');
            }
        });
    }
};
