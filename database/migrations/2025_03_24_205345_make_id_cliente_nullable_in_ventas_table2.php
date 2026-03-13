<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE ventas MODIFY id_cliente BIGINT UNSIGNED NULL');
        } 
        // Para PostgreSQL
        elseif (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE ventas ALTER COLUMN id_cliente DROP NOT NULL');
        }
        // Para SQLite
        elseif (DB::getDriverName() === 'sqlite') {
            Schema::table('ventas', function (Blueprint $table) {
                $table->unsignedBigInteger('id_cliente')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Para MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE ventas MODIFY id_cliente BIGINT UNSIGNED NOT NULL');
        }
        // Para PostgreSQL
        elseif (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE ventas ALTER COLUMN id_cliente SET NOT NULL');
        }
        // Para SQLite
        elseif (DB::getDriverName() === 'sqlite') {
            Schema::table('ventas', function (Blueprint $table) {
                $table->unsignedBigInteger('id_cliente')->nullable(false)->change();
            });
        }
    }
};