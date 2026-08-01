<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('media_notifications')) {
            Schema::create('media_notifications', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('post_id');
                $table->string('title');
                $table->string('message', 500)->nullable();
                $table->string('image')->nullable();
                // null user_id = broadcast to everyone (shown to all in-app)
                $table->unsignedInteger('user_id')->nullable();
                $table->tinyInteger('is_read')->default(0);
                $table->timestamps();

                $table->index('post_id');
                $table->index('user_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('media_notifications');
    }
};
