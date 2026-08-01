<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('media_likes')) {
            Schema::create('media_likes', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('post_id');
                $table->unsignedInteger('user_id');
                $table->timestamps();

                $table->unique(['post_id', 'user_id']);   // one like per user per post
                $table->index('post_id');
            });
        }

        if (!Schema::hasTable('media_comments')) {
            Schema::create('media_comments', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('post_id');
                $table->unsignedInteger('user_id');
                $table->text('comment');
                $table->tinyInteger('status')->default(1);  // 1 = visible, 0 = hidden by admin
                $table->timestamps();

                $table->index('post_id');
                $table->index('user_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('media_likes');
        Schema::dropIfExists('media_comments');
    }
};
