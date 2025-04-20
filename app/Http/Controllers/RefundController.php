<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Refund;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    //daftar refund (Bisnis owner dan admin)
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        if ($perPage === 'all') {
            return response()->json(Refund::with(['transaction.user'])->get(), 200);
        }
        return response()->json(Refund::with('transaction.user')->paginate($perPage), 200);
    }

    //melakukan refund (kasir, admin, bisnis owner) hanya untuk transaksi hari yang sama
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:transaction_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);
        $transaction = Transaction::findOrFail($request->transaction_id);

        // Pastikan refund hanya bisa dilakukan pada hari yang sama
        if ($transaction->created_at->format('Y-m-d') !== now()->format('Y-m-d')) {
            return response()->json(['message' => 'Refund hanya dapat dilakukan pada hari yang sama, tidak bisa lain hari!.'], 403);
        }

        foreach ($request->item as $item) {
            $transactionItem = TransactionItem::findOrFail($item['id']);
            if ($item['quantity']->$transactionItem->quantity) {
                return response()->json(['message' => 'Jumlah refund melebihi yang dibeli!.'], 400);
            }
            //kembalikan ke stok barang
            $inventory = Inventory::findOrFail($transactionItem->inventory_id);
            $inventory->increment('quantity', $item['quantity']);

            //simpan data refund
            Refund::create([
                'transaction_id' => $transaction->id,
                'user_id' => Auth::id(),
                'quantity' => $item['quantity'],
                'reason' => $request->reason,
                'status' => 'Approved'
            ]);
        }

        return response()->json(['message' => 'Refund berhasil diproses dan stok sudah dikembalikan!.'], 201);
    }
}
