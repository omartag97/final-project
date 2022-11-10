<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $tabel = 'odrers';

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'addition_request',
        'delivery_fee',
        'payment_type',
        'status',
        'created_at'
    ];

    protected $hidden = [''];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class , 'orders_products', 'order_id', 'product_id')
        ->withPivot('product_count');
    }
}
