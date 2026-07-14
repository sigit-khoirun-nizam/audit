<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('Auditor') && !$user->hasRole('Superadmin')) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        $notificationPhone = Setting::get('notification_phone', '08983274464');

        return view('pages.settings.index', compact('notificationPhone'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Auditor') && !$user->hasRole('Superadmin')) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        $request->validate([
            'notification_phone' => 'required|string|min:9|max:15',
        ]);

        Setting::set('notification_phone', $request->notification_phone);

        return redirect()->back()->with('success', 'Nomor notifikasi WhatsApp berhasil diperbarui.');
    }
}
