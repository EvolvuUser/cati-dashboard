<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function mobileCalls(Request $request)
    {
        $filters = $request->only(['campaign_id', 'user', 'status_name', 'date_from', 'date_to', 'search']);

        $baseQuery = fn() => DB::table('mobile_calls as mc')
                ->when(!empty($filters['campaign_id']), fn($q) => $q->where('mc.campaign_id', $filters['campaign_id']))
                ->when(!empty($filters['user']),        fn($q) => $q->where('mc.user', $filters['user']))
                ->when(!empty($filters['status_name']), fn($q) => $q->where('mc.status_name', $filters['status_name']))
                ->when(!empty($filters['date_from']),   fn($q) => $q->whereDate('mc.call_date', '>=', $filters['date_from']))
                ->when(!empty($filters['date_to']),     fn($q) => $q->whereDate('mc.call_date', '<=', $filters['date_to']))
                ->when(!empty($filters['search']),      fn($q) => $q->where(function ($q2) use ($filters) {
                    $q2->where('mc.db_no',       'like', '%' . $filters['search'] . '%')
                    ->orWhere('mc.user',       'like', '%' . $filters['search'] . '%')
                    ->orWhere('mc.campaign_id','like', '%' . $filters['search'] . '%')
                    ->orWhere('pi.job_name_by_research', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('pi.client_name',          'like', '%' . $filters['search'] . '%');
                }));

        $calls = $baseQuery()
            ->select(
                'mc.*',
            )
            ->orderByDesc('mc.call_date')
            ->paginate(50)
            ->withQueryString();

        $users     = DB::table('mobile_calls')->distinct()->orderBy('user')->pluck('user');
        $statuses  = DB::table('mobile_calls')->distinct()->orderBy('status_name')->pluck('status_name');

        $stats = $baseQuery()
            ->selectRaw('COUNT(*) as total, SUM(mc.length_in_sec) as total_sec, AVG(mc.length_in_sec) as avg_sec')
            ->first();

        return view('reports.mobile-calls', compact('calls', 'filters', 'users', 'statuses', 'stats'));
    }
}