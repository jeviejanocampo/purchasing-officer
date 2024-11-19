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
        'supplier_id',
        'input_budget',
        'balance',
        'product_to_buy',
    ];
}
