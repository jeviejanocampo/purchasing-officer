<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    protected $table = 'orders';
    protected $fillable = [
        'checkout_id',
        'user_id',
        'payment_method',
        'order_date',
        'order_status',
    ];

    // Disable timestamp handling
    public $timestamps = false;

    // Define the relationship with the Checkout model
    public function checkout()
    {
        return $this->belongsTo(Checkout::class, 'checkout_id', 'checkout_id');
    }
}


