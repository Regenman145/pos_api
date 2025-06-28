<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'quantity', 'vendor', 'price', 'status'];
    // Relasi: Inventory memiliki banyak transaction items
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
