<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        //jika user tidak ditemukan atau salah
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau Password salah'], 401);
        }

        //user ditemukan dan password cocok lalu buat token
        $token = $user->createToken('flutter-token')->plainTextToken;

        //response berhasil
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }

    public function register(Request $request)
    {
        //validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        //buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //buat token untuk user baru
        $token = $user->createToken('auth_token')->plainTextToken;

        //response berhasil
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function logout(Request $request)
    {
        //revoke token saat logout
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}
