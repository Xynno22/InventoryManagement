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
            $table->string('customer_name');
            $table->double('total_price');
            $table->timestamp('date');
            $table->string('voucher_code')->unique();

            // payment, status, type, company
            $table->foreignId('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreignId('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->foreignId('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreignId('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->timestamps();
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
