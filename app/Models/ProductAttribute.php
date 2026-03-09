<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    public $table = 'product_attributes';
    public $fillable = ['key', 'value', 'product_id'];
    public $timestamps = false;


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
