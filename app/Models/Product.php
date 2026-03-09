<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Str;


class Product extends Model
{

  public $table = 'products';

  protected $fillable = [
    'title',
    'description',
    'price',
    'quantity',
    'owner_id',
    'cover_image',
    'is_active'
  ];


  public static function booted()
  {
    static::creating(function ($product) {
      $product->slug = Str::slug($product->title);
    });

    static::updating(function ($product) {
      $product->slug = Str::slug($product->title);
    });

    static::deleting(function($product){
      if($product->cover_image){
        $path = str_replace('/storage/', '', $product->cover_image);
        Storage::disk('public')->delete($path);
      }
      foreach($product->pictures as $pic){
        $path = str_replace('/storage/', '', $pic->picture);
        Storage::disk('public')->delete($path);
      }
    });
  }

  public function pictures()
  {
    return $this->hasMany(ProductPicture::class, 'product_id');
  }

  public function attributes()
  {
    return $this->hasMany(ProductAttribute::class);
  }

  public function sub_categories()
  {
    return $this->belongsToMany(SubCategory::class, ProductSubCategory::class, 'product_id', 'sub_category_id');
  }

}



