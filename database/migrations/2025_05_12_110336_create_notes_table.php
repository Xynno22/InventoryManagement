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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->double('total_price');
            $table->string('description');

            // foreign id for user_id, voucher_code
            $table->foreignId('company_id')->references('id')->on('companies')->onDelete('cascade');
            // $table->string('voucher_code');
            // $table->foreign('voucher_code')->references('voucher_code')->on('transactions')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
