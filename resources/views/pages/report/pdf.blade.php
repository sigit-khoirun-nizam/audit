<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Temuan Audit Transaksi - {{ date('d-m-Y') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 1.5cm;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans min-h-screen p-6 sm:p-12 dark:bg-gray-900 dark:text-white/95">
    
    <!-- Print Navigation Bar -->
    <div class="no-print mx-auto max-w-7xl mb-8 flex justify-between items-center rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center gap-3">
            <svg class="text-brand-500" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><polyline points="10 9 9 9 8 9"/></svg>
            <div>
                <h4 class="font-bold text-gray-800 dark:text-white">Mode Pratinjau Cetak</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Klik tombol di sebelah kanan jika jendela cetak browser tidak terbuka secara otomatis.</p>
            </div>
        </div>
        <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600 transition shadow-sm">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak Laporan
        </button>
    </div>

    <!-- Printable Report Container -->
    <div class="mx-auto max-w-7xl bg-white p-8 border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-900/40 dark:border-gray-800">
        <!-- Logo and Report Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 pb-6 mb-6 dark:border-gray-800">
            <div>
                <h1 class="text-2xl font-black uppercase tracking-wider text-brand-600 dark:text-brand-500">
                    Sistem Informasi Audit Transaksi
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Laporan Temuan Audit & Tindak Lanjut Transaksi Keuangan</p>
            </div>
            <div class="mt-4 md:mt-0 text-left md:text-right text-xs text-gray-400 dark:text-gray-500 space-y-1">
                <p>Dicetak pada: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ date('d M Y, H:i') }}</span></p>
                <p>Oleh: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ Auth::user()->name }} ({{ Auth::user()->roles->pluck('name')->first() }})</span></p>
                <p>Status Data: <span class="font-semibold text-gray-700 dark:text-gray-300">Aktif</span></p>
            </div>
        </div>

        <!-- Filter Context Info -->
        <div class="mb-6 bg-gray-50 p-4 rounded-2xl border border-gray-100 dark:bg-white/[0.02] dark:border-gray-800/80 flex flex-wrap gap-x-8 gap-y-2 text-xs text-gray-500 dark:text-gray-400">
            <div>
                <span>Filter Status:</span>
                <span class="ml-1 font-semibold text-gray-800 dark:text-gray-200">
                    {{ request('status') ?: 'Semua Status' }}
                </span>
            </div>
            <div>
                <span>Rentang Tanggal:</span>
                <span class="ml-1 font-semibold text-gray-800 dark:text-gray-200">
                    {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }}
                    s/d
                    {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Akhir' }}
                </span>
            </div>
            @if(request('user_id'))
                <div>
                    <span>User / Teller Terfilter:</span>
                    <span class="ml-1 font-semibold text-gray-800 dark:text-gray-200">
                        ID #{{ request('user_id') }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Main Records Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-400 dark:border-gray-800 font-semibold uppercase">
                        <th class="py-3 px-3 w-8">No</th>
                        <th class="py-3 px-3 w-28">Tanggal</th>
                        <th class="py-3 px-3 w-24">Kode Pengguna</th>
                        <th class="py-3 px-3 w-32">Nomor Rekening</th>
                        <th class="py-3 px-3 w-44">Nama Nasabah</th>
                        <th class="py-3 px-3">Jenis Transaksi</th>
                        <th class="py-3 px-3 w-36">Pengguna / Teller</th>
                        <th class="py-3 px-3 w-28">Status</th>
                        <th class="py-3 px-3 w-36">Auditor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                    @forelse($transactions as $audit)
                        <tr class="align-top">
                            <td class="py-3.5 px-3 text-gray-500 font-medium">{{ $loop->iteration }}</td>
                            <td class="py-3.5 px-3 font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($audit->transaction_date)->format('d-m-Y') }}</td>
                            <td class="py-3.5 px-3 font-mono">{{ $audit->user_code }}</td>
                            <td class="py-3.5 px-3">{{ $audit->account_number }}</td>
                            <td class="py-3.5 px-3 font-medium text-gray-900 dark:text-white">{{ $audit->customer_name }}</td>
                            <td class="py-3.5 px-3 text-gray-500 dark:text-gray-400">{{ $audit->transaction_type }}</td>
                            <td class="py-3.5 px-3">{{ $audit->user ? $audit->user->name : 'Tidak Ada' }}</td>
                            <td class="py-3.5 px-3">
                                @if($audit->status === 'PENDING')
                                    <span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 font-semibold text-amber-800 dark:bg-amber-500/10 dark:text-amber-500">PENDING</span>
                                @elseif($audit->status === 'ON_REVIEW')
                                    <span class="inline-flex rounded-full bg-blue-50 px-2 py-0.5 font-semibold text-blue-800 dark:bg-blue-500/10 dark:text-blue-500">SEDANG DITINJAU</span>
                                @elseif($audit->status === 'REVISION')
                                    <span class="inline-flex rounded-full bg-red-50 px-2 py-0.5 font-semibold text-red-800 dark:bg-red-500/10 dark:text-red-500">REVISI</span>
                                @elseif($audit->status === 'DONE')
                                    <span class="inline-flex rounded-full bg-success-50 px-2 py-0.5 font-semibold text-success-800 dark:bg-success-500/10 dark:text-success-500">SELESAI</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-gray-500 dark:text-gray-400">
                                {{ $audit->creator ? $audit->creator->name : 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-400 dark:text-gray-500 italic">
                                Tidak ada data temuan audit transaksi yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Signature Block -->
        <div class="mt-16 grid grid-cols-2 gap-8 pt-8 border-t border-gray-100 dark:border-gray-800 text-xs">
            <div class="text-center">
                <p class="text-gray-400 dark:text-gray-500 mb-16">Dibuat & Diverifikasi Oleh,</p>
                <div class="border-b border-gray-300 dark:border-gray-700 mx-auto w-48 mb-1"></div>
                <p class="font-bold text-gray-800 dark:text-white/90">Auditor Internal</p>
                <p class="text-gray-400 dark:text-gray-500">Departemen Audit Transaksi</p>
            </div>
            <div class="text-center">
                <p class="text-gray-400 dark:text-gray-500 mb-16">Mengetahui & Menyetujui,</p>
                <div class="border-b border-gray-300 dark:border-gray-700 mx-auto w-48 mb-1"></div>
                <p class="font-bold text-gray-800 dark:text-white/90">Super Admin / Kepala Cabang</p>
                <p class="text-gray-400 dark:text-gray-500">Sistem Informasi Audit</p>
            </div>
        </div>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.addEventListener('load', () => {
            // Auto print with slight delay for layout compiling
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>
