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
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string('cheque_number')->unique();
            $table->unsignedBigInteger('account_number');
            $table->string('payee_name');
            $table->decimal('amount', 10, 2);
            $table->enum('cheque_status', ['issued', 'cashed', 'void']);
            $table->date('date_issued');
            $table->date('date_cashed')->nullable();
            $table->enum('authorization_status', ['authorized', 'unauthorized']);
            $table->boolean('stop_payment_flag')->default(false);
            $table->string('issuing_branch');
            $table->text('memo')->nullable();
            $table->timestamps();

            $table->foreign('account_number')->references('account_number')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
