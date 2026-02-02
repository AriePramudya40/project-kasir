<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_faktur',
        'user_id',
        'subtotal',
        'diskon',
        'approved_by',
        'grand_total',
        'status'
    ];

    // Relasi: Satu Penjualan punya banyak Barang
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}