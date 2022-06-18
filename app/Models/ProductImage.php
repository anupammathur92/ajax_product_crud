<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
 
class ProductImage extends Model
{
    protected $fillable = [
        'id', 'product_id', 'image_name'
    ];
}