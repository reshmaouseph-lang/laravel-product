<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes; 
    protected $fillable = [
        'product_name',
        'product_price',
        'product_description',
        'product_images'
    ];

    protected $casts = [
        'product_images' => 'array',
    ];
}
