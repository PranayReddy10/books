<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Coins: students earn them when their uploaded books get read, and redeem them
 * for shop gift cards.
 *
 * The ledger is the source of truth — users.coin_balance is a running total kept
 * alongside it so a balance never costs a SUM over every row. book_read_credits
 * is what stops a reader farming the same book: one credit per reader per book,
 * enforced by a unique index rather than by a check that could race.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('coin_transactions')) {
            Schema::create('coin_transactions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');            // who the coins belong to
                $table->string('type', 20);                    // read | upload | redeem | adjust
                $table->integer('coins');                      // signed: earnings +, redemptions -
                $table->unsignedInteger('book_id')->nullable();
                $table->unsignedInteger('reader_id')->nullable();
                $table->unsignedInteger('redemption_id')->nullable();
                $table->string('note', 255)->nullable();
                $table->timestamps();

                $table->index('user_id', 'idx_coin_user');
                $table->index('book_id', 'idx_coin_book');
                $table->index('type', 'idx_coin_type');
            });
        }

        if (!Schema::hasTable('book_read_credits')) {
            Schema::create('book_read_credits', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('book_id');
                $table->unsignedInteger('reader_id');
                $table->unsignedInteger('uploader_id');
                $table->integer('coins')->default(0);
                $table->timestamps();

                // One credit per reader per book — the anti-farming rule.
                $table->unique(['book_id', 'reader_id'], 'uq_book_reader');
                $table->index('uploader_id', 'idx_credit_uploader');
            });
        }

        if (!Schema::hasTable('coin_redemptions')) {
            Schema::create('coin_redemptions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->integer('coins');                       // spent
                $table->decimal('amount', 10, 2);              // money value at the time
                $table->string('code', 40)->nullable();        // the gift card / coupon code
                $table->string('status', 20)->default('pending'); // pending | issued | failed | cancelled
                $table->string('fail_reason', 255)->nullable();
                $table->unsignedInteger('woo_coupon_id')->nullable();
                $table->timestamp('issued_at')->nullable();
                $table->timestamps();

                $table->unique('code', 'uq_redeem_code');
                $table->index('user_id', 'idx_redeem_user');
                $table->index('status', 'idx_redeem_status');
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'coin_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('coin_balance')->default(0)->after('rollnumber');
            });
        }

        // Rates live in the single settings row, like every other admin setting.
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'coins_enabled')) {
                    $table->tinyInteger('coins_enabled')->default(1);
                }
                if (!Schema::hasColumn('settings', 'coins_per_read')) {
                    $table->integer('coins_per_read')->default(1);
                }
                if (!Schema::hasColumn('settings', 'coins_per_upload')) {
                    $table->integer('coins_per_upload')->default(10);
                }
                if (!Schema::hasColumn('settings', 'coin_value')) {
                    // Money one coin is worth, in the shop's currency.
                    $table->decimal('coin_value', 8, 4)->default(0.1000);
                }
                if (!Schema::hasColumn('settings', 'coins_min_redeem')) {
                    $table->integer('coins_min_redeem')->default(500);
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('coin_transactions');
        Schema::dropIfExists('book_read_credits');
        Schema::dropIfExists('coin_redemptions');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'coin_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('coin_balance');
            });
        }
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                foreach (['coins_enabled', 'coins_per_read', 'coins_per_upload', 'coin_value', 'coins_min_redeem'] as $col) {
                    if (Schema::hasColumn('settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
