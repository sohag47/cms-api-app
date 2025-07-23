<?php

use App\Enums\StatusEnums;
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
            $table->string('model')->nullable();
            $table->string('part_no')->nullable();
            $table->string('unit');
            $table->decimal('unit_price', 12, 3)->default(0);
            $table->tinyText('status')->default(StatusEnums::ACTIVE);
            $table->longText('specifications')->nullable();

            // for foreign key relationships, you can add them later as needed
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->foreignId('origin_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->foreignId('manufacture_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->foreignId('product_type_id')->nullable()->constrained('product_types')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
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
