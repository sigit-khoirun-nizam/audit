<?php

namespace App\Http\Controllers;

use App\Models\AuditTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditTransaction::with(['user', 'creator']);

        // Role-based scoping
        $user = auth()->user();
        if ($user && $user->hasRole('User')) {
            $query->where('user_id', $user->id);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->start_date !== '') {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date !== '') {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(25);
        $users = User::role('User')->get();

        return view('pages.report.index', compact('transactions', 'users'));
    }

    public function exportExcel(Request $request)
    {
        $query = AuditTransaction::with(['user', 'creator']);

        // Role-based scoping
        $user = auth()->user();
        if ($user && $user->hasRole('User')) {
            $query->where('user_id', $user->id);
        }

        // Apply same filters
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->has('start_date') && $request->start_date !== '') $query->where('transaction_date', '>=', $request->start_date);
        if ($request->has('end_date') && $request->end_date !== '') $query->where('transaction_date', '<=', $request->end_date);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);

        $transactions = $query->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AuditReportExport($transactions), 
            'audit_report_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        // Placeholder for PDF export
        // For now, return a printable simple HTML page of the filtered audit items
        $query = AuditTransaction::with(['user', 'creator']);

        // Role-based scoping
        $user = auth()->user();
        if ($user && $user->hasRole('User')) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->has('start_date') && $request->start_date !== '') $query->where('transaction_date', '>=', $request->start_date);
        if ($request->has('end_date') && $request->end_date !== '') $query->where('transaction_date', '<=', $request->end_date);
        if ($request->has('user_id') && $request->user_id !== '') $query->where('user_id', $request->user_id);

        $transactions = $query->get();

        return view('pages.report.pdf', compact('transactions'));
    }
}
