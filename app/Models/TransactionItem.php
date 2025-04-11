<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;
    protected $fllable = ['transaction_id', 'inventory_id', 'quantity', 'discount', 'price'];
    // Relasi: TransactionItem milik satu transaksi
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    // Relasi: TransactionItem terkait dengan satu barang (Inventory)
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
