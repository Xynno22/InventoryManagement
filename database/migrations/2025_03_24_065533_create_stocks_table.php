<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productID')->constrained('products')->onDelete('cascade');
            $table->double('currentStock');
            $table->double('minimumStock');
            $table->timestamp('lastUpdated')->useCurrent();
            $table->foreignId('companyID')->constrained('companies')->onDelete('cascade');
            $table->double('totalOrder')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};
