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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_number'); //foreign key column
            $table->string('transaction_type');
            $table->string('amount');
            $table->string('description');
            $table->string('reference');
            $table->string('method');
            $table->string('fee');
            $table->decimal('running_balance', 10, 2);
            $table->string('status');
            $table->timestamps();

            $table->foreign('account_number')->references('account_number')->on('accounts'); //foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
