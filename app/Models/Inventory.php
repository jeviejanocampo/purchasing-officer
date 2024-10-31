<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    // Specify the table if it doesn't follow Laravel's naming convention
    protected $table = 'inventory';

    // Specify the fillable attributes
    protected $fillable = [
        'budget_identifier',
        'product_name',
        'exp_date',
        'unit_cost',
        'pieces_per_set',
        'stocks_per_set',
    ];

    // Optionally, if you're using timestamps
    public $timestamps = true; // This is true by default
}
