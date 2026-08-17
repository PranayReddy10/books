<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Posts can now be words only, and a photo post can carry several images.
 *
 * Three shape changes:
 *  - media_type was ENUM('photo','video'); 'text' has to be storable.
 *  - file_url was NOT NULL; a text post has no file.
 *  - extra_images holds the photos after the first, as a JSON array.
 *
 * The first two are raw ALTERs on purpose: ->change() needs doctrine/dbal,
 * which this project does not require.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('media_posts')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            // varchar rather than a wider enum, so a future type needs no migration.
            DB::statement("ALTER TABLE media_posts MODIFY media_type VARCHAR(20) NOT NULL DEFAULT 'photo'");
            DB::statement("ALTER TABLE media_posts MODIFY file_url VARCHAR(255) NULL");
        }

        if (!Schema::hasColumn('media_posts', 'extra_images')) {
            Schema::table('media_posts', function (Blueprint $table) {
                // JSON array of the additional photo URLs; the cover stays in file_url.
                $table->text('extra_images')->nullable()->after('thumb_url');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable('media_posts')) {
            return;
        }

        if (Schema::hasColumn('media_posts', 'extra_images')) {
            Schema::table('media_posts', function (Blueprint $table) {
                $table->dropColumn('extra_images');
            });
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            // Text posts would violate both constraints, so they go first.
            DB::table('media_posts')->where('media_type', 'text')->delete();
            DB::statement("ALTER TABLE media_posts MODIFY file_url VARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE media_posts MODIFY media_type ENUM('photo','video') NOT NULL DEFAULT 'photo'");
        }
    }
};
