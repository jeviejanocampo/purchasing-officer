<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetBalance extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'budget_balance';

    // Specify the fillable properties
    protected $fillable = [
        'remaining_total_balance',
    ];

    // Define any relationships here if needed
}
