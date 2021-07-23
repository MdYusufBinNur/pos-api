<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->string('invoice');
            $table->double('total');
            $table->double('due')->nullable();
            $table->double('paid');
            $table->double('total_vat')->default(0);
            $table->double('total_discount')->default(0);
            $table->string('status')->default('pending');
            $table->string('delivery_method')->default('regular'); // Could be online delivery
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
        Schema::dropIfExists('product_sales');
    }
}
