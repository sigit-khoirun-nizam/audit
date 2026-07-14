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
        // Placeholder for Excel export
        // In the future, this will build a spreadsheet using Laravel Excel or direct CSV output
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_report_' . date('Y-m-d') . '.csv"',
        ];

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

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Tanggal Transaksi', 'ID User', 'Kode User', 'Nomor Rekening', 'Nama Nasabah', 'Jenis Transaksi', 'Deskripsi', 'Status', 'Dibuat Oleh']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->transaction_date,
                    $t->user_id,
                    $t->user_code,
                    $t->account_number,
                    $t->customer_name,
                    $t->transaction_type,
                    $t->description,
                    $t->status,
                    $t->creator ? $t->creator->name : 'System',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
