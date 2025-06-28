<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    //untuk melihat daftar barang
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        $query = Inventory::orderBy('created_at', 'desc');

        if ($perPage === 'all') {
            return response()->json($query->get(), 200);
        }
        return response()->json($query->paginate($perPage), 200);
    }

    //tambah barang khusus Admin dan Inventory manager
    public function store(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'inventory'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1', // Menggunakan quantity
            'vendor' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'name' => 'required|string|max:255',
        ]);
        $item = Inventory::create($request->only(['name', 'quantity', 'vendor', 'price']));
        return response()->json($item, 201);
    }

    //update barang
    public function update(Request $request, $id)
    {
        if (!in_array(Auth::user()->role, ['admin', 'inventory'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $item = Inventory::fndOrFail($id);
        // Jika stok berkurang, wajib memberikan alasan
        if ($request->has('quantity') && $request->quantity < $item->quantity) {
            $request->validate(['reason' => 'required|string|max:255']);
        }
        $item->update($request->only(['name', 'quantity', 'vendor', 'price']));
        return response()->json($item, 200);
    }

    //menghapus barang (soft delet: status menjadi invalid)
    public function destroy($id)
    {
        if (!in_array(Auth::user()->role, ['admin', 'inventory'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $item = Inventory::fndOrFail($id);
        $item->update(['status' => 'invalid']);
        return response()->json(['message' => 'Item status set to invalid'], 200);
    }
}
