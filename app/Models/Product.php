<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = []; 

    public static function productCode()
    {
        return self::doesntExist()
         ? 1001
         : self::max('code') + 1;
    }


    
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }


    public function createProductImages($request){

        foreach ($request->images as $image) {
            $this->images()->create([
             'filename' => $image,
         ]);
        }
        
    }

}
