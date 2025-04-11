<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;
    protected $fllable = ['transaction_id', 'user_id', 'reason'];
    // Relasi: Refund terkait dengan satu transaksi
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    // Relasi: Refund dilakukan oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
