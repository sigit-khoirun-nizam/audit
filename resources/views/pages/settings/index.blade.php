@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pengaturan Sistem" />
    
    <div class="space-y-6 max-w-2xl">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
            <div class="border-b border-gray-100 pb-4 dark:border-gray-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                    Pengaturan WhatsApp Gateway
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Konfigurasi nomor WhatsApp tujuan notifikasi otomatis ketika temuan audit selesai (DONE).
                </p>
            </div>

            <!-- Session Notifications -->
            @if(session('success'))
                <div class="mt-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-500/10 dark:text-green-500 flex items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-red-500/10 dark:text-red-500 space-y-1">
                    @foreach($errors->all() as $error)
                        <p class="flex items-center gap-2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="notification_phone" class="mb-1.5 block text-xs font-semibold uppercase text-gray-400">
                        Nomor WhatsApp Penerima
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="notification_phone" 
                               name="notification_phone" 
                               value="{{ old('notification_phone', $notificationPhone) }}" 
                               placeholder="Contoh: 08983274464"
                               class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" 
                               required />
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">
                        Gunakan format nomor lokal (seperti 08983274464) atau internasional (seperti 628983274464). Notifikasi akan otomatis terkirim ke nomor ini saat audit dinyatakan Selesai.
                    </p>
                </div>

                <div class="flex items-center justify-end border-t border-gray-100 pt-4 dark:border-gray-800">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition shadow-sm">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
