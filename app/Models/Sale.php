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
        'bayar',        // <--- TAMBAHKAN INI (WAJIB)
        'approved_by',
        'grand_total',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
    
    // Tambahan: Relasi ke User agar nama kasir bisa muncul di struk
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}