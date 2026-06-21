<?php

namespace App\Http\Controllers;

use App\Models\AuditTransaction;
use App\Models\AuditFile;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AuditTransactionImport;

class AuditTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditTransaction::with(['user', 'creator']);

        // Role-based scoping
        $user = Auth::user();
        if ($user && $user->hasRole('User')) {
            $query->where('user_id', $user->id);
        }

        // Filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->start_date !== '') {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date !== '') {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('account_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('user_code', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(10);

        return view('pages.audit.index', compact('transactions'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->hasRole('Auditor') && !$user->hasRole('Superadmin')) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        // Fetch all users with 'User' role to assign the transaction
        $users = User::role('User')->get();
        return view('pages.audit.create', compact('users'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Auditor') && !$user->hasRole('Superadmin')) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        $request->validate([
            'transaction_date' => 'required|date',
            'user_id'          => 'required|exists:users,id',
            'account_number'   => 'required|string',
            'customer_name'    => 'required|string',
            'transaction_type' => 'required|string',
            'description'      => 'required|string',
            'files'            => 'nullable|array',
            'files.*'          => 'file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $assignedUser = User::findOrFail($request->user_id);

        $transaction = AuditTransaction::create([
            'transaction_date' => $request->transaction_date,
            'user_id'          => $request->user_id,
            'user_code'        => $assignedUser->user_code ?? '',
            'account_number'   => $request->account_number,
            'customer_name'    => $request->customer_name,
            'transaction_type' => $request->transaction_type,
            'description'      => $request->description,
            'status'           => 'PENDING',
            'created_by'       => Auth::id(),
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $path = $file->store('audit', 'public');

                AuditFile::create([
                    'audit_transaction_id' => $transaction->id,
                    'file_name'            => $originalName,
                    'file_path'            => $path,
                    'uploaded_by'          => Auth::id(),
                ]);
            }
        }

        // Activity Log
        ActivityLog::create([
            'user_id'              => Auth::id(),
            'audit_transaction_id' => $transaction->id,
            'action'               => 'CREATE_AUDIT',
            'ip_address'           => $request->ip(),
        ]);

        // WhatsApp Notification to User
        $this->sendWhatsAppToUser($assignedUser, $transaction);

        return redirect()->route('audit.index')->with('success', 'Transaksi audit berhasil dibuat.');
    }

    public function show($id)
    {
        $transaction = AuditTransaction::with([
            'user', 
            'creator', 
            'files.uploader', 
            'responses.user', 
            'responses.files', 
            'comments.user'
        ])->findOrFail($id);

        // Access check
        $user = Auth::user();
        if ($user && $user->hasRole('User') && $transaction->user_id !== $user->id) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        return view('pages.audit.show', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('Auditor') && !$user->hasRole('Superadmin')) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        $request->validate([
            'status' => 'required|in:PENDING,ON_REVIEW,REVISION,DONE',
        ]);

        $transaction = AuditTransaction::findOrFail($id);
        $oldStatus = $transaction->status;
        $transaction->status = $request->status;
        $transaction->save();

        // Activity Log
        ActivityLog::create([
            'user_id'              => Auth::id(),
            'audit_transaction_id' => $transaction->id,
            'action'               => "CHANGE_STATUS_FROM_{$oldStatus}_TO_{$request->status}",
            'ip_address'           => $request->ip(),
        ]);

        // WhatsApp Notification if status changed to DONE (Approved)
        if ($request->status === 'DONE' && $oldStatus !== 'DONE') {
            $assignedUser = User::find($transaction->user_id);
            if ($assignedUser) {
                $this->sendWhatsAppApproval($assignedUser, $transaction);
            }
        }

        $statusLabel = match($request->status) {
            'PENDING' => 'PENDING',
            'ON_REVIEW' => 'SEDANG DITINJAU',
            'REVISION' => 'REVISI',
            'DONE' => 'SELESAI',
            default => $request->status
        };
        return redirect()->back()->with('success', "Status berhasil diperbarui menjadi {$statusLabel}.");
    }

    private function sendWhatsAppToUser(User $user, AuditTransaction $transaction)
    {
        $phone = $user->phone;
        if (!$phone) {
            Log::warning("WhatsApp not sent to User ID {$user->id} because phone number is missing.");
            return;
        }

        $message = "Halo {$user->name}\n\n"
                 . "Ada temuan audit baru:\n\n"
                 . "Tanggal: {$transaction->transaction_date}\n"
                 . "Nasabah: {$transaction->customer_name}\n"
                 . "Keterangan: {$transaction->description}\n\n"
                 . "Silahkan login sistem audit untuk upload bukti penyelesaian.";

        // Simulate WhatsApp Gateway API Call
        Log::info("SIMULATING WA GATEWAY SEND TO {$phone}: \n{$message}");
    }

    private function sendWhatsAppApproval(User $user, AuditTransaction $transaction)
    {
        $phone = $user->phone;
        if (!$phone) {
            return;
        }

        $message = "Audit anda sudah selesai.\n\n"
                 . "Status: SELESAI\n\n"
                 . "Terima kasih.";

        // Simulate WhatsApp Gateway API Call
        Log::info("SIMULATING WA GATEWAY SEND TO {$phone}: \n{$message}");
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_audit.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            
            // Excel headers
            fputcsv($file, [
                'tanggal_transaksi',
                'kode_user',
                'nomor_rekening',
                'nama_nasabah',
                'jenis_transaksi',
                'deskripsi'
            ]);

            // Sample rows
            fputcsv($file, [
                date('Y-m-d'),
                '17800T60',
                '000003',
                'GEMILANG',
                'TELLER 1780060',
                'Keterangan selisih kas teller sebesar Rp 100,000'
            ]);
            
            fputcsv($file, [
                date('Y-m-d'),
                '17800CS63',
                '000004',
                'BUDI SANJAYA',
                'CUSTOMER SERVICE',
                'Kesalahan input data pembukaan rekening baru'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importExcel(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Auditor') && !$user->hasRole('Superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new AuditTransactionImport;
            Excel::import($import, $request->file('file'));

            $count = $import->getImportedCount();
            $skipped = $import->getSkippedRows();

            $msg = "Berhasil mengimpor {$count} data transaksi audit.";
            if (count($skipped) > 0) {
                $reasons = collect($skipped)->map(fn($item) => $item['reason'])->unique()->implode(', ');
                return redirect()->route('audit.index')->with('success', $msg . " Beberapa baris dilewati karena: " . $reasons);
            }

            return redirect()->route('audit.index')->with('success', $msg);

        } catch (\Exception $e) {
            Log::error("Import Excel Error: " . $e->getMessage());
            return redirect()->back()->withErrors(['file' => 'Gagal mengimpor file Excel: ' . $e->getMessage()]);
        }
    }
}
