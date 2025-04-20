<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    //membuat transaksi baru (kasir, admin, bisnis owener)
    public function store(Request $request)
    {
        if (!in_array(Auth::user()->role, ['cashier', 'admin', 'business_owner'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,qris,bank'
        ]);

        $total_price = 0;
        $transaction_items = [];

        foreach ($request->items as $item) {
            $inventory = Inventory::find($item['id']);
            if (!$inventory) {
                return response()->json(['message' => 'Barang tidak ditemukan.'], 404);
            }

            if ($inventory->quantity < $item['quantity']) {
                return response()->json(['message' => "Not enough stock for {$inventory->name}"], 400);
            }

            $discounted_price = ($inventory->price - ($item['discount'] ?? 0)) * $item['quantity'];
            $total_price += $discounted_price;

            $transaction_items[] = [
                'inventory_id' => $item['id'],
                'quantity' => $item['quantity'],
                'discount' => $item['discount'] ?? 0,
                'price' => $inventory->price
            ];
            $inventory->decrement('quantity', $item['quantity']);
        }
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'total_price' => $total_price,
            'payment_method' => $request->payment_method
        ]);

        foreach ($transaction_items as $item) {
            TransactionItem::create(array_merge($item, ['transaction_id' => $transaction->id]));
        }
        return response()->json($transaction, 201);
    }

    //melihat daftar transaksi
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        if ($perPage === 'all') {
            return response()->json(Transaction::with('user')->get(), 200);
        }
        return response()->json(Transaction::with('user')->paginate($perPage), 200);
    }

    //melihat detail transaksi berdasarkan ID
    public function show($id)
    {
        $transaction = Transaction::with('items.inventory')->find($id);
        if (!$transaction) return response()->json(['message' => 'Transaksi tidak temukan'], 404);

        return response()->json($transaction, 200);
    }
}
