<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 10pt; line-height: 1.4; color: #000; }
        h3, h4 { margin: 5px 0; }
        .section-title { font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 3px; margin-top: 15px; margin-bottom: 10px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 4px; border: 1px solid #ccc; vertical-align: top; }
        .info-label { width: 30%; font-weight: bold; background-color: #f5f5f5; }
        .info-value { width: 70%; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; text-align: left; }
        .data-table th { background-color: #f5f5f5; border: 1px solid #ccc; padding: 5px; font-weight: bold; }
        .data-table td { border: 1px solid #ccc; padding: 5px; vertical-align: top; }
        .desc-box { border: 1px solid #ccc; padding: 10px; background-color: #fafafa; margin-bottom: 15px; }
        .signature-table { width: 100%; margin-top: 40px; text-align: center; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>

    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px;">
        <tr>
            <td width="70%">
                <h3>Laporan Detail Temuan Audit Transaksi</h3>
            </td>
            <td width="30%" align="right" style="font-size: 8pt;">
                Tanggal Cetak: {{ date('d-m-Y H:i') }}<br>
                Dicetak Oleh: {{ Auth::user()->name }}
            </td>
        </tr>
    </table>

    <div class="section-title">Informasi Transaksi</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Kode Audit</td>
            <td class="info-value">{{ $transaction->user_code }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Transaksi</td>
            <td class="info-value">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Nomor Rekening</td>
            <td class="info-value">{{ $transaction->account_number }}</td>
        </tr>
        <tr>
            <td class="info-label">Nama Nasabah</td>
            <td class="info-value">{{ $transaction->customer_name }}</td>
        </tr>
        <tr>
            <td class="info-label">Jenis Transaksi</td>
            <td class="info-value">{{ $transaction->transaction_type }}</td>
        </tr>
        <tr>
            <td class="info-label">User / Teller</td>
            <td class="info-value">{{ $transaction->user ? $transaction->user->name : '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Auditor</td>
            <td class="info-value">{{ $transaction->creator ? $transaction->creator->name : '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Status</td>
            <td class="info-value">{{ $transaction->status }}</td>
        </tr>
    </table>

    <div class="section-title">Deskripsi Temuan</div>
    <div class="desc-box">
        {!! nl2br(e($transaction->description)) !!}
    </div>

    @if($transaction->files->count() > 0)
        <div class="section-title">Lampiran Auditor</div>
        <table class="data-table">
            <tr>
                <th width="10%">No</th>
                <th width="60%">Nama File</th>
                <th width="30%">Pengunggah</th>
            </tr>
            @foreach($transaction->files as $index => $file)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ $file->file_name }}</td>
                    <td>{{ $file->uploader ? $file->uploader->name : 'Auditor' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <div class="section-title">Tindak Lanjut & Respon</div>
    @if($transaction->responses->count() > 0)
        <table class="data-table">
            <tr>
                <th width="10%">No</th>
                <th width="20%">Tanggal</th>
                <th width="30%">Pengguna</th>
                <th width="40%">Catatan</th>
            </tr>
            @foreach($transaction->responses as $index => $resp)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ $resp->created_at->format('d-m-Y') }}</td>
                    <td>{{ $resp->user ? $resp->user->name : '-' }}</td>
                    <td>{{ $resp->note }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <div style="font-style: italic; margin-bottom: 15px;">Belum ada respon tindak lanjut.</div>
    @endif

    @if($transaction->comments->count() > 0)
        <div class="section-title">Catatan Diskusi</div>
        <table class="data-table">
            @foreach($transaction->comments as $comment)
                <tr>
                    <td width="30%"><b>{{ $comment->user ? $comment->user->name : '-' }}</b><br><small>{{ $comment->created_at->format('d-m-Y H:i') }}</small></td>
                    <td width="70%">{{ $comment->message }}</td>
                </tr>
            @endforeach
        </table>
    @endif

</body>
</html>
