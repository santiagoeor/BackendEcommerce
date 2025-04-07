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
            $table->id('pk_product');
            $table->char('name_product', 100);
            $table->char('description_product', 200);
            $table->double('price', 22, 0);
            $table->unsignedBigInteger('stock')->default(0);
            $table->unsignedBigInteger('amount')->default(0);
            $table->boolean('product_status');

            $table->unsignedBigInteger('fk_category');
            $table->foreign('fk_category')->references('pk_category')->on('categories');

            $table->unsignedBigInteger('fk_brand');
            $table->foreign('fk_brand')->references('pk_brand')->on('brands');

            $table->unsignedBigInteger('fk_image');
            $table->foreign('fk_image')->references('pk_image_product')->on('product_images');

            $table->unsignedBigInteger('fk_variant');
            $table->foreign('fk_variant')->references('pk_variant')->on('product_variants');

            $table->timestamps();
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