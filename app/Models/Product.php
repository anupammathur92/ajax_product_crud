<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
 
class Product extends Model
{
    protected $fillable = [
        'id', 'product_name', 'product_price', 'description'
    ];
}