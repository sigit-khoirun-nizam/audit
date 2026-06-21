@extends('layouts.app')

@section('content')
  @php
      $user = Auth::user();
      $isAuditor = $user->hasRole('Auditor') || $user->hasRole('Superadmin');
      $isAssignedUser = $user->id === $transaction->user_id;
  @endphp

  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('audit.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white/90 sm:text-2xl">
                    Detail Audit #{{ $transaction->id }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Kode Audit: <span class="font-semibold text-brand-500">{{ $transaction->user_code }}</span>
                </p>
            </div>
        </div>
        <div>
            @if($transaction->status === 'PENDING')
                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800 dark:bg-amber-500/10 dark:text-amber-500">PENDING</span>
            @elseif($transaction->status === 'ON_REVIEW')
                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-800 dark:bg-blue-500/10 dark:text-blue-500">SEDANG DITINJAU</span>
            @elseif($transaction->status === 'REVISION')
                <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-sm font-semibold text-red-800 dark:bg-red-500/10 dark:text-red-500">REVISI</span>
            @elseif($transaction->status === 'DONE')
                <span class="inline-flex rounded-full bg-success-50 px-3 py-1 text-sm font-semibold text-success-800 dark:bg-success-500/10 dark:text-success-500">SELESAI</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left: Transaction Info & Attachments -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Details Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 border-b border-gray-100 pb-3 dark:border-gray-800">
                    Informasi Transaksi
                </h3>
                
                <div class="mt-4 grid grid-cols-1 gap-y-4 gap-x-6 sm:grid-cols-2">
                    <div>
                        <span class="text-xs font-semibold uppercase text-gray-400">Tanggal Transaksi</span>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $transaction->transaction_date }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase text-gray-400">Nomor Rekening</span>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $transaction->account_number }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase text-gray-400">Nama Nasabah</span>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $transaction->customer_name }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase text-gray-400">Jenis Transaksi</span>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $transaction->transaction_type }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase text-gray-400">Pengguna yang Ditugaskan</span>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $transaction->user ? $transaction->user->name : 'Tidak Ada' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase text-gray-400">Auditor / Pembuat</span>
                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $transaction->creator ? $transaction->creator->name : 'Sistem' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-xs font-semibold uppercase text-gray-400">Deskripsi / Temuan</span>
                        <div class="mt-1.5 rounded-xl bg-gray-50 p-4 dark:bg-white/5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                            {{ $transaction->description }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auditor Attachments Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 border-b border-gray-100 pb-3 dark:border-gray-800">
                    Lampiran Auditor
                </h3>
                
                <div class="mt-4">
                    @if($transaction->files->count() > 0)
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach($transaction->files as $file)
                                <div class="flex flex-col justify-between rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><polyline points="10 9 9 9 8 9"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-800 dark:text-white/90" title="{{ $file->file_name }}">
                                                {{ $file->file_name }}
                                            </p>
                                            <p class="text-xs text-gray-400">Diunggah oleh {{ $file->uploader ? $file->uploader->name : 'Tidak Ada' }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center justify-end gap-2 border-t border-gray-200/50 pt-2 dark:border-gray-800">
                                        @if(preg_match('/\.(jpg|jpeg|png)$/i', $file->file_name))
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="text-xs font-semibold text-gray-500 hover:text-gray-700 dark:hover:text-white">Lihat Gambar</a>
                                        @endif
                                        <a href="{{ asset('storage/' . $file->file_path) }}" download class="text-xs font-bold text-brand-500 hover:text-brand-600">Unduh</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Tidak ada lampiran yang diunggah oleh auditor.</p>
                    @endif
                </div>
            </div>

            <!-- Discussion timeline -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 border-b border-gray-100 pb-3 dark:border-gray-800">
                    Diskusi & Log Aktivitas
                </h3>

                <!-- Comments Timeline -->
                <div class="mt-6 space-y-4 max-h-96 overflow-y-auto pr-2">
                    @forelse($transaction->comments as $comment)
                        <div class="flex gap-3 {{ $comment->user_id === Auth::id() ? 'justify-end' : '' }}">
                            <div class="max-w-[80%] rounded-2xl p-4 {{ $comment->user_id === Auth::id() ? 'bg-brand-500 text-white rounded-tr-none' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300 rounded-tl-none' }}">
                                <p class="text-xs font-bold {{ $comment->user_id === Auth::id() ? 'text-brand-100' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $comment->user ? $comment->user->name : 'Tidak Ada' }}
                                </p>
                                <p class="mt-1 text-sm leading-relaxed whitespace-pre-line">{{ $comment->message }}</p>
                                <span class="mt-2 block text-right text-[10px] opacity-70">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic text-center py-4">Belum ada komentar.</p>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <form method="POST" action="{{ route('audit.comment.store', $transaction->id) }}" class="mt-6 border-t border-gray-100 pt-4 dark:border-gray-800">
                    @csrf
                    <input type="hidden" name="audit_transaction_id" value="{{ $transaction->id }}" />
                    <div>
                        <textarea name="message" rows="2" placeholder="Kirim komentar/pertanyaan..."
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" required></textarea>
                    </div>
                    <div class="mt-2 flex justify-end">
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">
                            Kirim Komentar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side: Actions & Proof Submissions -->
        <div class="space-y-6">
            <!-- Auditor Status Controls Panel -->
            @if($isAuditor)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 border-b border-gray-100 pb-3 dark:border-gray-800">
                        Kontrol Auditor
                    </h3>
                    
                    <form method="POST" action="{{ route('audit.update', $transaction->id) }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase text-gray-400">Ubah Status</label>
                            <select name="status" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="PENDING" {{ $transaction->status === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                <option value="ON_REVIEW" {{ $transaction->status === 'ON_REVIEW' ? 'selected' : '' }}>SEDANG DITINJAU</option>
                                <option value="REVISION" {{ $transaction->status === 'REVISION' ? 'selected' : '' }}>REVISI</option>
                                <option value="DONE" {{ $transaction->status === 'DONE' ? 'selected' : '' }}>SELESAI</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-brand-500 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition">
                            Perbarui Status
                        </button>
                    </form>
                </div>
            @endif

            <!-- User Response Upload Panel -->
            @if($isAssignedUser && $transaction->status !== 'DONE')
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 border-b border-gray-100 pb-3 dark:border-gray-800">
                        Kirim Bukti Respon
                    </h3>
                    
                    <form method="POST" action="{{ route('audit.response.store', $transaction->id) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="audit_transaction_id" value="{{ $transaction->id }}" />
                        
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase text-gray-400">Catatan Respon</label>
                            <textarea name="note" rows="3" placeholder="Tulis detail respon, penjelasan, atau catatan koreksi..."
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90 focus:outline-hidden focus:ring-3" required></textarea>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase text-gray-400">File Bukti</label>
                            <input type="file" name="files[]" multiple class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 dark:file:bg-white/5 dark:file:text-white" required />
                            <p class="mt-1 text-[10px] text-gray-400">Format JPG, PNG, PDF diperbolehkan. Maks 5MB per file.</p>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-success-500 py-2.5 text-sm font-semibold text-white hover:bg-success-600 transition shadow-theme-xs">
                            Kirim Bukti & Set Tinjauan
                        </button>
                    </form>
                </div>
            @endif

            <!-- Past Response History Panel -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 border-b border-gray-100 pb-3 dark:border-gray-800">
                    Bukti & Riwayat
                </h3>
                
                <div class="mt-4 space-y-4 max-h-[350px] overflow-y-auto pr-1">
                    @forelse($transaction->responses as $index => $resp)
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/5 text-sm">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-bold text-gray-800 dark:text-white/80">Bukti #{{ $index + 1 }}</span>
                                <span class="text-[10px] text-gray-400">{{ $resp->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $resp->note }}</p>
                            
                            <!-- Response Files list -->
                            @if($resp->files->count() > 0)
                                <div class="mt-3 space-y-1">
                                    <p class="text-xs font-bold text-gray-400">File:</p>
                                    @foreach($resp->files as $file)
                                        <div class="flex items-center justify-between gap-2 text-xs">
                                            <span class="truncate text-gray-600 dark:text-gray-400" title="{{ $file->file_name }}">
                                                &bull; {{ $file->file_name }}
                                            </span>
                                            <a href="{{ asset('storage/' . $file->file_path) }}" download class="text-brand-500 font-bold hover:underline shrink-0">Unduh</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic text-center py-4">Belum ada respon bukti yang dikirimkan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
  </div>
@endsection
