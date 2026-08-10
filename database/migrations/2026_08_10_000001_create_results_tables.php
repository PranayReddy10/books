<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Results feature (Phase 1). Committed to the repo for parity, but on the live
 * shared-hosting box the tables are created via the phpMyAdmin SQL patch
 * (sql/results_feature_phase1.sql) — there is no artisan migrate workflow live.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('results')) {
            Schema::create('results', function (Blueprint $table) {
                $table->increments('id');
                $table->string('hall_ticket_no', 20);
                $table->unsignedInteger('user_id')->nullable();
                $table->unsignedInteger('university_id')->nullable();
                $table->string('student_name', 150)->nullable();
                $table->string('regulation', 10)->nullable();
                $table->string('degree', 30)->nullable();
                $table->string('branch', 100)->nullable();
                $table->decimal('current_cgpa', 4, 2)->nullable();
                $table->decimal('total_credits', 6, 1)->nullable();
                $table->integer('backlogs_count')->default(0);
                $table->string('source', 10)->default('manual');
                $table->tinyInteger('verified')->default(0);
                $table->tinyInteger('locked')->default(0);
                $table->tinyInteger('is_public')->default(0);
                $table->string('share_token', 40)->nullable();
                $table->timestamps();

                $table->unique('hall_ticket_no', 'uq_hall_ticket');
                $table->unique('share_token', 'uq_share_token');
                $table->index('user_id', 'idx_user');
                $table->index('university_id', 'idx_university');
                $table->index('verified', 'idx_verified');
            });
        }

        if (!Schema::hasTable('result_semesters')) {
            Schema::create('result_semesters', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('result_id');
                $table->string('sem_code', 10);
                $table->decimal('sgpa', 4, 2)->nullable();
                $table->decimal('credits_earned', 6, 1)->nullable();
                $table->string('exam_month_year', 30)->nullable();
                $table->timestamps();
                $table->index('result_id', 'idx_result');
            });
        }

        if (!Schema::hasTable('result_subjects')) {
            Schema::create('result_subjects', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('result_semester_id');
                $table->string('subject_code', 30)->nullable();
                $table->string('subject_name', 200)->nullable();
                $table->integer('internal')->nullable();
                $table->integer('external')->nullable();
                $table->integer('total')->nullable();
                $table->string('grade', 5)->nullable();
                $table->decimal('credits', 4, 1)->nullable();
                $table->tinyInteger('is_backlog')->default(0);
                $table->timestamps();
                $table->index('result_semester_id', 'idx_semester');
            });
        }

        if (!Schema::hasTable('report_cards')) {
            Schema::create('report_cards', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('result_id');
                $table->string('pdf_url', 500)->nullable();
                $table->tinyInteger('verified_at_generation')->default(0);
                $table->timestamp('generated_at')->nullable();
                $table->timestamps();
                $table->index('result_id', 'idx_result');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('report_cards');
        Schema::dropIfExists('result_subjects');
        Schema::dropIfExists('result_semesters');
        Schema::dropIfExists('results');
    }
};
