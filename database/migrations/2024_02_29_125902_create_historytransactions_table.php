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
        Schema::create('historytransactions', function (Blueprint $table) {
            $table->id()->primary;
            $table->unsignedBigInteger('savepayId');
            $table->unsignedBigInteger('loantransId');
            $table->double('nominal');
            $table->string('paymentMethod');
            $table->string('description');
            $table->string('status');

            $table->foreign('savepayId')->references('id')->on('savingpayments');
            $table->foreign('loantransId')->references('id')->on('savings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historytransactions');
    }
};
