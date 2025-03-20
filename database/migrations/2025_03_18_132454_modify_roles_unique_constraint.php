<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            // Hapus unique constraint lama
            $table->dropUnique(['name', 'guard_name']);

            // Tambahkan unique constraint baru dengan company_id
            $table->unique(['name', 'guard_name', 'company_id']);
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            // Kembalikan ke unique constraint lama jika rollback
            $table->dropUnique(['name', 'guard_name', 'company_id']);
            $table->unique(['name', 'guard_name']);
        });
    }
};

