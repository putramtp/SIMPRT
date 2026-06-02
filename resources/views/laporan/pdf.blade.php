<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Teknisi — {{ $laporan->task->title }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #1a1d23; background: #fff; }

    .page { padding: 32px 40px; }

    /* Header */
    .header { border-bottom: 2px solid #1565C0; padding-bottom: 12px; margin-bottom: 20px; }
    .header-top { overflow: hidden; }
    .header-brand { float: left; }
    .header-brand-name { font-size: 18pt; font-weight: bold; color: #1565C0; }
    .header-brand-sub { font-size: 9pt; color: #7a8099; margin-top: 2px; }
    .header-doc { float: right; text-align: right; }
    .header-doc-title { font-size: 13pt; font-weight: bold; color: #1a1d23; }
    .header-doc-no { font-size: 9pt; color: #7a8099; margin-top: 2px; }
    .clearfix { clear: both; }

    /* Section title */
    .section-title { font-size: 10pt; font-weight: bold; color: #1565C0; background: #E3F2FD; padding: 5px 10px; margin-bottom: 0; border-left: 3px solid #1565C0; }

    /* Info table */
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .info-table td { padding: 6px 10px; font-size: 10pt; border-bottom: 1px solid #e0e4ed; vertical-align: top; }
    .info-table td.key { width: 35%; color: #7a8099; font-weight: bold; }
    .info-table td.val { color: #1a1d23; }

    /* Description block */
    .desc-block { border: 1px solid #e0e4ed; padding: 12px 14px; font-size: 10pt; line-height: 1.7; margin-bottom: 18px; white-space: pre-wrap; }

    /* Signature section */
    .sig-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .sig-table td { width: 50%; padding: 10px; text-align: center; border: 1px solid #e0e4ed; }
    .sig-label { font-size: 9pt; font-weight: bold; color: #7a8099; margin-bottom: 8px; }
    .sig-img { max-height: 80px; max-width: 200px; }
    .sig-line { border-top: 1px solid #1a1d23; margin-top: 12px; padding-top: 4px; font-size: 9pt; }

    /* Photo */
    .photo-block { margin-bottom: 18px; }
    .photo-img { max-width: 100%; max-height: 320px; display: block; margin: 8px auto 0; border: 1px solid #e0e4ed; }

    /* Footer */
    .footer { border-top: 1px solid #e0e4ed; padding-top: 10px; margin-top: 24px; text-align: center; font-size: 8.5pt; color: #7a8099; }

    /* Status badge */
    .status-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 9pt; font-weight: bold; }
    .status-badge.submitted { background: #E3F2FD; color: #1565C0; }
    .status-badge.approved  { background: #EAF3DE; color: #2E7D32; }
    .status-badge.draft     { background: #f4f6fb; color: #7a8099; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-top">
            <div class="header-brand">
                <div class="header-brand-name">SIPRT</div>
                <div class="header-brand-sub">Sistem Informasi Penugasan &amp; Pelaporan Teknisi</div>
            </div>
            <div class="header-doc">
                <div class="header-doc-title">LAPORAN KERJA TEKNISI</div>
                <div class="header-doc-no">No. RPT-{{ str_pad($laporan->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    {{-- Task Info --}}
    <div class="section-title">Informasi Tugas</div>
    <table class="info-table">
        <tr>
            <td class="key">Judul Tugas</td>
            <td class="val">{{ $laporan->task->title }}</td>
        </tr>
        <tr>
            <td class="key">Customer</td>
            <td class="val">{{ $laporan->task->customer->name }}</td>
        </tr>
        <tr>
            <td class="key">Teknisi</td>
            <td class="val">{{ $laporan->teknisi->name }}</td>
        </tr>
        @if($laporan->task->due_date)
        <tr>
            <td class="key">Deadline</td>
            <td class="val">{{ $laporan->task->due_date->format('d F Y') }}</td>
        </tr>
        @endif
        <tr>
            <td class="key">Tanggal Laporan</td>
            <td class="val">{{ $laporan->created_at->format('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="key">Status</td>
            <td class="val">
                <span class="status-badge {{ $laporan->status }}">
                    {{ ucfirst($laporan->status) }}
                </span>
            </td>
        </tr>
    </table>

    {{-- Description --}}
    <div class="section-title">Deskripsi Pekerjaan</div>
    <div class="desc-block">{{ $laporan->description }}</div>

    {{-- Task description if any --}}
    @if($laporan->task->description)
    <div class="section-title">Deskripsi Tugas (dari Sales)</div>
    <div class="desc-block">{{ $laporan->task->description }}</div>
    @endif

    {{-- Template data --}}
    @if($laporan->template_data && $laporan->task->template && count($laporan->template_data))
    <div class="section-title">{{ $laporan->task->template->name }}</div>
    <table class="info-table">
        @foreach($laporan->task->template->fields as $section)
            @foreach($section['fields'] ?? [] as $field)
                @if(in_array($field['type'], ['photo','signature'])) @continue @endif
                @php $val = $laporan->template_data[$field['id']] ?? null; @endphp
                <tr>
                    <td class="key">{{ $field['label'] }}</td>
                    <td class="val">
                        @if($field['type'] === 'checkbox')
                            {{ ($val == '1' || $val === true) ? '✓ Ya' : '✗ Tidak' }}
                        @elseif($val !== null && $val !== '')
                            {{ $val }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
    </table>
    @endif

    {{-- Photos --}}
    @php $allPhotos = $laporan->photos ?? []; @endphp
    @if(count($allPhotos) > 0)
    <div class="section-title">Dokumentasi Foto</div>
    @foreach($allPhotos as $i => $photoPath)
    <div class="photo-block">
        <img class="photo-img" src="{{ public_path('storage/' . $photoPath) }}" alt="Foto {{ $i + 1 }}">
    </div>
    @endforeach
    @endif

    {{-- Signatures --}}
    @if($laporan->signature_tech || $laporan->signature_cust)
    <div class="section-title">Tanda Tangan</div>
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-label">Teknisi</div>
                @if($laporan->signature_tech)
                    <img class="sig-img" src="{{ $laporan->signature_tech }}" alt="TTD Teknisi">
                @else
                    <div style="height:80px;"></div>
                @endif
                <div class="sig-line">{{ $laporan->teknisi->name }}</div>
            </td>
            <td>
                <div class="sig-label">Customer / Penerima</div>
                @if($laporan->signature_cust)
                    <img class="sig-img" src="{{ $laporan->signature_cust }}" alt="TTD Customer">
                @else
                    <div style="height:80px;"></div>
                @endif
                <div class="sig-line">{{ $laporan->task->customer->name }}</div>
            </td>
        </tr>
    </table>
    @else
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-label">Teknisi</div>
                <div style="height:80px;"></div>
                <div class="sig-line">{{ $laporan->teknisi->name }}</div>
            </td>
            <td>
                <div class="sig-label">Customer / Penerima</div>
                <div style="height:80px;"></div>
                <div class="sig-line">{{ $laporan->task->customer->name }}</div>
            </td>
        </tr>
    </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh SIPRT pada {{ now()->format('d F Y, H:i') }} WIB.
        Laporan No. RPT-{{ str_pad($laporan->id, 5, '0', STR_PAD_LEFT) }}
    </div>

</div>
</body>
</html>
