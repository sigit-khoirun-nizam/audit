<?php

namespace App\Http\Controllers;

use App\Models\AuditTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            // Fallback for unauthenticated access in default views
            return view('pages.dashboard.ecommerce', [
                'title' => 'E-commerce Dashboard',
                'metrics' => [
                    'total' => 0,
                    'pending' => 0,
                    'on_review' => 0,
                    'done' => 0,
                    'overdue' => 0
                ]
            ]);
        }

        $metrics = [];

        if ($user->hasRole('Superadmin')) {
            // Superadmin Metrics
            $metrics['total'] = AuditTransaction::count();
            $metrics['pending'] = AuditTransaction::where('status', 'PENDING')->count();
            $metrics['on_review'] = AuditTransaction::where('status', 'ON_REVIEW')->count();
            $metrics['done'] = AuditTransaction::where('status', 'DONE')->count();
            
            // Overdue: non-DONE audits older than 3 days
            $metrics['overdue'] = AuditTransaction::where('status', '!=', 'DONE')
                ->where('created_at', '<', now()->subDays(3))
                ->count();

            // Monthly stats for chart
            $metrics['monthly_chart'] = AuditTransaction::select(
                DB::raw('count(id) as count'),
                DB::raw("DATE_FORMAT(transaction_date, '%Y-%m') as month")
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        } elseif ($user->hasRole('Auditor')) {
            // Auditor Metrics
            $metrics['created'] = AuditTransaction::where('created_by', $user->id)->count();
            $metrics['awaiting_review'] = AuditTransaction::where('created_by', $user->id)
                ->where('status', 'ON_REVIEW')
                ->count();
            $metrics['done'] = AuditTransaction::where('created_by', $user->id)
                ->where('status', 'DONE')
                ->count();

        } else {
            // User Dashboard
            $metrics['my_total'] = AuditTransaction::where('user_id', $user->id)->count();
            $metrics['pending'] = AuditTransaction::where('user_id', $user->id)->where('status', 'PENDING')->count();
            $metrics['review'] = AuditTransaction::where('user_id', $user->id)->where('status', 'ON_REVIEW')->count();
            $metrics['done'] = AuditTransaction::where('user_id', $user->id)->where('status', 'DONE')->count();
        }

        // Fetch recent audits based on role
        if ($user->hasRole('User')) {
            $recentAudits = AuditTransaction::with(['user', 'creator'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        } else {
            $recentAudits = AuditTransaction::with(['user', 'creator'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('pages.dashboard.ecommerce', [
            'title' => 'Dashboard',
            'metrics' => $metrics,
            'recentAudits' => $recentAudits,
        ]);
    }
}
