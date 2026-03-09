<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class Category extends Model
{
  protected $table = 'categories';

  protected $fillable = [
    'title'
  ];

  public static function booted()
  {
    static::creating(function ($category) {
      $category->slug = Str::slug($category->title);
    });

    static::updating(function ($category) {
      $category->slug = Str::slug($category->title);
    });
  }

  public $timestamps = false;


  public function sub_categories(){
    return $this->hasMany(SubCategory::class);
  }

}


