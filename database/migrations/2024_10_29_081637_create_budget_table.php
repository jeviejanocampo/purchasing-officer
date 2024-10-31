<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budget', function (Blueprint $table) {
            $table->id(); // Primary key (auto-increment)
            $table->string('reference_code', 10)->unique(); // Reference code (e.g., AB12)
            $table->decimal('input_budget', 15, 2); // Input budget (up to 15 digits, 2 decimal places)
            $table->decimal('balance', 15, 2); // Balance amount
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
        Schema::dropIfExists('budget');
    }
}
