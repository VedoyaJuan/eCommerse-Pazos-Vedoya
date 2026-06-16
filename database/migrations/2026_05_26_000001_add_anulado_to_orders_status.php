<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check");
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'anulado'))");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check");
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled'))");
        }
    }
};
