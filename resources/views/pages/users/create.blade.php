@extends('layouts.app')

@section('content')
  <div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-white/90 sm:text-2xl">
                Buat Profil Pengguna
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Atur kredensial baru, kode pengguna, dan tetapkan peran
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <!-- Name -->
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nama Lengkap<span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: John Doe"
                        class="h-11 w-full rounded-lg border @error('name') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required />
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Alamat Email<span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: user@gmail.com"
                        class="h-11 w-full rounded-lg border @error('email') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required />
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Kata Sandi<span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" placeholder="Masukkan kata sandi yang aman"
                        class="h-11 w-full rounded-lg border @error('password') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required />
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nomor Telepon (WhatsApp)
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890"
                        class="h-11 w-full rounded-lg border @error('phone') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Code -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Kode Pengguna (Pengidentifikasi Unik)
                    </label>
                    <input type="text" name="user_code" value="{{ old('user_code') }}" placeholder="Contoh: 17800T60"
                        class="h-11 w-full rounded-lg border @error('user_code') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                    @error('user_code')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Roles Checkboxes -->
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tetapkan Peran
                    </label>
                    <div class="flex flex-wrap gap-4">
                        @foreach($roles as $role)
                            <label class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-medium text-gray-700 cursor-pointer dark:border-gray-800 dark:bg-white/5 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                    class="rounded border-gray-300 text-brand-500 focus:ring-brand-500/20"
                                    {{ is_array(old('roles')) && in_array($role->name, old('roles')) ? 'checked' : '' }} />
                                {{ $role->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <a href="{{ route('users.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
                    Buat Pengguna
                </button>
            </div>
        </form>
    </div>
  </div>
@endsection
