<?php

namespace App\Http\Controllers;

use App\Models\AuditTransaction;
use App\Models\AuditComment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditCommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'audit_transaction_id' => 'required|exists:audit_transactions,id',
            'message'              => 'required|string|max:1000',
        ]);

        $transaction = AuditTransaction::findOrFail($request->audit_transaction_id);

        // Access check - only the assigned user, creator, or Superadmin can comment
        $user = Auth::user();
        if ($user && $user->hasRole('User') && $transaction->user_id !== $user->id) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        $comment = AuditComment::create([
            'audit_transaction_id' => $transaction->id,
            'user_id'              => Auth::id(),
            'message'              => $request->message,
        ]);

        // Log Activity
        ActivityLog::create([
            'user_id'              => Auth::id(),
            'audit_transaction_id' => $transaction->id,
            'action'               => 'POST_COMMENT',
            'ip_address'           => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil dikirim.');
    }
}
