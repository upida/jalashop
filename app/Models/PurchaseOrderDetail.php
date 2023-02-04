<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'sku',
        'quantity'
    ];

    public function purchase()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_id', 'number');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }
}
