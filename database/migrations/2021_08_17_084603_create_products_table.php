<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('brand_id');
            $table->integer('category_id');
            $table->integer('product_type_id');
            $table->string('images');
            $table->float('price');
            $table->boolean('status');
            $table->string('short_description');
            $table->longText('description');
            $table->longText('specification');
            $table->integer('quality');
            $table->integer('discount');
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
