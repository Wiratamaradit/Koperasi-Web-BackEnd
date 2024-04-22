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
        Schema::create('loantransactions', function (Blueprint $table) {
            $table->id()->primary;
            $table->unsignedBigInteger('installmentId');
            $table->string('fileName');
            $table->binary('receipt');

            $table->foreign('installmentId')->references('id')->on('installments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loantransactions');
    }
};
