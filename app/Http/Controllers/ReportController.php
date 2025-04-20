<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Refund;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth::sanctum');
    }

    //laporan penjualan dalam rentang tanggal
    public function salesReport(Request $request)
    {
        if (!in_array(Auth::user()->role, ['business_owner', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $startDate = $request->query('start_date', now()->startOfMonth());
        $endDate = $request->query('end_date', now());

        $sales = Transaction::whereBetween('created_at', [$startDate, $endDate])->select(
            DB::raw("DATE(create_at) as date"),
            DB::raw("SUM(total_price) as total_sales"),
            DB::raw("COUNT(id) as total_transactions")
        )->groupBy('date')->orderBy('date', 'asc')->get();

        return response()->json([
            'total_trasnasctions' => $sales->sum('total_transactions'),
            'total_income' => $sales->sum('total_sales'),
            'sales' => $sales
        ], 200);
    }

    //laporan retur dalam rentang tanggal
    public function refundReport(Request $request)
    {
        if (!in_array(Auth::user()->role, ['business_owner', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $startDate = $request->query('start_date', now()->startOfMonth());
        $endDate = $request->query('end_date', now());

        $refund = Refund::whereBetween('created_at', [$startDate, $endDate])->select(
            DB::raw("DATE(create_at) as date"),
            DB::raw("SUM(quantity) as total_refunded_items"),
            DB::raw("COUNT(id) as total_refunds")
        )->groupBy('date')->orderBy('date', 'asc')->get();

        return response()->json([
            'total_refunds' => $refund->sum('total_refund'),
            'total_refunded_items' => $refund->sum('total_refund'),
            'refund' => $refund
        ], 200);
    }

    //laporan stok barang
    public function inventoryReport()
    {
        if (!in_array(Auth::user()->role, ['business_owner', 'admin', 'inventory'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json(Inventory::all(), 200);
    }
}
