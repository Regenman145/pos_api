<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(Auth::user()->role, ['business_owner', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->has('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('action', 'like', '%' . $request->keyword . '%')
                    ->orWhere('module', 'like', '%' . $request->keyword . '%');
            });
        }
        return response()->json($query->paginate(20));
    }
}
