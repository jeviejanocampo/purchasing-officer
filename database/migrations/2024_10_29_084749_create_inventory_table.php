<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->unsignedBigInteger('budget_identifier'); // Foreign key for budget
            $table->string('product_name'); // Name of the product
            $table->decimal('unit_cost', 10, 2); // Unit cost as a decimal
            $table->integer('pieces_per_set'); // Number of pieces per set
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraint
            $table->foreign('budget_identifier')->references('id')->on('budget')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}
