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
        Schema::create('savings', function (Blueprint $table) {
            $table->id()->primary;
            $table->unsignedBigInteger('userId');
            $table->string('code');
            $table->double('nominalPerMonth');
            $table->double('interest');
            $table->date('date');
            $table->string('paymentMethod');
            $table->string('timePeriod');
            $table->string('status');

            $table->foreign('userId')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
