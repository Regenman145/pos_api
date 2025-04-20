<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sacntum');
    }

    //melihat daftar semua user khusus bisnis owner
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        if ($perPage === 'all') {
            return response()->json(User::where('role', '!=', 'business_owner')->get(), 200);
        }
        return response()->json(User::where('role', '!=', 'business_owner')->paginate($perPage), 200);
    }

    //menambah user baru khusus bisnis owner
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'business_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,inventory,cashier'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);
        return response()->json($user, 201);
    }

    //mengupdate user khusus bisnis owner
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'business_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role' => 'nullable|in:admin,inventory,cashier'
        ]);
        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'role' => $request->role ?? $user->role
        ]);
        return response()->json($user, 200);
    }

    //menghapus user khusus bisnis owner
    public function destroy($id)
    {
        if (Auth::user()->role !== 'business_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $user = User::fnd($id);
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus'], 200);
    }
}
