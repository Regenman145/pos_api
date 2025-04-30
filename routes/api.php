<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionItemController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;

// AUTHENTICATION ROUTES
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::middleware('auth:sanctum')->post('/logout', 'logout');
});
// USER MANAGEMENT ROUTES (Hanya Business Owner)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']); // Lihat daftar user (dengan pagination)
    Route::post('/users', [UserController::class, 'store']); // Tambah user
    Route::put('/users/{id}', [UserController::class, 'update']); // Edit user
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Hapus user
});
// INVENTORY ROUTES (Hanya Admin & Inventory Manager)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index']); // Lihat daftar barang (dengan pagination)
    Route::post('/inventory', [InventoryController::class, 'store']); // Tambah barang
    Route::put('/inventory/{id}', [InventoryController::class, 'update']); // Edit barang
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy']); // Hapus barang (soft delete)
});

// TRANSACTION ROUTES (Hanya Kasir, Admin, Business Owner)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index']); // Lihat daftar transaksi (dengan pagination)
    Route::get('/transactions/{id}', [TransactionController::class, 'show']); // Lihat detail transaksi
    Route::post('/transactions', [TransactionController::class, 'store']); // Buat transaksi baru
});
// TRANSACTION ITEM ROUTES (Detail Barang dalam Transaksi)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/transactions/{transaction_id}/items', [TransactionItemController::class, 'index']); // Lihat detail transaksi (dengan pagination)
});
// REFUND ROUTES (Hanya Kasir, Admin, Business Owner)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/refunds', [RefundController::class, 'index']); // Lihat daftar refund (Admin & Owner, dengan pagination)
    Route::post('/refunds', [RefundController::class, 'store']); // Refund transaksi
});

// REPORT ROUTES (Hanya Business Owner & Admin)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/report/sales', [ReportController::class, 'salesReport']); // Laporan penjualan
    Route::get('/report/inventory', [ReportController::class, 'inventoryReport']); // Laporan stok barang
});
// ACTIVITY LOG ROUTES (Hanya Business Owner & Admin)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index']); // Lihat log aktivitas (dengan pagination)
});
