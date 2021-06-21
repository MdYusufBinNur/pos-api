<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSaleLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_sale_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_sale_id');
            $table->unsignedBigInteger('product_id');
            $table->double('price');
            $table->double('discount')->default(0);
            $table->double('vat')->default(0);
            $table->double('quantity');
            $table->timestamps();

            $table->foreign('product_sale_id')
                ->references('id')
                ->on('product_sales')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sale_logs');
    }
}
