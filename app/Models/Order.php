<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'admin',
        'customer'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer', 'id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'order_id', 'id');
    }
}
