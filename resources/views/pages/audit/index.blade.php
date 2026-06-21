@extends('layouts.app')

@section('content')
  @php
      $user = Auth::user();
  @endphp

  <div class="space-y-6" x-data="{ importModalOpen: false }">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-white/90 sm:text-2xl">
                Transaksi Audit
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola dan pantau semua masalah transaksi yang dilaporkan
            </p>
        </div>
        @if ($user->hasRole('Auditor') || $user->hasRole('Superadmin'))
            <div class="flex items-center gap-3">
                <button @click="importModalOpen = true"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] transition shadow-theme-xs">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Impor Excel
                </button>
                <a href="{{ route('audit.create') }}" 
                   class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 4.16699V15.8337M4.16669 10.0003H15.8334" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Temuan Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <form method="GET" action="{{ route('audit.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Search -->
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No Rek, Nasabah, Kode User..."
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
            </div>
            <!-- Status -->
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Status</label>
                <select name="status" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Semua Status</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                    <option value="ON_REVIEW" {{ request('status') === 'ON_REVIEW' ? 'selected' : '' }}>SEDANG DITINJAU</option>
                    <option value="REVISION" {{ request('status') === 'REVISION' ? 'selected' : '' }}>REVISI</option>
                    <option value="DONE" {{ request('status') === 'DONE' ? 'selected' : '' }}>SELESAI</option>
                </select>
            </div>
            <!-- Tanggal Mulai -->
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
            </div>
            <!-- Tanggal Akhir -->
            <div class="flex items-end gap-2">
                <div class="flex-grow">
                    <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>
                <button type="submit" class="h-10 rounded-lg bg-gray-100 px-4 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10 transition">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('audit.index') }}" class="h-10 flex items-center justify-center rounded-lg bg-red-50 px-3 text-sm font-medium text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-500 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-semibold uppercase text-gray-400 dark:border-gray-800">
                        <th class="py-3 px-4 w-12">No</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Kode Pengguna</th>
                        <th class="py-3 px-4">Nomor Rekening</th>
                        <th class="py-3 px-4">Nama Nasabah</th>
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4">User Diaudit</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($transactions as $audit)
                        <tr>
                            <td class="py-3.5 px-4 text-gray-500">{{ $loop->iteration + ($transactions->firstItem() - 1) }}</td>
                            <td class="py-3.5 px-4 font-medium">{{ $audit->transaction_date }}</td>
                            <td class="py-3.5 px-4 text-gray-500 dark:text-gray-400">{{ $audit->user_code }}</td>
                            <td class="py-3.5 px-4">{{ $audit->account_number }}</td>
                            <td class="py-3.5 px-4 font-medium">{{ $audit->customer_name }}</td>
                            <td class="py-3.5 px-4 text-gray-500 dark:text-gray-400">{{ $audit->transaction_type }}</td>
                            <td class="py-3.5 px-4 text-sm">{{ $audit->user ? $audit->user->name : 'Tidak Ada' }}</td>
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
                            <td colspan="9" class="py-8 text-center text-gray-400">
                                Tidak ada data transaksi audit ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div x-show="importModalOpen" 
         class="fixed inset-0 z-99999 flex items-center justify-center p-4 bg-gray-900/60 dark:bg-gray-950/70"
         x-cloak
         style="display: none;"
         @keydown.escape.window="importModalOpen = false">
        
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900"
             @click.away="importModalOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                    Impor Transaksi Audit
                </h3>
                <button @click="importModalOpen = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-white">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            
            <form action="{{ route('audit.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase text-gray-400">Pilih File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-white/5 dark:file:text-white/85" />
                </div>
                
                <!-- Format Instructions -->
                <div class="rounded-xl bg-gray-50 p-4 dark:bg-white/5 text-xs text-gray-500 dark:text-gray-400 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-gray-700 dark:text-gray-300">Format Kolom Excel:</p>
                        <a href="{{ route('audit.import.template') }}" class="text-brand-500 hover:text-brand-600 font-bold flex items-center gap-1 transition">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Unduh Contoh
                        </a>
                    </div>
                    <ul class="list-disc pl-4 space-y-1">
                        <li><code>tanggal_transaksi</code> (YYYY-MM-DD)</li>
                        <li><code>kode_user</code> (Kode unik user / teller)</li>
                        <li><code>nomor_rekening</code></li>
                        <li><code>nama_nasabah</code></li>
                        <li><code>jenis_transaksi</code></li>
                        <li><code>deskripsi</code> (Deskripsi temuan)</li>
                    </ul>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <button type="button" @click="importModalOpen = false"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition shadow-sm">
                        Mulai Impor
                    </button>
                </div>
            </form>
        </div>
    </div>
  </div>
@endsection

