<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutDetail extends Model
{
    use HasFactory;

    protected $table = 'checkout_details'; // Specify the table name if needed
    protected $primaryKey = 'checkout_details_id'; // Specify the primary key if needed

    // Define the relationship with the Checkout model
    public function checkout()
    {
        return $this->belongsTo(Checkout::class, 'checkout_id');
    }

    // Define the relationship with the Product model
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Fillable attributes
    protected $fillable = [
        'checkout_id',
        'product_id',
        'cart_id',
        'product_quantity',
        'product_price',
        'product_image',
        'status',
        'inventory_id'  
    ];
}
