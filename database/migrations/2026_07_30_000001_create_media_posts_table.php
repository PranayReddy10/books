<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('media_posts')) {
            Schema::create('media_posts', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->nullable();   // uploader (null = admin/system)
                $table->enum('media_type', ['photo', 'video'])->default('photo');
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('file_url');                       // full CDN url (Spaces)
                $table->string('thumb_url')->nullable();          // poster/thumbnail for videos
                $table->string('link_url')->nullable();           // optional URL shown with the post
                $table->tinyInteger('is_admin_upload')->default(0);
                // per-post feature toggles (like book details)
                $table->tinyInteger('show_views')->default(1);
                $table->tinyInteger('allow_likes')->default(1);
                $table->tinyInteger('allow_comments')->default(1);
                // moderation — varchar to match the live `books.upload_status` convention
                $table->string('upload_status', 20)->default('pending');
                $table->string('reject_reason', 500)->nullable();
                $table->tinyInteger('status')->default(0);        // 1 = live in feed
                $table->unsignedInteger('view_count')->default(0);
                $table->timestamps();

                $table->index(['status', 'upload_status']);
                $table->index('user_id');
                $table->index('media_type');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('media_posts');
    }
};
