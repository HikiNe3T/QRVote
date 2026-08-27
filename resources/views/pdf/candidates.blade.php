<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lanyard Kandidat - {{ $event->name }}</title>
    <style>
        /* Margin dirapatkan agar 4 kartu muat dalam 1 halaman A4 */
        @page {
            size: A4;
            margin: 10mm 10mm 10mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 12px;
            line-height: 1.45;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .page {
            padding: 0;
        }

        /* Spacer yang diulang di SETIAP halaman (thead/tfoot table utama)
           supaya halaman ke-2 dst tetap punya jarak atas & bawah. */
        .page-spacer-top {
            height: 2mm;
        }

        .page-spacer-bottom {
            height: 8mm;
        }

        /* ============ GRID KARTU ============ */
        .card-grid {
            width: 190mm;
            margin: 0 auto;
        }

        .content-wrapper {
            padding: 0;
        }

        .card-grid td {
            width: 95mm;
            text-align: center;
            padding: 0 0 4mm 0;
            vertical-align: top;
        }

        .card {
            width: 90mm;
            height: 110mm;
            margin: 0 auto;
            border: 1px dashed #cbd5e1; /* garis potong lanyard */
            border-radius: 10px;
            background: #ffffff;
            page-break-inside: avoid;
            overflow: hidden;
            text-align: center;
        }

        .card-inner {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin: 1.5mm;
            height: 107mm;
            overflow: hidden;
        }

        .card-accent {
            height: 4mm;
            background: #2563eb;
        }

        .card-accent-thin {
            height: 1mm;
            background: #93c5fd;
        }

        .card-body {
            padding: 3mm 4mm 2mm 4mm;
        }

        .card-number {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.6px;
            padding: 2px 10px;
            margin-bottom: 2.5mm;
        }

        /* --- Foto / avatar inisial (diperbesar agar muat menampilkan foto kandidat) --- */
        .card-photo-frame {
            width: 26mm;
            height: 26mm;
            border-radius: 50%;
            margin: 0 auto 2.5mm auto;
            overflow: hidden;
            border: 2px solid #bfdbfe;
        }

        .card-photo-frame img {
            width: 26mm;
            height: 26mm;
            object-fit: cover;
        }

        .card-avatar {
            width: 26mm;
            height: 26mm;
            border-radius: 50%;
            background: #2563eb;
            color: #ffffff;
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 0.5px;
            line-height: 26mm;
            margin: 0 auto 2.5mm auto;
        }

        .card-name {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-position {
            display: inline-block;
            max-width: 70mm;
            font-size: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #64748b;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 2px 8px;
            margin-bottom: 2mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 0 auto 2mm auto;
            width: 56mm;
        }

        /* --- QR: elemen paling menonjol di kartu --- */
        .card-qr {
            width: 33mm;
            height: 33mm;
            background: #ffffff;
            border: 2px solid #2563eb;
            border-radius: 10px;
            padding: 1.5mm;
            margin: 0 auto;
        }

        .card-qr img {
            width: 29mm;
            height: 29mm;
        }

        .card-qr-empty {
            width: 29mm;
            height: 29mm;
            line-height: 29mm;
            text-align: center;
            color: #cbd5e1;
            font-size: 9px;
        }

        .card-qr-label {
            font-size: 8.5px;
            color: #1e293b;
            font-weight: bold;
            letter-spacing: 0.6px;
            padding-top: 1.5mm;
        }

        .card-qr-label .card-qr-sub {
            display: block;
            font-size: 7px;
            color: #94a3b8;
            font-weight: normal;
            letter-spacing: 0.3px;
            padding-top: 0.8mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ============ FOOTER (informasi event) ============ */
        .footer {
            margin-top: 4mm;
            border-top: 2px solid #3b82f6;
            padding-top: 3mm;
            text-align: center;
            font-size: 9px;
            color: #475569;
        }

        .footer-event-name {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 0.4px;
            margin-bottom: 1mm;
        }

        .footer-meta {
            font-size: 8.5px;
            color: #64748b;
            letter-spacing: 0.3px;
        }

        .footer-brand {
            margin-top: 2mm;
            font-size: 8px;
            color: #94a3b8;
            letter-spacing: 0.4px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="content-wrapper">
        <table class="card-grid">
            <thead>
                <tr><td class="page-spacer-top" colspan="2"></td></tr>
            </thead>
            <tfoot>
                <tr><td class="page-spacer-bottom" colspan="2"></td></tr>
            </tfoot>
            <tbody>
            @foreach($candidates->chunk(2) as $row)
            <tr @if($loop->iteration % 2 === 0 && !$loop->last) style="page-break-after: always;" @endif>

                @foreach($row as $candidate)
                <td>
                    <div class="card">
                        <div class="card-inner">
                            <div class="card-accent"></div>
                            <div class="card-accent-thin"></div>
                            <div class="card-body">

                                <div class="card-number">NO. {{ $candidate->number }}</div>

                                @php
                                    $photoPath = $candidate->photo
                                        ? storage_path('app/public/candidates/' . $candidate->photo)
                                        : null;
                                    $photoBase64 = null;
                                    if ($photoPath && file_exists($photoPath)) {
                                        $ext  = strtolower(pathinfo($candidate->photo, PATHINFO_EXTENSION));
                                        $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';
                                        $photoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
                                    }

                                    // Inisial dipakai sebagai foto profil sementara
                                    // selama kandidat belum mengunggah foto.
                                    $nameParts = preg_split('/\s+/', trim($candidate->name));
                                    $initials  = strtoupper(
                                        mb_substr($nameParts[0] ?? '', 0, 1) .
                                        mb_substr($nameParts[1] ?? '', 0, 1)
                                    );
                                    if ($initials === '') {
                                        $initials = '?';
                                    }
                                @endphp

                                @if($photoBase64)
                                    <div class="card-photo-frame">
                                        <img src="{{ $photoBase64 }}" alt="{{ $candidate->name }}">
                                    </div>
                                @else
                                    <div class="card-avatar">{{ $initials }}</div>
                                @endif

                                <div class="card-name">{{ $candidate->name }}</div>
                                @if($candidate->position)
                                    <div class="card-position">{{ $candidate->position }}</div>
                                @endif

                                <div class="card-divider"></div>

                                <div class="card-qr">
                                    @if(!empty($qrBase64[$candidate->id]))
                                        <img src="data:image/png;base64,{{ $qrBase64[$candidate->id] }}" alt="QR Kandidat {{ $candidate->name }}">
                                    @else
                                        <div class="card-qr-empty">QR tidak tersedia</div>
                                    @endif
                                </div>
                                <div class="card-qr-label">
                                    SCAN UNTUK VOTING
                                    <span class="card-qr-sub">VoteQR &middot; {{ $event->name }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </td>
                @endforeach
                @if($row->count() < 2)
                <td></td>
                @endif
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="footer-event-name">{{ $event->name }}</div>
        <div class="footer-meta">
            {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
            @if($event->end_date)
                - {{ \Carbon\Carbon::parse($event->end_date)->format('d M Y') }}
            @endif
            &nbsp;&middot;&nbsp; {{ $candidates->count() }} Kandidat
        </div>
        <div class="footer-brand">VoteQR - Sistem Voting Digital &nbsp;|&nbsp; Dicetak {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</div>
    </div>
</div>
</body>
</html>
