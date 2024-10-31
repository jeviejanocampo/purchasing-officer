<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $table = 'budget'; // Specify the table name if it's not plural

    protected $fillable = [
        'reference_code',
        'input_budget',
        'product_to_buy', // Add product_to_buy to the fillable array
    ];
}
