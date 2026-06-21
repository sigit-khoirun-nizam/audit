@extends('layouts.app')

@section('content')
  @php
      $user = Auth::user();
      $role = $user ? $user->roles->pluck('name')->first() : 'Guest';
  @endphp

  <div class="space-y-6">
    <!-- Welcome Banner Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white/90 sm:text-2xl">
                    Selamat datang kembali, {{ $user->name }}!
                </h1>
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                    Sistem Informasi Audit Transaksi &mdash; Peran: <span class="font-semibold text-brand-500">{{ $role }}</span>
                </p>
            </div>
            @if ($user->hasRole('Auditor') || $user->hasRole('Superadmin'))
                <div>
                    <a href="{{ route('audit.create') }}" 
                       class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 4.16699V15.8337M4.16669 10.0003H15.8334" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Temuan Audit Baru
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
        @if ($role === 'Superadmin')
            <!-- Total Audits -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Audit</span>
                        <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $metrics['total'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                </div>
            </div>
            <!-- Pending -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Temuan Tertunda</span>
                        <h4 class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-500">{{ $metrics['pending'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
            </div>
            <!-- On Review -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Sedang Ditinjau</span>
                        <h4 class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-500">{{ $metrics['on_review'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                </div>
            </div>
            <!-- Overdue -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Terlambat (&gt;3 hari)</span>
                        <h4 class="mt-2 text-2xl font-bold text-red-600 dark:text-red-500">{{ $metrics['overdue'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                </div>
            </div>

        @elseif ($role === 'Auditor')
            <!-- Created -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Audit Dibuat</span>
                        <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $metrics['created'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </div>
                </div>
            </div>
            <!-- Awaiting Review -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu Peninjauan</span>
                        <h4 class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-500">{{ $metrics['awaiting_review'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
            </div>
            <!-- Done -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Terverifikasi & Selesai</span>
                        <h4 class="mt-2 text-2xl font-bold text-success-600 dark:text-success-500">{{ $metrics['done'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 text-success-600">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>
            </div>

        @else
            <!-- My Total -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Audit Saya</span>
                        <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $metrics['my_total'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                </div>
            </div>
            <!-- Pending Action -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tindakan Tertunda</span>
                        <h4 class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-500">{{ $metrics['pending'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
            </div>
            <!-- Under Review -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Dikirim / Ditinjau</span>
                        <h4 class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-500">{{ $metrics['review'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                </div>
            </div>
            <!-- Completed -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Selesai</span>
                        <h4 class="mt-2 text-2xl font-bold text-success-600 dark:text-success-500">{{ $metrics['done'] ?? 0 }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 text-success-600">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Recent Audits Table -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <div class="flex flex-col justify-between gap-4 border-b border-gray-100 pb-4 dark:border-gray-800 sm:flex-row sm:items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                    Temuan Audit Terbaru
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Masalah terbaru yang dilaporkan pada transaksi
                </p>
            </div>
            <div>
                <a href="{{ route('audit.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:hover:text-brand-400">
                    Lihat Semua Audit &rarr;
                </a>
            </div>
        </div>
        
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-semibold uppercase text-gray-400 dark:border-gray-800">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Kode User</th>
                        <th class="py-3 px-4">Nomor Rekening</th>
                        <th class="py-3 px-4">Nama Nasabah</th>
                        <th class="py-3 px-4">Jenis Transaksi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentAudits as $audit)
                        <tr>
                            <td class="py-3.5 px-4 font-medium">{{ $audit->transaction_date }}</td>
                            <td class="py-3.5 px-4 text-gray-500 dark:text-gray-400">{{ $audit->user_code }}</td>
                            <td class="py-3.5 px-4">{{ $audit->account_number }}</td>
                            <td class="py-3.5 px-4 font-medium">{{ $audit->customer_name }}</td>
                            <td class="py-3.5 px-4 text-gray-500 dark:text-gray-400">{{ $audit->transaction_type }}</td>
                            <td class="py-3.5 px-4">
                                @if($audit->status === 'PENDING')
                                    <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-500/10 dark:text-amber-500">PENDING</span>
                                @elseif($audit->status === 'ON_REVIEW')
                                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-500/10 dark:text-blue-500">SEDANG DITINJAU</span>
                                @elseif($audit->status === 'REVISION')
                                    <span class="inline-flex rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-500/10 dark:text-red-500">REVISI</span>
                                @elseif($audit->status === 'DONE')
                                    <span class="inline-flex rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-800 dark:bg-success-500/10 dark:text-success-500">SELESAI</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('audit.show', $audit->id) }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600 dark:hover:text-brand-400">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-400">
                                Tidak ada temuan audit ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
  </div>
@endsection
