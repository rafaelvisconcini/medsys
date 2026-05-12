<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY perfil ENUM('proprietario','admin','profissional','recepcionista','financeiro') NOT NULL DEFAULT 'recepcionista'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY perfil ENUM('admin','profissional','recepcionista','financeiro') NOT NULL DEFAULT 'recepcionista'");
    }
};
