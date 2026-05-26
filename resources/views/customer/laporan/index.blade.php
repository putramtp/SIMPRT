@extends('layouts.customer')

@section('css')
<style>
.cust-report-item { background:var(--white); border:1px solid var(--border-light); border-radius:var(--radius-lg); overflow:hidden; margin-bottom:10px; text-decoration:none; color:inherit; display:block; }
.cust-report-item:hover { border-color:var(--blue); }
.cust-report-item-bar { height:3px; }
.bar-blue   { background:var(--blue); }
.bar-yellow { background:var(--yellow); }
.bar-green  { background:var(--green); }
.bar-gray   { background:var(--border); }
.cust-report-item-body { padding:12px; }
.cust-report-item-head { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:8px; }
.cust-report-item-title { font-size:13px; font-weight:600; color:var(--text); line-height:1.4; }
.cust-report-badge { border-radius:20px; padding:2px 8px; font-size:10px; font-weight:600; white-space:nowrap; flex-shrink:0; }
.badge-submitted { background:var(--blue-light); color:#0C447C; }
.badge-approved  { background:var(--green-light); color:#27500A; }
.badge-rejected  { background:#fef2f2; color:#991b1b; }
.cust-report-item-meta { display:flex; flex-direction:column; gap:4px; margin-bottom:8px; }
.cust-report-item-meta-row { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--text-secondary); }
.cust-report-item-meta-row i { font-size:13px; }
.cust-report-item-desc { font-size:12px; color:var(--text-secondary); background:var(--bg); border-radius:var(--radius-md); padding:8px 10px; line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.cust-report-item-footer { display:flex; justify-content:space-between; align-items:center; margin-top:10px; padding-top:10px; border-top:1px solid var(--border-light); }
.cust-report-item-date { font-size:11px; color:var(--text-secondary); }
.cust-detail-btn { font-size:11px; font-weight:600; color:var(--blue); display:flex; align-items:center; gap:3px; }
.cust-report-photo { width:40px; height:40px; border-radius:var(--radius-md); object-fit:cover; flex-shrink:0; }
.cust-report-photo-placeholder { width:40px; height:40px; border-radius:var(--radius-md); background:var(--blue-light); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.cust-report-photo-placeholder i { font-size:18px; color:var(--blue); opacity:.5; }
.cust-empty { text-align:center; padding:3rem 1rem; color:var(--text-secondary); font-size:13px; }
.cust-empty i { font-size:2.5rem; display:block; margin-bottom:10px; opacity:.2; }
</style>
@endsection

@section('content')
@php
    $statusLabel = ['submitted' => 'Terkirim', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
    $statusBar   = ['submitted' => 'bar-blue', 'approved' => 'bar-green', 'rejected' => 'bar-yellow'];
    $statusBadge = ['submitted' => 'badge-submitted', 'approved' => 'badge-approved', 'rejected' => 'badge-rejected'];
@endphp

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-file-text"></i>
    <span>Laporan Saya — {{ $customer->name }}</span>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:700;font-size:15px;">Semua Laporan</h5>
    <span style="font-size:12px;color:var(--text-secondary);">{{ $reports->count() }} laporan</span>
</div>

@forelse($reports as $report)
@php
    $bar   = $statusBar[$report->status]   ?? 'bar-gray';
    $badge = $statusBadge[$report->status] ?? 'badge-submitted';
    $label = $statusLabel[$report->status] ?? ucfirst($report->status);
@endphp
<a href="{{ route('customer.laporan.show', $report) }}" class="cust-report-item">
    <div class="cust-report-item-bar {{ $bar }}"></div>
    <div class="cust-report-item-body">
        <div class="cust-report-item-head">
            <div style="display:flex;align-items:flex-start;gap:10px;flex:1;min-width:0;">
                @if($report->photo)
                    <img src="{{ asset('storage/' . $report->photo) }}" alt="Foto" class="cust-report-photo">
                @else
                    <div class="cust-report-photo-placeholder">
                        <i class="ti ti-file-text"></i>
                    </div>
                @endif
                <div class="cust-report-item-title">{{ $report->task?->title ?? '-' }}</div>
            </div>
            <span class="cust-report-badge {{ $badge }}">{{ $label }}</span>
        </div>
        <div class="cust-report-item-meta">
            @if($report->teknisi)
            <div class="cust-report-item-meta-row">
                <i class="ti ti-user"></i>
                <span>{{ $report->teknisi->name }}</span>
            </div>
            @endif
        </div>
        @if($report->description)
        <div class="cust-report-item-desc">{{ $report->description }}</div>
        @endif
        <div class="cust-report-item-footer">
            <span class="cust-report-item-date">
                <i class="ti ti-calendar" style="font-size:11px;"></i>
                {{ $report->created_at->format('d M Y, H:i') }}
            </span>
            <span class="cust-detail-btn">
                Lihat Detail <i class="ti ti-chevron-right" style="font-size:11px;"></i>
            </span>
        </div>
    </div>
</a>
@empty
<div class="cust-empty">
    <i class="ti ti-file-off"></i>
    Belum ada laporan untuk akun Anda.
</div>
@endforelse
@endsection
