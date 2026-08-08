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
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 15mm;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-white text-black font-sans min-h-screen p-4 sm:p-8">
    
    <!-- Print Navigation Bar -->
    <div class="no-print mx-auto max-w-7xl mb-8 flex justify-between items-center rounded-lg border border-gray-300 bg-gray-50 p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div>
                <h4 class="font-bold text-gray-800">Mode Pratinjau Cetak PDF</h4>
                <p class="text-sm text-gray-600">Gunakan pengaturan Landscape untuk hasil terbaik.</p>
            </div>
        </div>
        <button onclick="window.print()" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">
            Cetak Dokumen
        </button>
    </div>

    <!-- Printable Report Container -->
    <div class="mx-auto max-w-7xl bg-white">
        
        <!-- Header Laporan -->
        <div class="border-b-2 border-black pb-4 mb-6">
            <h1 class="text-2xl font-bold uppercase text-black text-center">
                Laporan Audit Transaksi
            </h1>
            <p class="text-md text-center">Periode: {{ request('start_date') ? date('d/m/Y', strtotime(request('start_date'))) : 'Awal' }} s/d {{ request('end_date') ? date('d/m/Y', strtotime(request('end_date'))) : 'Akhir' }}</p>
            <div class="mt-4 flex justify-between text-sm">
                <div>Status: {{ request('status') ?: 'Semua Status' }}</div>
                <div>Tanggal Cetak: {{ date('d F Y, H:i') }}</div>
            </div>
        </div>

        <!-- Main Records Table -->
        <table class="w-full text-left text-sm border-collapse border border-black mb-8">
            <thead>
                <tr>
                    <th class="py-2 px-3 border border-black text-center w-12">No</th>
                    <th class="py-2 px-3 border border-black">Tanggal</th>
                    <th class="py-2 px-3 border border-black">Kode Teller</th>
                    <th class="py-2 px-3 border border-black">No. Rekening</th>
                    <th class="py-2 px-3 border border-black">Nama Nasabah</th>
                    <th class="py-2 px-3 border border-black">Tipe Transaksi</th>
                    <th class="py-2 px-3 border border-black">Nama Teller</th>
                    <th class="py-2 px-3 border border-black text-center">Status</th>
                    <th class="py-2 px-3 border border-black">Auditor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $audit)
                    <tr>
                        <td class="py-2 px-3 text-center border border-black">{{ $loop->iteration }}</td>
                        <td class="py-2 px-3 border border-black">{{ \Carbon\Carbon::parse($audit->transaction_date)->format('d/m/Y') }}</td>
                        <td class="py-2 px-3 border border-black">{{ $audit->user_code }}</td>
                        <td class="py-2 px-3 border border-black">{{ $audit->account_number }}</td>
                        <td class="py-2 px-3 border border-black">{{ $audit->customer_name }}</td>
                        <td class="py-2 px-3 border border-black">{{ $audit->transaction_type }}</td>
                        <td class="py-2 px-3 border border-black">{{ $audit->user ? $audit->user->name : '-' }}</td>
                        <td class="py-2 px-3 border border-black text-center">{{ $audit->status }}</td>
                        <td class="py-2 px-3 border border-black">
                            {{ $audit->creator ? $audit->creator->name : 'Sistem' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-gray-500 font-medium border border-black">
                            Tidak ada data temuan audit transaksi yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
    </div>

    <!-- Auto Print Script -->
    <script>
        window.addEventListener('load', () => {
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>
