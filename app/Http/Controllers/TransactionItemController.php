<?php

namespace App\Http\Controllers;

use App\Models\TransactionItem;
use Illuminate\Http\Request;

class TransactionItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    //untuk melihat daftar barang dalam transaksi tertentu
    public function index($transaction_id)
    {
        $item = TransactionItem::where('transaction_id', $transaction_id)->with('inventory')->get();
        if ($item->isEmpty()) {
            return response()->json(['message' => 'Tidak ada item yang ditemukan dalam transaksi ini.'], 404);
        }
        return response()->json($item, 200);
    }
}
