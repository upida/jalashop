<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'sku';

    protected $fillable = [
        'admin',
        'title',
        'price',
        'quantity'
    ];

    /**
     * Get the admin for the product.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin', 'id');
    }
}
