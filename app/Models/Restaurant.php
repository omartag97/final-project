<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Restaurant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $tabel = 'restaurants';

    protected $fillable = [
        'store_name',
        'type',
        'first_name',
        'last_name',
        'mobile',
        'email',
        'password',
        'region',
        'description',
        'image',
        'working_hours',
        'delivery_time',
        'min_order',
        'online_tracking',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'pivot',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        return $this->hasMany(
            Product::class,
        );
    }
}
