<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('book_department', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('department_id');
            $table->index('book_id');
            $table->index('department_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_department');
    }
};
