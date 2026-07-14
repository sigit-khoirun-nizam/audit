<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: helvetica, sans-serif;
            color: #333333;
            font-size: 10pt;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 10pt;
            color: #4b5563;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 6px;
            vertical-align: top;
        }
        .info-label {
            width: 25%;
            font-weight: bold;
            color: #4b5563;
        }
        .info-value {
            width: 75%;
            color: #1f2937;
        }
        .description-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .status-badge {
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .status-pending {
            color: #b45309;
            background-color: #fef3c7;
        }
        .status-review {
            color: #1d4ed8;
            background-color: #dbeafe;
        }
        .status-revision {
            color: #b91c1c;
            background-color: #fee2e2;
        }
        .status-done {
            color: #15803d;
            background-color: #dcfce7;
        }
        .timeline-item {
            border-left: 2px solid #e5e7eb;
            padding-left: 10px;
            margin-bottom: 12px;
        }
        .timeline-header {
            font-weight: bold;
            font-size: 9pt;
            color: #4b5563;
        }
        .timeline-body {
            margin-top: 4px;
            color: #1f2937;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-cell {
            width: 50%;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
        .signature-line {
            width: 150px;
            border-bottom: 1px solid #333333;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="70%">
                    <span class="header-title">Sistem Informasi Audit Transaksi</span><br>
                    <span class="header-subtitle">Laporan Hasil Temuan & Tindak Lanjut Audit</span>
                </td>
                <td width="30%" align="right" style="font-size: 8pt; color: #6b7280;">
                    Dicetak: {{ date('d-m-Y H:i') }}<br>
                    Oleh: {{ Auth::user()->name }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Info Transaksi -->
    <div class="section-title">Informasi Audit #{{ $transaction->id }}</div>
    <table class="info-table" cellpadding="2">
        <tr>
            <td class="info-label">Kode Audit:</td>
            <td class="info-value" style="font-weight: bold; color: #3b82f6;">{{ $transaction->user_code }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Transaksi:</td>
            <td class="info-value">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Nomor Rekening:</td>
            <td class="info-value">{{ $transaction->account_number }}</td>
        </tr>
        <tr>
            <td class="info-label">Nama Nasabah:</td>
            <td class="info-value">{{ $transaction->customer_name }}</td>
        </tr>
        <tr>
            <td class="info-label">Jenis Transaksi:</td>
            <td class="info-value">{{ $transaction->transaction_type }}</td>
        </tr>
        <tr>
            <td class="info-label">User Diaudit:</td>
            <td class="info-value">{{ $transaction->user ? $transaction->user->name : '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Auditor / Pembuat:</td>
            <td class="info-value">{{ $transaction->creator ? $transaction->creator->name : 'Sistem' }}</td>
        </tr>
        <tr>
            <td class="info-label">Status Audit:</td>
            <td class="info-value">
                @if($transaction->status === 'PENDING')
                    <span class="status-badge status-pending">PENDING</span>
                @elseif($transaction->status === 'ON_REVIEW')
                    <span class="status-badge status-review">SEDANG DITINJAU</span>
                @elseif($transaction->status === 'REVISION')
                    <span class="status-badge status-revision">REVISI</span>
                @elseif($transaction->status === 'DONE')
                    <span class="status-badge status-done">SELESAI</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- Deskripsi Temuan -->
    <div class="section-title">Deskripsi / Temuan Audit</div>
    <div class="description-box">
        {!! nl2br(e($transaction->description)) !!}
    </div>

    <!-- Lampiran Auditor -->
    @if($transaction->files->count() > 0)
        <div class="section-title">Lampiran Auditor</div>
        <table width="100%" cellpadding="3" cellspacing="0" style="margin-bottom: 15px; border: 1px solid #e5e7eb;">
            <tr style="background-color: #f3f4f6; font-weight: bold; color: #4b5563;">
                <th width="10%" align="center">No</th>
                <th width="60%">Nama File</th>
                <th width="30%">Pengunggah</th>
            </tr>
            @foreach($transaction->files as $index => $file)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ $file->file_name }}</td>
                    <td>{{ $file->uploader ? $file->uploader->name : 'Auditor' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <!-- Bukti Respon & Riwayat -->
    <div class="section-title">Bukti Respon & Riwayat Penyelesaian</div>
    @if($transaction->responses->count() > 0)
        @foreach($transaction->responses as $index => $resp)
            <div class="timeline-item">
                <div class="timeline-header">
                    Bukti #{{ $index + 1 }} - Diajukan oleh {{ $resp->user ? $resp->user->name : 'User' }} pada {{ $resp->created_at->format('d-m-Y H:i') }}
                </div>
                <div class="timeline-body">
                    <strong>Catatan:</strong> {{ $resp->note }}
                    @if($resp->files->count() > 0)
                        <br><span style="font-size: 8pt; color: #6b7280;">File terlampir:
                        @foreach($resp->files as $file)
                            &bull; {{ $file->file_name }} &nbsp;
                        @endforeach
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div style="color: #6b7280; font-style: italic; margin-bottom: 15px;">Belum ada respon bukti yang dikirimkan.</div>
    @endif

    <!-- Riwayat Diskusi / Komentar -->
    @if($transaction->comments->count() > 0)
        <div class="section-title">Riwayat Diskusi / Komentar</div>
        @foreach($transaction->comments as $comment)
            <div style="margin-bottom: 8px; border-bottom: 1px dashed #e5e7eb; padding-bottom: 6px;">
                <div style="font-weight: bold; font-size: 9pt; color: #4b5563;">
                    {{ $comment->user ? $comment->user->name : 'Sistem' }} - <span style="font-weight: normal; font-size: 8pt; color: #9ca3af;">{{ $comment->created_at->format('d-m-Y H:i') }}</span>
                </div>
                <div style="margin-top: 2px;">{{ $comment->message }}</div>
            </div>
        @endforeach
    @endif

    <!-- Signature Block -->
    <table class="signature-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="signature-cell">
                <span style="color: #4b5563;">Dibuat & Diverifikasi Oleh,</span>
                <div class="signature-space"></div>
                <div class="signature-line"></div>
                <span style="font-weight: bold; color: #1f2937;">Auditor Cabang</span>
            </td>
            <td class="signature-cell">
                <span style="color: #4b5563;">Mengetahui & Menyetujui,</span>
                <div class="signature-space"></div>
                <div class="signature-line"></div>
                <span style="font-weight: bold; color: #1f2937;">Super Admin / Pimpinan</span>
            </td>
        </tr>
    </table>

</body>
</html>
