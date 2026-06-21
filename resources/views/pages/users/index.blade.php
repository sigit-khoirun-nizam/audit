@extends('layouts.app')

@section('content')
  <div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-white/90 sm:text-2xl">
                Manajemen Pengguna
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola kredensial pengguna, nomor telepon, kode, dan penugasan peran
            </p>
        </div>
        <div>
            <a href="{{ route('users.create') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 4.16699V15.8337M4.16669 10.0003H15.8334" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Buat Pengguna Baru
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-semibold uppercase text-gray-400 dark:border-gray-800">
                        <th class="py-3 px-4 w-12">No</th>
                        <th class="py-3 px-4">Nama</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Kode Pengguna</th>
                        <th class="py-3 px-4">Telepon</th>
                        <th class="py-3 px-4">Peran</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($users as $user)
                        <tr>
                            <td class="py-3.5 px-4 text-gray-500">{{ $loop->iteration + ($users->firstItem() - 1) }}</td>
                            <td class="py-3.5 px-4 font-medium">{{ $user->name }}</td>
                            <td class="py-3.5 px-4">{{ $user->email }}</td>
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $user->user_code ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-gray-500 dark:text-gray-400">{{ $user->phone ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 mr-1">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-3.5">
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 transition" title="Edit Pengguna">
                                        <svg class="w-5 h-5 text-gray-500 hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                                        </svg>
                                    </a>
                                    
                                    @if(Auth::id() !== $user->id)
                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" id="delete-form-{{ $user->id }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $user->id }}')" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 transition" title="Hapus Pengguna">
                                                <svg class="w-5 h-5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                Tidak ada pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
    function confirmDelete(userId) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d92d20',
            cancelButtonColor: '#475467',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            background: isDark ? '#1d2939' : '#ffffff',
            color: isDark ? '#f9fafb' : '#101828',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + userId).submit();
            }
        });
    }
</script>
@endpush
