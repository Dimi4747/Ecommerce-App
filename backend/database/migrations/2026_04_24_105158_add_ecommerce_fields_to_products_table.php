<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->integer('stock_quantity')->default(0)->after('currency_code');
            $table->decimal('weight', 8, 2)->nullable()->after('stock_quantity');
            $table->string('dimensions')->nullable()->after('weight');
            $table->string('image')->nullable()->after('dimensions');
            $table->boolean('is_featured')->default(false)->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'stock_quantity', 'weight', 'dimensions', 'image', 'is_featured']);
        });
    }
};
