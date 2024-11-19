<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Define the table name if it differs from the class name
    protected $table = 'products';

    // Define the primary key
    protected $primaryKey = 'product_id';

    // Specify the fillable columns
    protected $fillable = [
        'product_details_id',
        'product_name',
        'product_price',
        'product_description',
        'product_stocks',
        'product_expiry_date',
        'product_image',
        'created_at',
        'product_status'
    ];

    // Disable timestamps if the table does not use 'created_at' and 'updated_at'
    public $timestamps = false;

    // Define the relationship with ProductDetails
    public function details()
    {
        return $this->belongsTo(ProductDetails::class, 'product_details_id', 'product_details_id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'product_id', 'product_id');
        }
    
}
