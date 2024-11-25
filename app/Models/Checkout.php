<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $table = 'checkout'; // Specify the table name if it's different

    protected $primaryKey = 'checkout_id'; // Specify the primary key if it's different

    // Define the relationship with the CheckoutDetail model
    public function checkoutDetails()
    {
        return $this->hasMany(CheckoutDetail::class, 'checkout_id');
    }

    // You can add any other fillable or guarded properties here as needed
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'total_amount',
        'created_at',
        'checkout_status'
    ];
}
