<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'qty',
        'harga_saat_itu',
        'subtotal_line'
    ];

    // Relasi: Item ini milik Produk siapa?
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}