<?php

namespace App\Http\Controllers;

use App\Models\ApiUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiUsageController extends Controller
{
    /**
     * Display a listing of API usage records with statistics
     */
    public function index(Request $request)
    {
        $query = ApiUsage::with(['project', 'user']);

        // Filters
        if ($request->filled('api_type')) {
            $query->where('api_type', $request->api_type);
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Get statistics for current filters
        $totalCost = $query->sum('estimated_cost');
        $totalCalls = $query->count();
        $successfulCalls = $query->where('status', 'success')->count();
        $failedCalls = $query->where('status', 'failed')->count();

        // Paginate results
        $apiUsages = $query->latest()->paginate(50)->withQueryString();

        // Get unique api types and purposes for filters
        $apiTypes = ApiUsage::select('api_type')->distinct()->pluck('api_type');
        $purposes = ApiUsage::select('purpose')->distinct()->pluck('purpose');

        return view('api-usage.index', compact(
            'apiUsages',
            'totalCost',
            'totalCalls',
            'successfulCalls',
            'failedCalls',
            'apiTypes',
            'purposes'
        ));
    }

    /**
     * Display the specified API usage record
     */
    public function show(ApiUsage $apiUsage)
    {
        $apiUsage->load(['project', 'user']);
        return view('api-usage.show', compact('apiUsage'));
    }

    /**
     * Display statistics dashboard
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subDays(30)->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        // Daily cost trend
        $dailyCosts = ApiUsage::whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(estimated_cost) as total_cost'),
                DB::raw('COUNT(*) as total_calls')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Cost by API type
        $costByApiType = ApiUsage::whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                'api_type',
                DB::raw('SUM(estimated_cost) as total_cost'),
                DB::raw('COUNT(*) as total_calls')
            )
            ->groupBy('api_type')
            ->orderByDesc('total_cost')
            ->get();

        // Cost by purpose
        $costByPurpose = ApiUsage::whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                'purpose',
                DB::raw('SUM(estimated_cost) as total_cost'),
                DB::raw('COUNT(*) as total_calls')
            )
            ->groupBy('purpose')
            ->orderByDesc('total_cost')
            ->get();

        // Top expensive projects
        $topProjects = ApiUsage::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('project_id')
            ->select(
                'project_id',
                DB::raw('SUM(estimated_cost) as total_cost'),
                DB::raw('COUNT(*) as total_calls')
            )
            ->groupBy('project_id')
            ->orderByDesc('total_cost')
            ->limit(10)
            ->with('project')
            ->get();

        // Overall statistics
        $totalCost = ApiUsage::whereBetween('created_at', [$dateFrom, $dateTo])->sum('estimated_cost');
        $totalCalls = ApiUsage::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $successRate = $totalCalls > 0
            ? (ApiUsage::whereBetween('created_at', [$dateFrom, $dateTo])->where('status', 'success')->count() / $totalCalls) * 100
            : 0;

        return view('api-usage.statistics', compact(
            'dailyCosts',
            'costByApiType',
            'costByPurpose',
            'topProjects',
            'totalCost',
            'totalCalls',
            'successRate',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApiUsage $apiUsage)
    {
        $apiUsage->delete();

        return redirect()->route('api-usage.index')
            ->with('success', 'API usage record deleted successfully.');
    }
}
