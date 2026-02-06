<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_faktur', 'user_id', 'customer_name', 'payment_method',
        'subtotal', 'diskon', 'approved_by', 'grand_total', 
        'bayar',
        'status'
    ];

    public function items() { return $this->hasMany(SaleItem::class); }
    public function user() { return $this->belongsTo(User::class); } // Pastikan relasi ke User (Kasir) ada
}