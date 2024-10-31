<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpDateToInventoryTable extends Migration
{
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->date('exp_date')->nullable(); // Add the exp_date column
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('exp_date'); // Remove the exp_date column if needed
        });
    }
}
