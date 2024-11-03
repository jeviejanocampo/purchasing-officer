<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id(); // This creates an auto-incrementing 'id' column
            $table->text('log_data'); // This creates a 'log_data' column to store log entries
            $table->timestamps(); // Optional: This adds created_at and updated_at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
