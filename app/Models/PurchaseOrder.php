<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'number';

    protected $fillable = [
        'admin'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin', 'number');
    }

    public function detail()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_id', 'number');
    }

}
