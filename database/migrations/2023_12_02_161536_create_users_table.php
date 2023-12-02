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
            $table->id();
            $table->string('full_name');
            $table->string('phone_number')->unique();
            $table->unsignedBigInteger('account_number'); //foreign key column
            $table->timestamp('phone_number_verified_at')->nullable();
            $table->string('pin');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('account_number')->references('account_number')->on('accounts'); //foreign key constraint
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
