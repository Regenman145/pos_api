<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fllable = ['user_id', 'total_price', 'payment_method'];
    // Relasi: Transaksi milik seorang user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relasi: Transaksi memiliki banyak barang (TransactionItem)
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
    // Relasi: Transaksi bisa memiliki refund
    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
