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
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tanggal Transaksi<span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}"
                        class="h-11 w-full rounded-lg border @error('transaction_date') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required />
                    @error('transaction_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assigned User -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tugaskan ke Pengguna / Teller<span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" class="h-11 w-full rounded-lg border @error('user_id') border-red-500 @else border-gray-300 @enderror bg-white px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required>
                        <option value="">Pilih Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} &mdash; {{ $u->user_code }}
                            </option>
                        @endforeach
                    </select>
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
                    <p class="mt-1 text-xs text-gray-400">Format yang diperbolehkan: JPG, PNG, PDF. Ukuran maks: 5MB per file.</p>
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
