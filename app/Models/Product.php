<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $tabel = 'products';

    protected $fillable = [
        'restaurant_id',
        'name',
        'price',
        'description',
        'image',
        'created_at'
    ];

    protected $hidden = [
        'pivot',
    ];

    public function orders(){
        return $this->belongsToMany(Order::class, 'orders_products', 'product_id', 'order_id');
    }

    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }
}
