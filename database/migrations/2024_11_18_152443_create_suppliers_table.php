<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('supplier_name'); // Supplier name
            $table->string('contact_person'); // Contact person
            $table->string('phone_number'); // Phone number
            $table->string('email')->nullable(); // Email address (optional)
            $table->text('address'); // Address
            $table->string('product_type'); // Product type
            $table->enum('status', ['Active', 'Inactive'])->default('Active'); // Status
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
