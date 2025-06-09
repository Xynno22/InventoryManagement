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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('categoryID');
            $table->text('detail')->nullable();
            $table->string('image')->nullable();
            $table->string('unit');
            $table->unsignedBigInteger('companyID');
            $table->string('location')->nullable();
            $table->double('purchase_price');
            $table->double('sale_price');
            $table->timestamps();

            // Foreign keys
            $table->foreign('categoryID')->references('id')->on('product_categories')->onDelete('cascade');
            $table->foreign('companyID')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
