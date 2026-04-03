<?php
namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{

  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      "title" => "sometimes|string|min:3|max:50",
      "description" => "sometimes|string|min:10|max:1000",
      "cover_image" => "sometimes|image|mimes:jpg,jpeg,png,webp|max:2048",
      "price" => "sometimes|numeric|min:0",
      "quantity" => "sometimes|integer|min:0",
    ];
  }


}
