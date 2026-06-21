<?php

namespace App\Http\Controllers;

use App\Models\AuditTransaction;
use App\Models\AuditResponse;
use App\Models\ResponseFile;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AuditResponseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'audit_transaction_id' => 'required|exists:audit_transactions,id',
            'note'                 => 'required|string',
            'files'                => 'required|array|min:1',
            'files.*'              => 'file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $transaction = AuditTransaction::findOrFail($request->audit_transaction_id);

        // Access check - only the assigned user (or Auditor/Superadmin) can respond
        $user = Auth::user();
        if ($user && $user->hasRole('User') && $transaction->user_id !== $user->id) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        // Create the response record
        $response = AuditResponse::create([
            'audit_transaction_id' => $transaction->id,
            'user_id'              => Auth::id(),
            'note'                 => $request->note,
            'status'               => 'SUBMITTED',
        ]);

        // Update the audit transaction status to ON_REVIEW
        $oldStatus = $transaction->status;
        $transaction->status = 'ON_REVIEW';
        $transaction->save();

        // Handle response file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $path = $file->store('audit', 'public');

                ResponseFile::create([
                    'response_id' => $response->id,
                    'file_name'   => $originalName,
                    'file_path'   => $path,
                ]);
            }
        }

        // Log Activity
        ActivityLog::create([
            'user_id'              => Auth::id(),
            'audit_transaction_id' => $transaction->id,
            'action'               => 'SUBMIT_RESPONSE',
            'ip_address'           => $request->ip(),
        ]);

        // WhatsApp Notification to Auditor (Creator of transaction)
        $auditor = User::find($transaction->created_by);
        if ($auditor) {
            $this->sendWhatsAppToAuditor($auditor, $user, $transaction, $response);
        }

        return redirect()->back()->with('success', 'Respon bukti berhasil dikirim.');
    }

    private function sendWhatsAppToAuditor(User $auditor, User $submittingUser, AuditTransaction $transaction, AuditResponse $response)
    {
        $phone = $auditor->phone;
        if (!$phone) {
            Log::warning("WhatsApp not sent to Auditor ID {$auditor->id} because phone number is missing.");
            return;
        }

        $message = "📢 *KONFIRMASI PENYELESAIAN AUDIT*\n"
                 . "================================\n\n"
                 . "Halo *Tim Auditor*,\n\n"
                 . "Teller telah menyelesaikan temuan audit dan mengupload bukti penyelesaian.\n\n"
                 . "```\n"
                 . "Tanggal Audit : " . $transaction->transaction_date . "\n"
                 . "ID Teller     : " . $submittingUser->user_code . "\n"
                 . "Nama Teller   : " . $submittingUser->name . "\n"
                 . "No Rekening   : " . $transaction->account_number . "\n"
                 . "Nasabah       : " . $transaction->customer_name . "\n"
                 . "Jenis Trans   : " . $transaction->transaction_type . "\n"
                 . "Status        : MENUNGGU REVIEW\n"
                 . "Waktu Upload  : " . now()->format('d-m-Y H:i') . "\n"
                 . "```\n\n"
                 . "📝 *Catatan Teller:*\n"
                 . $response->note . "\n\n"
                 . "📎 Bukti penyelesaian sudah berhasil dilampirkan.\n\n"
                 . "Silahkan login Sistem Audit untuk melakukan pengecekan dan approval.\n\n"
                 . "--------------------------------\n"
                 . "-=| *AUDIT MONITORING SYSTEM* |=-";

        try {
            $token = env('FONNTE_TOKEN');
            if ($token) {
                $responseHttp = Http::withoutVerifying()
                    ->withHeaders([
                        'Authorization' => $token,
                    ])
                    ->post('https://api.fonnte.com/send', [
                        'target'  => $phone,
                        'message' => $message,
                    ]);

                Log::info("Fonnte WA Response to {$phone}: " . $responseHttp->body());
            } else {
                Log::warning("Fonnte Token is missing in .env.");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp notification via Fonnte to {$phone}: " . $e->getMessage());
        }
    }
}
