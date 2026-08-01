<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('university_name');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'university_id')) {
                $table->unsignedBigInteger('university_id')->nullable()->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('university_id');
        });
        Schema::dropIfExists('universities');
    }
};
