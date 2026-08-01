<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookIdToMediaPostsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('media_posts') && ! Schema::hasColumn('media_posts', 'book_id')) {
            Schema::table('media_posts', function (Blueprint $table) {
                // Optional link from a post to a book. null = no linked book.
                $table->unsignedInteger('book_id')->nullable()->after('link_url');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('media_posts') && Schema::hasColumn('media_posts', 'book_id')) {
            Schema::table('media_posts', function (Blueprint $table) {
                $table->dropColumn('book_id');
            });
        }
    }
}
