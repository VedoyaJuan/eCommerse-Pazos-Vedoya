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
        // 1. Create brands table
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 2. Add brand_id column to products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
        });

        // 3. Migrate existing brand names from products to brands
        $products = DB::table('products')->whereNotNull('brand')->get();
        foreach ($products as $product) {
            $brand = DB::table('brands')->where('name', $product->brand)->first();
            if (!$brand) {
                $brandId = DB::table('brands')->insertGetId([
                    'name' => $product->brand,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $brandId = $brand->id;
            }

            DB::table('products')
                ->where('id', $product->id)
                ->update(['brand_id' => $brandId]);
        }

        // 4. Drop the old brand column
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create brand string column
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable();
        });

        // Restore brand name values from brands table
        $products = DB::table('products')->whereNotNull('brand_id')->get();
        foreach ($products as $product) {
            $brand = DB::table('brands')->where('id', $product->brand_id)->first();
            if ($brand) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['brand' => $brand->name]);
            }
        }

        // Drop foreign key and column brand_id
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        // Drop brands table
        Schema::dropIfExists('brands');
    }
};
