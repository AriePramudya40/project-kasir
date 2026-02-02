<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal', 
        'tipe', 
        'product_id', 
        'qty', 
        'alasan', 
        'kembali_ke_stok'
    ];

    // Relasi: Satu Penjualan punya banyak Barang
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}