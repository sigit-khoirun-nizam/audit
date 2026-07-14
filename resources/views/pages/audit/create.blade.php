@extends('layouts.app')

@section('content')
  <div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('audit.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-white/90 sm:text-2xl">
                Buat Temuan Audit
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Input masalah transaksi dan tugaskan ke pengguna
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <form method="POST" action="{{ route('audit.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <!-- Transaction Date -->
                <div>
                    <x-form.date-picker 
                        id="transaction_date" 
                        name="transaction_date" 
                        label="Tanggal Transaksi" 
                        placeholder="Pilih Tanggal" 
                        defaultDate="{{ old('transaction_date', date('Y-m-d')) }}" />
                    @error('transaction_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assigned User -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tugaskan ke Pengguna / Teller<span class="text-red-500">*</span>
                    </label>
                    <div x-data="{
                        open: false,
                        search: '',
                        selectedId: '{{ old('user_id') }}',
                        selectedName: '{{ old('user_id') ? addslashes($users->firstWhere('id', old('user_id'))->name . ' — ' . $users->firstWhere('id', old('user_id'))->user_code) : 'Pilih Pengguna' }}',
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
                        }
                    }" class="relative bg-transparent z-40" :class="open ? 'z-40' : 'z-20'">
                        <!-- Hidden Select to bind with form submission -->
                        <select name="user_id" id="user_id" class="hidden" required :value="selectedId">
                            <option value="">Pilih Pengguna</option>
                            <template x-for="opt in options" :key="opt.id">
                                <option :value="opt.id" :selected="opt.id == selectedId" x-text="opt.name"></option>
                            </template>
                        </select>

                        <button type="button" @click="open = !open" @click.outside="open = false" 
                            class="h-11 w-full rounded-lg border @error('user_id') border-red-500 @else border-gray-300 dark:border-gray-700 @enderror bg-transparent px-4 py-2.5 text-sm text-left text-gray-800 dark:text-white/90 focus:outline-hidden focus:ring-1 focus:ring-brand-500 focus:border-brand-500 flex justify-between items-center dark:bg-white/[0.03]">
                            <span x-text="selectedName" :class="selectedId ? 'text-gray-800 dark:text-white' : 'text-gray-400 dark:text-gray-500'">Pilih Pengguna</span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-transition class="absolute left-0 z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto p-2 space-y-2">
                            <input type="text" x-model="search" placeholder="Cari..." 
                                class="h-9 w-full rounded-md border border-gray-300 dark:border-gray-700 bg-transparent px-3 py-1.5 text-xs text-gray-800 dark:text-gray-200 focus:outline-hidden focus:ring-1 focus:ring-brand-500 focus:border-brand-500" 
                                @click.stop="">
                            <div class="space-y-1">
                                <template x-for="opt in filteredOptions" :key="opt.id">
                                    <button type="button" @click="selectOption(opt)" 
                                        class="w-full text-left px-3 py-2 rounded-md text-xs hover:bg-brand-55 dark:hover:bg-brand-500/10 hover:text-brand-600 dark:hover:text-brand-400 text-gray-700 dark:text-gray-300 block">
                                        <span x-text="opt.name"></span>
                                    </button>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="py-2 text-center text-xs text-gray-400 dark:text-gray-500">
                                    Tidak ditemukan data
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('user_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nomor Rekening<span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Contoh: 000003"
                        class="h-11 w-full rounded-lg border @error('account_number') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required />
                    @error('account_number')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Customer Name -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nama Nasabah<span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Contoh: GEMILANG"
                        class="h-11 w-full rounded-lg border @error('customer_name') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required />
                    @error('customer_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction Type -->
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Jenis Transaksi<span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="transaction_type" value="{{ old('transaction_type') }}" placeholder="Contoh: TELLER 1780060"
                        class="h-11 w-full rounded-lg border @error('transaction_type') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required />
                    @error('transaction_type')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description / Finding -->
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Deskripsi / Temuan<span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="4" placeholder="Jelaskan rincian temuan audit transaksi..."
                        class="w-full rounded-lg border @error('description') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Uploads -->
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        File Bukti (Opsional)
                    </label>
                    <input type="file" name="files[]" multiple
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-white/5 dark:file:text-white/80" />
                    <p class="mt-1 text-xs text-gray-400">Format yang diperbolehkan: JPG, PNG, PDF, Excel (XLSX, XLS, CSV). Ukuran maks: 5MB per file.</p>
                    @error('files')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    @error('files.*')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <a href="{{ route('audit.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
                    Kirim Temuan
                </button>
            </div>
        </form>
    </div>
  </div>
@endsection
