@extends('layouts.app')

@section('content')
  <div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-white/90 sm:text-2xl">
                Laporan & Log Ekspor
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Filter dan ekspor temuan audit transaksi sebagai laporan spreadsheet atau dokumen
            </p>
        </div>
        
        <!-- Export Actions -->
        <div class="flex items-center gap-2">
            <a href="{{ route('report.export.excel', request()->all()) }}" 
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><polyline points="10 9 9 9 8 9"/></svg>
                Ekspor Excel (CSV)
            </a>
            <a href="{{ route('report.export.pdf', request()->all()) }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition shadow-theme-xs">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak Laporan PDF
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <form method="GET" action="{{ route('report.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- User Filter -->
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Filter Berdasarkan Pengguna / Teller</label>
                <select name="user_id" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->user_code }})
                        </option>
                    @endforeach
                </select>
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
            <!-- Start Date -->
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
            </div>
            <!-- End Date -->
            <div class="flex items-end gap-2">
                <div class="flex-grow">
                    <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>
                <button type="submit" class="h-10 rounded-lg bg-gray-100 px-4 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10 transition">
                    Filter
                </button>
                @if(request()->anyFilled(['user_id', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('report.index') }}" class="h-10 flex items-center justify-center rounded-lg bg-red-50 px-3 text-sm font-medium text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-500 transition">
                        Atur Ulang
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
                        <th class="py-3 px-4">Jenis Transaksi</th>
                        <th class="py-3 px-4">Pengguna Diaudit</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Auditor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($transactions as $audit)
                        <tr>
                            <td class="py-3.5 px-4 text-gray-500">{{ $loop->iteration + ($transactions->firstItem() - 1) }}</td>
                            <td class="py-3.5 px-4 font-medium">{{ $audit->transaction_date }}</td>
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $audit->user_code }}</td>
                            <td class="py-3.5 px-4">{{ $audit->account_number }}</td>
                            <td class="py-3.5 px-4 font-medium">{{ $audit->customer_name }}</td>
                            <td class="py-3.5 px-4 text-gray-500 dark:text-gray-400">{{ $audit->transaction_type }}</td>
                            <td class="py-3.5 px-4">{{ $audit->user ? $audit->user->name : 'Tidak Ada' }}</td>
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
                            <td class="py-3.5 px-4 text-gray-500 dark:text-gray-400 text-xs">
                                {{ $audit->creator ? $audit->creator->name : 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-400">
                                Tidak ada data yang sesuai dengan filter pencarian.
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
  </div>
@endsection
