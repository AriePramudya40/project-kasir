<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Izinkan kolom ini diisi secara massal
    protected $fillable = [
    'kode', 
    'nama', 
    'harga', 
    'stok',
    'image_url' // <--- Tambahkan ini
    ];
}