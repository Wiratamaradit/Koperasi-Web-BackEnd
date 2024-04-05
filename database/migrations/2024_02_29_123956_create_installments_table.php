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
        Schema::create('installments', function (Blueprint $table) {
            $table->id()->primary;
            $table->unsignedBigInteger('loanId');
            $table->double('nominalPayment');
            $table->double('expense');
            $table->date('date');
            $table->string('paymentStatus');
            $table->string('validationStatus');
            $table->string('description');
            $table->string('statusInstallment');

            $table->foreign('loanId')->references('id')->on('loans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
