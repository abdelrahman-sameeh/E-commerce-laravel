<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ProductSubCategory extends Model {
  public $table = "product_sub_category";

  public $timestamps = false;

  protected $fillable = ['product_id', 'sub_category_id'];
}
