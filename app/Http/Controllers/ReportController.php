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
                ->leftJoin('project_information as pi', function($join) {
                    $join->whereRaw('pi.name_by_cati_team COLLATE utf8mb4_unicode_ci = mc.campaign_id');
                })
                ->leftJoin('tbl_client_names as tcn', function($join) {
                    $join->whereRaw('tcn.client_id COLLATE utf8mb4_unicode_ci = pi.client_name');
                })
                ->leftJoin('tbl_client_industries as tci', function($join) {
                    $join->whereRaw('tci.client_industry_id COLLATE utf8mb4_unicode_ci = pi.client_industry');
                })
                ->leftJoin('tbl_cati_centers as tcc', function($join) {
                    $join->whereRaw("
                        JSON_CONTAINS(
                            pi.cati_centers,
                            CONCAT('\"', tcc.cati_center_id, '\"')
                        )
                    ");
                })
                ->when(!empty($filters['campaign_id']), fn($q) => $q->where('tcn.client_title', $filters['campaign_id']))
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
                'pi.job_name_by_research',
                'pi.job_number',
                'pi.client_name',
                'pi.client_type',
                'pi.type_of_calling',
                'pi.client_industry',
                'pi.calling_methodology',
                'pi.languages',
                'pi.cati_centers',
                'tcn.client_title as tcn_client_name',
                'tci.client_industry_name as tci_client_industry_name',
                DB::raw("
                    GROUP_CONCAT(
                        DISTINCT tcc.cati_center_name
                        SEPARATOR ', '
                    ) as cati_center_names
                "),
            )
            ->groupBy(
                'mc.id',
                'pi.job_name_by_research',
                'pi.job_number',
                'pi.client_name',
                'pi.client_type',
                'pi.type_of_calling',
                'pi.client_industry',
                'pi.calling_methodology',
                'pi.languages',
                'pi.cati_centers',
                'tcn.client_title',
                'tci.client_industry_name'
            )
            ->orderByDesc('mc.call_date')
            ->paginate(50)
            ->withQueryString();

       $campaigns = DB::table('mobile_calls as mc')
        ->leftJoin('project_information as pi', function($join) {
            $join->whereRaw('pi.name_by_cati_team COLLATE utf8mb4_unicode_ci = mc.campaign_id');
        })
        ->leftJoin('tbl_client_names as tcn', function($join) {
            $join->whereRaw('tcn.client_id COLLATE utf8mb4_unicode_ci = pi.client_name');
        })
        ->leftJoin('tbl_client_industries as tci', function($join) {
            $join->whereRaw('tci.client_industry_id COLLATE utf8mb4_unicode_ci = pi.client_industry');
        })
        ->select('tcn.client_title as tcn_client_name')
        ->distinct()
        ->orderBy('tcn.client_title')
        ->pluck('tcn_client_name');

        $users     = DB::table('mobile_calls')->distinct()->orderBy('user')->pluck('user');
        $statuses  = DB::table('mobile_calls')->distinct()->orderBy('status_name')->pluck('status_name');

        $stats = $baseQuery()
            ->selectRaw('COUNT(*) as total, SUM(mc.length_in_sec) as total_sec, AVG(mc.length_in_sec) as avg_sec')
            ->first();

        return view('reports.mobile-calls', compact('calls', 'filters', 'campaigns', 'users', 'statuses', 'stats'));
    }
}