<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory'; // Specify the table name

    protected $fillable = [
        'budget_identifier',
        'product_id', // Add product_id to fillable
        'product_name',
        'unit_cost',
        'pieces_per_set',
        'stocks_per_set',
        'exp_date',
    ];
}
