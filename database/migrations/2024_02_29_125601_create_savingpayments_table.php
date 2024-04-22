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
        Schema::create('savingpayments', function (Blueprint $table) {
            $table->id()->primary;
            $table->unsignedBigInteger('saveId');
            $table->double('nominal');
            $table->double('payment');
            $table->string('paymentMethod');
            $table->date('date');
            $table->string('status');

            $table->foreign('saveId')->references('id')->on('savings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savingpayments');
    }
};
