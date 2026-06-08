<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Kerja Teknisi — {{ $laporan->task->title }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10.5pt; color: #1a1d23; background: #fff; }

@page { margin: 0; size: A4 portrait; }

.page {
    padding: 0 0 60px 0;
    position: relative;
    min-height: 100%;
}

/* ── Top accent bar ── */
.topbar { background: #1565C0; height: 6px; width: 100%; }

/* ── Header / Letterhead ── */
.letterhead {
    padding: 20px 40px 16px 40px;
    border-bottom: 1px solid #dde2ed;
}
.lh-table { width: 100%; border-collapse: collapse; }
.lh-brand { vertical-align: top; width: 55%; }
.lh-docinfo { vertical-align: top; text-align: right; width: 45%; }

.brand-name { font-size: 22pt; font-weight: bold; color: #1565C0; letter-spacing: 1px; }
.brand-tagline { font-size: 8.5pt; color: #7a8099; margin-top: 2px; }
.brand-contact { font-size: 8pt; color: #9aa0b0; margin-top: 8px; line-height: 1.6; }

.doc-title { font-size: 13pt; font-weight: bold; color: #1a1d23; text-transform: uppercase; letter-spacing: 0.5px; }
.doc-no { font-size: 9pt; color: #1565C0; font-weight: bold; margin-top: 4px; }
.doc-date { font-size: 8.5pt; color: #7a8099; margin-top: 3px; }
.doc-status { margin-top: 8px; }

/* ── Body ── */
.body { padding: 24px 40px 0 40px; }

/* ── Section heading ── */
.sec { margin-bottom: 0; }
.sec-head {
    font-size: 8pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #fff;
    background: #1565C0;
    padding: 5px 10px;
    border-radius: 2px 2px 0 0;
}
.sec-body {
    border: 1px solid #dde2ed;
    border-top: none;
    margin-bottom: 20px;
}

/* ── Info grid (two columns) ── */
.grid-table { width: 100%; border-collapse: collapse; }
.grid-table td { padding: 7px 12px; font-size: 9.5pt; vertical-align: top; }
.grid-table tr:not(:last-child) td { border-bottom: 1px solid #f0f2f7; }
.gt-k { width: 20%; color: #7a8099; font-weight: bold; white-space: nowrap; }
.gt-v { width: 30%; color: #1a1d23; }
.gt-sep { width: 1%; color: #dde2ed; text-align: center; }

/* ── Divider between two-column rows ── */
.col-divider { border-left: 1px solid #f0f2f7; }

/* ── Description block ── */
.desc-pad { padding: 12px 14px; font-size: 10pt; line-height: 1.75; color: #1a1d23; white-space: pre-wrap; }

/* ── Template data table ── */
.tpl-table { width: 100%; border-collapse: collapse; }
.tpl-table tr:nth-child(even) td { background: #f8f9fc; }
.tpl-table td { padding: 6px 12px; font-size: 9.5pt; vertical-align: top; border-bottom: 1px solid #f0f2f7; }
.tpl-k { width: 35%; color: #5a6070; font-weight: bold; }
.tpl-v { color: #1a1d23; }

/* ── Photo grid ── */
.photo-table { width: 100%; border-collapse: collapse; }
.photo-cell { padding: 10px; text-align: center; vertical-align: top; }
.photo-cell img { max-width: 100%; max-height: 280px; border: 1px solid #dde2ed; border-radius: 3px; }
.photo-caption { font-size: 8pt; color: #9aa0b0; margin-top: 5px; }

/* ── Signature block ── */
.sig-table { width: 100%; border-collapse: collapse; }
.sig-cell { width: 50%; padding: 16px 20px 14px; text-align: center; vertical-align: top; }
.sig-cell:first-child { border-right: 1px solid #dde2ed; }
.sig-role { font-size: 8pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #7a8099; margin-bottom: 10px; }
.sig-img-wrap { height: 90px; display: table-cell; vertical-align: middle; text-align: center; width: 100%; }
.sig-img { max-height: 85px; max-width: 220px; }
.sig-line { border-top: 1px solid #1a1d23; margin: 10px 20px 0; }
.sig-name { font-size: 9.5pt; font-weight: bold; margin-top: 5px; color: #1a1d23; }
.sig-sub { font-size: 8pt; color: #7a8099; margin-top: 2px; }
.sig-date { font-size: 8pt; color: #9aa0b0; margin-top: 4px; }

/* ── Status badge ── */
.badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 8.5pt; font-weight: bold; }
.badge-submitted { background: #E3F2FD; color: #1565C0; border: 1px solid #BBDEFB; }
.badge-approved  { background: #EAF3DE; color: #2E7D32; border: 1px solid #C8E6C9; }
.badge-draft     { background: #FFF8E1; color: #E65100; border: 1px solid #FFE082; }

/* ── Priority badge ── */
.prio-high   { color: #c62828; font-weight: bold; }
.prio-medium { color: #E65100; font-weight: bold; }
.prio-low    { color: #2E7D32; font-weight: bold; }

/* ── Footer ── */
.footer-wrap {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    border-top: 2px solid #1565C0;
    background: #f8f9fc;
    padding: 7px 40px;
}
.footer-table { width: 100%; border-collapse: collapse; }
.footer-left  { font-size: 7.5pt; color: #9aa0b0; vertical-align: middle; }
.footer-right { font-size: 7.5pt; color: #9aa0b0; text-align: right; vertical-align: middle; }
.footer-bold  { font-weight: bold; color: #7a8099; }
</style>
</head>
<body>
<div class="page">

    {{-- Top accent bar --}}
    <div class="topbar"></div>

    {{-- Letterhead --}}
    <div class="letterhead">
        <table class="lh-table">
            <tr>
                <td class="lh-brand">
                    <div class="brand-name">SIPRT</div>
                    <div class="brand-tagline">Sistem Informasi Penugasan &amp; Pelaporan Teknisi</div>
                    <div class="brand-contact">
                        Laporan dibuat otomatis &bull; {{ now()->format('d F Y') }}
                    </div>
                </td>
                <td class="lh-docinfo">
                    <div class="doc-title">Laporan Kerja Teknisi</div>
                    <div class="doc-no">No. RPT-{{ str_pad($laporan->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="doc-date">Dibuat: {{ $laporan->created_at->format('d F Y, H:i') }} WIB</div>
                    <div class="doc-status">
                        @if($laporan->status === 'submitted')
                            <span class="badge badge-submitted">&#10003; Terkirim</span>
                        @elseif($laporan->status === 'approved')
                            <span class="badge badge-approved">&#10003; Disetujui</span>
                        @else
                            <span class="badge badge-draft">&#8635; Draft</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Body --}}
    <div class="body">

        {{-- Section 1: Informasi Umum --}}
        <div class="sec">
            <div class="sec-head">Informasi Umum</div>
            <div class="sec-body">
                <table class="grid-table">
                    {{-- Row 1: Judul | Tanggal Laporan --}}
                    <tr>
                        <td class="gt-k">Judul Tugas</td>
                        <td class="gt-v" colspan="3"><strong>{{ $laporan->task->title }}</strong></td>
                    </tr>
                    {{-- Row 2: Customer | Deadline --}}
                    <tr>
                        <td class="gt-k">Customer</td>
                        <td class="gt-v">{{ $laporan->task->customer->name }}</td>
                        <td class="gt-k col-divider">Deadline</td>
                        <td class="gt-v">
                            @if($laporan->task->due_date)
                                {{ $laporan->task->due_date->format('d F Y') }}
                            @else
                                <span style="color:#9aa0b0;">—</span>
                            @endif
                        </td>
                    </tr>
                    {{-- Row 3: Teknisi Pelaksana | Prioritas --}}
                    <tr>
                        <td class="gt-k">Teknisi Pelaksana</td>
                        <td class="gt-v">
                            @if($laporan->task->assignees && $laporan->task->assignees->count())
                                {{ $laporan->task->assignees->pluck('name')->join(', ') }}
                            @else
                                {{ $laporan->teknisi->name }}
                            @endif
                        </td>
                        <td class="gt-k col-divider">Prioritas</td>
                        <td class="gt-v">
                            @php $prio = $laporan->task->priority ?? null; @endphp
                            @if($prio === 'high')
                                <span class="prio-high">&#9650; Tinggi</span>
                            @elseif($prio === 'medium')
                                <span class="prio-medium">&#9654; Sedang</span>
                            @elseif($prio === 'low')
                                <span class="prio-low">&#9660; Rendah</span>
                            @else
                                <span style="color:#9aa0b0;">—</span>
                            @endif
                        </td>
                    </tr>
                    {{-- Row 4: Dibuat oleh | Status --}}
                    <tr>
                        <td class="gt-k">Dibuat Laporan Oleh</td>
                        <td class="gt-v">{{ $laporan->teknisi->name }}</td>
                        <td class="gt-k col-divider">Status Laporan</td>
                        <td class="gt-v">
                            @if($laporan->status === 'submitted')
                                <span class="badge badge-submitted">Terkirim</span>
                            @elseif($laporan->status === 'approved')
                                <span class="badge badge-approved">Disetujui</span>
                            @else
                                <span class="badge badge-draft">Draft</span>
                            @endif
                        </td>
                    </tr>
                    {{-- Customer address --}}
                    @if($laporan->task->customer->address || $laporan->task->customer->phone || $laporan->task->customer->email)
                    <tr>
                        <td class="gt-k">Kontak Customer</td>
                        <td class="gt-v" colspan="3" style="color:#5a6070; font-size:9pt; line-height:1.6;">
                            @if($laporan->task->customer->address)
                                {{ $laporan->task->customer->address }}<br>
                            @endif
                            @if($laporan->task->customer->phone)
                                Tel: {{ $laporan->task->customer->phone }}
                            @endif
                            @if($laporan->task->customer->email)
                                @if($laporan->task->customer->phone) &nbsp;&bull;&nbsp; @endif
                                {{ $laporan->task->customer->email }}
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Section 2: Deskripsi Pekerjaan --}}
        <div class="sec">
            <div class="sec-head">Deskripsi Pekerjaan</div>
            <div class="sec-body">
                <div class="desc-pad">{{ $laporan->description ?: '—' }}</div>
            </div>
        </div>

        {{-- Section 3: Deskripsi Tugas (from sales) --}}
        @if($laporan->task->description)
        <div class="sec">
            <div class="sec-head">Instruksi Tugas (dari Sales)</div>
            <div class="sec-body">
                <div class="desc-pad">{{ $laporan->task->description }}</div>
            </div>
        </div>
        @endif

        {{-- Section 4: Template / Form Fields --}}
        @if($laporan->template_data && $laporan->task->template && count($laporan->template_data))
        <div class="sec">
            <div class="sec-head">Formulir: {{ $laporan->task->template->name }}</div>
            <div class="sec-body">
                <table class="tpl-table">
                    @foreach($laporan->task->template->fields as $section)
                        @if(!empty($section['title']))
                        <tr>
                            <td colspan="2" style="background:#f0f4fb; font-size:8.5pt; font-weight:bold; color:#1565C0; padding:5px 12px; border-bottom:1px solid #dde2ed;">
                                {{ $section['title'] }}
                            </td>
                        </tr>
                        @endif
                        @foreach($section['fields'] ?? [] as $field)
                            @if(in_array($field['type'], ['photo','signature'])) @continue @endif
                            @php $val = $laporan->template_data[$field['id']] ?? null; @endphp
                            <tr>
                                <td class="tpl-k">{{ $field['label'] }}</td>
                                <td class="tpl-v">
                                    @if($field['type'] === 'checkbox')
                                        @if($val == '1' || $val === true)
                                            <span style="color:#2E7D32; font-weight:bold;">&#10003; Ya</span>
                                        @else
                                            <span style="color:#c62828;">&#10007; Tidak</span>
                                        @endif
                                    @elseif($val !== null && $val !== '')
                                        {{ $val }}
                                    @else
                                        <span style="color:#9aa0b0;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            </div>
        </div>
        @endif

        {{-- Section 5: Dokumentasi Foto --}}
        @php $allPhotos = $laporan->photos ?? []; @endphp
        @if(count($allPhotos) > 0)
        <div class="sec">
            <div class="sec-head">Dokumentasi Foto ({{ count($allPhotos) }} foto)</div>
            <div class="sec-body">
                @php $chunks = array_chunk($allPhotos, 2); @endphp
                @foreach($chunks as $row)
                <table class="photo-table">
                    <tr>
                        @foreach($row as $i => $photoPath)
                        @php $globalIdx = array_search($photoPath, $allPhotos); @endphp
                        <td class="photo-cell" style="width:{{ count($row) === 1 ? '60%' : '50%' }}; {{ count($row) === 1 ? 'margin:0 auto;' : '' }}">
                            <img src="{{ public_path('storage/' . $photoPath) }}" alt="Foto {{ $globalIdx + 1 }}">
                            <div class="photo-caption">Foto {{ $globalIdx + 1 }}</div>
                        </td>
                        @endforeach
                        @if(count($row) === 1)
                        <td class="photo-cell" style="width:50%;"></td>
                        @endif
                    </tr>
                </table>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Section 6: Tanda Tangan --}}
        <div class="sec">
            <div class="sec-head">Tanda Tangan &amp; Persetujuan</div>
            <div class="sec-body">
                <table class="sig-table">
                    <tr>
                        <td class="sig-cell">
                            <div class="sig-role">Teknisi Pelaksana</div>
                            <table style="width:100%; border-collapse:collapse;"><tr><td style="text-align:center; height:90px; vertical-align:middle;">
                            @if($laporan->teknisi?->signature)
                                <img class="sig-img" src="{{ public_path('storage/' . $laporan->teknisi->signature) }}" alt="TTD Teknisi">
                            @else
                                <span style="color:#dde2ed; font-size:9pt;">[ tidak tersedia ]</span>
                            @endif
                            </td></tr></table>
                            <div class="sig-line"></div>
                            <div class="sig-name">{{ $laporan->teknisi->name }}</div>
                            <div class="sig-sub">Teknisi</div>
                            <div class="sig-date">Tanggal: {{ $laporan->created_at->format('d F Y') }}</div>
                        </td>
                        <td class="sig-cell">
                            <div class="sig-role">Perwakilan Customer</div>
                            <table style="width:100%; border-collapse:collapse;"><tr><td style="text-align:center; height:90px; vertical-align:middle;">
                            @if($laporan->signature_cust)
                                <img class="sig-img" src="{{ public_path('storage/' . $laporan->signature_cust) }}" alt="TTD Customer">
                            @else
                                <span style="color:#dde2ed; font-size:9pt;">[ belum ditandatangani ]</span>
                            @endif
                            </td></tr></table>
                            <div class="sig-line"></div>
                            <div class="sig-name">{{ $laporan->task->customer->name }}</div>
                            <div class="sig-sub">Customer / Penerima Layanan</div>
                            <div class="sig-date">Tanggal: {{ $laporan->updated_at->format('d F Y') }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>{{-- /body --}}

    {{-- Fixed Footer --}}
    <div class="footer-wrap">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    <span class="footer-bold">SIPRT</span> &mdash; Sistem Informasi Penugasan &amp; Pelaporan Teknisi
                    &nbsp;&bull;&nbsp; Digenerate {{ now()->format('d/m/Y H:i') }} WIB
                </td>
                <td class="footer-right">
                    <span class="footer-bold">No. RPT-{{ str_pad($laporan->id, 5, '0', STR_PAD_LEFT) }}</span>
                    &nbsp;&bull;&nbsp; {{ $laporan->task->customer->name }}
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
