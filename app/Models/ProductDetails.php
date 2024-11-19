<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetails extends Model
{
    use HasFactory;

    protected $table = 'product_details';  // Table name if it differs from the model name
    protected $primaryKey = 'product_details_id';

    // Specify the columns that are fillable (optional, adjust as needed)
    protected $fillable = [
        'product_details_id',
        'category_name',
        'product_status'
    ];

    public $timestamps = false;

    // Define the inverse relationship with Product
    public function products()
    {
        return $this->hasMany(Product::class, 'product_details_id', 'product_details_id');
    }
}
