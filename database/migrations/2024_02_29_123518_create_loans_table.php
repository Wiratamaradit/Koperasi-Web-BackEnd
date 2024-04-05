<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id()->primary;
            $table->unsignedBigInteger('userId');
            $table->string('code');
            $table->double('nominal');
            $table->double('interest');
            $table->integer('tenor');
            $table->date('date');
            $table->string('description');
            $table->string('loanStatus');
            $table->string('validationLoanStatus');
            $table->string('status');

            $table->foreign('userId')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
