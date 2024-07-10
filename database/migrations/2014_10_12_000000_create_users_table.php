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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->primary;
            $table->string('codeUser');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->integer('nik');
            $table->string('position');
            $table->string('employeeStatus');
            $table->string('branchName');
            $table->string('managerName');
            $table->string('joinDate');
            $table->string('address');
            $table->integer('phoneNumber');
            $table->string('bankName');
            $table->string('accountNumber');
            $table->string('validationStatus');
            $table->string('registrationStatus');
            $table->string('status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
