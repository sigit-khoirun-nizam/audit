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
            @if(Auth::user()->hasRole('Auditor') || Auth::user()->hasRole('Superadmin'))
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Filter Berdasarkan Pengguna / Teller</label>
                    <div x-data="{
                        open: false,
                        search: '',
                        selectedId: '{{ request('user_id') }}',
                        selectedName: '{{ request('user_id') && $users->firstWhere('id', request('user_id')) ? addslashes($users->firstWhere('id', request('user_id'))->name . ' — ' . $users->firstWhere('id', request('user_id'))->user_code) : 'Semua Pengguna' }}',
                        options: [
                            @foreach($users as $u)
                                { id: '{{ $u->id }}', name: '{{ addslashes($u->name) }} — {{ $u->user_code }}' },
                            @endforeach
                        ],
                        get filteredOptions() {
                            if (!this.search) return this.options;
                            return this.options.filter(opt => opt.name.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        selectOption(opt) {
                            this.selectedId = opt.id;
                            this.selectedName = opt.name;
                            this.open = false;
                            this.search = '';
                        },
                        clearOption() {
                            this.selectedId = '';
                            this.selectedName = 'Semua Pengguna';
                            this.open = false;
                            this.search = '';
                        }
                    }" class="relative bg-transparent z-40" :class="open ? 'z-40' : 'z-20'">
                        <select name="user_id" id="user_id" class="hidden" :value="selectedId">
                            <option value="">Semua Pengguna</option>
                            <template x-for="opt in options" :key="opt.id">
                                <option :value="opt.id" :selected="opt.id == selectedId" x-text="opt.name"></option>
                            </template>
                        </select>

                        <button type="button" @click="open = !open" @click.outside="open = false" 
                            class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 py-2.5 text-sm text-left text-gray-800 dark:text-white/90 focus:outline-hidden focus:ring-1 focus:ring-brand-500 focus:border-brand-500 flex justify-between items-center dark:bg-white/[0.03] shadow-theme-xs">
                            <span x-text="selectedName" :class="selectedId ? 'text-gray-800 dark:text-white' : 'text-gray-800 dark:text-white/90'">Semua Pengguna</span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-transition class="absolute left-0 z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto p-2 space-y-2">
                            <input type="text" x-model="search" placeholder="Cari pengguna..." 
                                class="h-9 w-full rounded-md border border-gray-300 dark:border-gray-700 bg-transparent px-3 py-1.5 text-xs text-gray-800 dark:text-gray-200 focus:outline-hidden focus:ring-1 focus:ring-brand-500 focus:border-brand-500" 
                                @click.stop="">
                            <div class="space-y-1">
                                <button type="button" @click="clearOption()" 
                                    class="w-full text-left px-3 py-2 rounded-md text-xs hover:bg-brand-50 dark:hover:bg-brand-500/10 hover:text-brand-600 dark:hover:text-brand-400 text-gray-700 dark:text-gray-300 block">
                                    Semua Pengguna
                                </button>
                                <template x-for="opt in filteredOptions" :key="opt.id">
                                    <button type="button" @click="selectOption(opt)" 
                                        class="w-full text-left px-3 py-2 rounded-md text-xs hover:bg-brand-50 dark:hover:bg-brand-500/10 hover:text-brand-600 dark:hover:text-brand-400 text-gray-700 dark:text-gray-300 block">
                                        <span x-text="opt.name"></span>
                                    </button>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="py-2 text-center text-xs text-gray-400 dark:text-gray-500">
                                    Tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Status -->
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Status</label>
                <div class="relative z-20 bg-transparent">
                    <select name="status" 
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 dark:text-white/90 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:placeholder:text-white/30">
                        <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">Semua Status</option>
                        <option value="PENDING" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" {{ request('status') === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                        <option value="ON_REVIEW" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" {{ request('status') === 'ON_REVIEW' ? 'selected' : '' }}>SEDANG DITINJAU</option>
                        <option value="REVISION" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" {{ request('status') === 'REVISION' ? 'selected' : '' }}>REVISI</option>
                        <option value="DONE" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" {{ request('status') === 'DONE' ? 'selected' : '' }}>SELESAI</option>
                    </select>
                    <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
            </div>
            <!-- Start Date -->
            <div>
                <x-form.date-picker 
                    id="start_date" 
                    name="start_date" 
                    label="Tanggal Mulai" 
                    placeholder="Pilih Tanggal" 
                    defaultDate="{{ request('start_date') }}" />
            </div>
            <!-- End Date -->
            <div class="flex items-end gap-2">
                <div class="flex-grow">
                    <x-form.date-picker 
                        id="end_date" 
                        name="end_date" 
                        label="Tanggal Selesai" 
                        placeholder="Pilih Tanggal" 
                        defaultDate="{{ request('end_date') }}" />
                </div>
                <button type="submit" class="h-11 rounded-lg bg-gray-100 px-4 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10 transition">
                    Filter
                </button>
                @if(request()->anyFilled(['user_id', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('report.index') }}" class="h-11 flex items-center justify-center rounded-lg bg-red-50 px-3 text-sm font-medium text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-500 transition">
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
                            <td class="py-3.5 px-4 font-medium">{{ \Carbon\Carbon::parse($audit->transaction_date)->format('d-m-Y') }}</td>
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $audit->user_code }}</td>
                            <td class="py-3.5 px-4">{{ $audit->account_number }}</td>
                            <td class="py-3.5 px-4 font-medium">{{ $audit->customer_name }}</td>
                            <td class="py-3.5 px-4 text-gray-500 dark:text-gray-400">{{ $audit->transaction_type }}</td>
                            <td class="py-3.5 px-4">{{ $audit->user ? $audit->user->name : 'Tidak Ada' }}</td>
                            <td class="py-3.5 px-4">
                                @if($audit->status === 'PENDING')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-500/10 dark:text-amber-500">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        PENDING
                                    </span>
                                @elseif($audit->status === 'ON_REVIEW')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-800 dark:bg-blue-500/10 dark:text-blue-500">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        SEDANG DITINJAU
                                    </span>
                                @elseif($audit->status === 'REVISION')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-800 dark:bg-red-500/10 dark:text-red-500">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        REVISI
                                    </span>
                                @elseif($audit->status === 'DONE')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-semibold text-success-800 dark:bg-success-500/10 dark:text-success-500">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        SELESAI
                                    </span>
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
