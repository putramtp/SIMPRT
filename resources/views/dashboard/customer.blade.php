@extends('layouts.app')

@section('css')
<style>
/* ── Customer PWA Dashboard — scoped, not in public.css ───────── */

/* Hero */
.cust-dash-hero {
    background: linear-gradient(135deg, var(--blue-dark) 0%, #1976D2 100%);
    margin: -1rem -1rem 16px;
    padding: 16px 1rem 20px;
    border-radius: 0 0 20px 20px;
}
@media (min-width: 640px) {
    .cust-dash-hero { margin: -.75rem -1.5rem 20px; padding: 16px 1.5rem 20px; }
}
@media (min-width: 1024px) {
    .cust-dash-hero { margin: -.75rem -2rem 20px; padding: 16px 2rem 20px; }
}
.cust-dash-hero-top {
    display: flex; justify-content: space-between; align-items: flex-start;
    margin-bottom: 16px;
}
.cust-dash-brand { display: flex; align-items: center; gap: 8px; }
.cust-dash-brand-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: #fff;
    display: flex; align-items: center; justify-content: center;
}
.cust-dash-brand-icon i { font-size: 20px; color: var(--blue); }
.cust-dash-brand-name  { font-size: 13px; font-weight: 600; color: #fff; }
.cust-dash-brand-sub   { font-size: 10px; color: #90CAF9; }
.cust-dash-readonly {
    background: rgba(255,255,255,.15);
    border-radius: 20px; padding: 4px 10px;
    display: flex; align-items: center; gap: 4px;
    font-size: 10px; color: #90CAF9;
}
.cust-dash-title {
    font-size: 19px; font-weight: 600; color: #fff; margin-bottom: 2px;
}
.cust-dash-company { font-size: 13px; color: #90CAF9; margin-bottom: 12px; }
.cust-dash-status {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(105,240,174,.15);
    border: 1px solid rgba(105,240,174,.4);
    border-radius: 20px; padding: 5px 14px;
}
.cust-dash-status-dot { width: 7px; height: 7px; border-radius: 50%; background: #69F0AE; }
.cust-dash-status-txt { font-size: 12px; color: #69F0AE; font-weight: 500; }

/* Summary strip */
.cust-summary-strip {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 8px; margin-bottom: 20px;
}
@media (min-width: 640px) { .cust-summary-strip { grid-template-columns: 1fr 1fr 1fr 1fr; } }
.cust-summary-card {
    background: var(--white); border: 1px solid var(--border-light);
    border-radius: var(--radius-md); padding: 10px; text-align: center;
}
.cust-summary-val { font-size: 20px; font-weight: 700; line-height: 1.1; }
.cust-summary-lbl { font-size: 10px; color: var(--text-secondary); margin-top: 3px; }
.c-blue   { color: var(--blue); }
.c-green  { color: var(--green); }
.c-yellow { color: var(--yellow); }
.c-gray   { color: var(--text-secondary); }

/* Section header */
.cust-sec-row {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 10px;
}
.cust-sec-title { font-size: 13px; font-weight: 600; color: var(--text); }
.cust-sec-count {
    background: var(--blue-light); color: #0C447C;
    border-radius: 20px; padding: 3px 10px;
    font-size: 11px; font-weight: 600;
}

/* Report cards */
.cust-report-item {
    background: var(--white); border: 1px solid var(--border-light);
    border-radius: var(--radius-lg); overflow: hidden;
    margin-bottom: 10px; text-decoration: none; color: inherit;
    display: block;
}
.cust-report-item:hover { border-color: var(--blue); }
.cust-report-item-bar { height: 3px; }
.bar-blue   { background: var(--blue); }
.bar-yellow { background: var(--yellow); }
.bar-green  { background: var(--green); }
.bar-gray   { background: var(--border); }

.cust-report-item-body { padding: 12px; }
.cust-report-item-head {
    display: flex; justify-content: space-between; align-items: flex-start;
    gap: 8px; margin-bottom: 8px;
}
.cust-report-item-title { font-size: 13px; font-weight: 600; color: var(--text); line-height: 1.4; }
.cust-report-badge {
    border-radius: 20px; padding: 2px 8px;
    font-size: 10px; font-weight: 600; white-space: nowrap; flex-shrink: 0;
}
.badge-submitted { background: var(--blue-light);   color: #0C447C; }
.badge-approved  { background: var(--green-light);  color: #27500A; }
.badge-rejected  { background: #fef2f2;              color: #991b1b; }

.cust-report-item-meta {
    display: flex; flex-direction: column; gap: 4px;
    margin-bottom: 10px;
}
.cust-report-item-meta-row {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; color: var(--text-secondary);
}
.cust-report-item-meta-row i { font-size: 13px; }

.cust-report-item-desc {
    font-size: 12px; color: var(--text-secondary);
    background: var(--bg);
    border-radius: var(--radius-md); padding: 8px 10px;
    line-height: 1.5;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden;
}

.cust-report-item-footer {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 10px; padding-top: 10px;
    border-top: 1px solid var(--border-light);
}
.cust-report-item-date { font-size: 11px; color: var(--text-secondary); }
.cust-detail-btn {
    font-size: 11px; font-weight: 600; color: var(--blue);
    display: flex; align-items: center; gap: 3px;
}

/* Photo thumb */
.cust-report-photo {
    width: 48px; height: 48px; border-radius: var(--radius-md);
    object-fit: cover; flex-shrink: 0;
}
.cust-report-photo-placeholder {
    width: 48px; height: 48px; border-radius: var(--radius-md);
    background: var(--blue-light);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.cust-report-photo-placeholder i { font-size: 20px; color: var(--blue); opacity: .5; }

/* Empty */
.cust-empty {
    text-align: center; padding: 3rem 1rem;
    color: var(--text-secondary); font-size: 13px;
}
.cust-empty i { font-size: 2.5rem; display: block; margin-bottom: 10px; opacity: .2; }

/* Info card */
.cust-info-card {
    background: var(--white); border: 1px solid var(--border-light);
    border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 16px;
}
.cust-info-card-header {
    background: var(--blue-light); padding: 9px 14px;
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600; color: #0C447C;
}
.cust-info-card-header i { font-size: 14px; color: var(--blue); }
.cust-info-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 10px; padding: 14px;
}
.cust-info-lbl { font-size: 10px; color: var(--text-secondary); margin-bottom: 2px; }
.cust-info-val { font-size: 12px; font-weight: 600; color: var(--text); }
</style>
@endsection

@section('content')
@php
    $total     = $reports->count();
    $submitted = $reports->where('status', 'submitted')->count();
    $approved  = $reports->where('status', 'approved')->count();
    $other     = $total - $submitted - $approved;

    $statusLabel = ['submitted' => 'Terkirim', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
    $statusBar   = ['submitted' => 'bar-blue', 'approved' => 'bar-green', 'rejected' => 'bar-yellow'];
    $statusBadge = ['submitted' => 'badge-submitted', 'approved' => 'badge-approved', 'rejected' => 'badge-rejected'];
@endphp

{{-- ── Hero ── --}}
<div class="cust-dash-hero">
    <div class="cust-dash-hero-top">
        <div class="cust-dash-brand">
            <div class="cust-dash-brand-icon">
                <i class="ti ti-bolt"></i>
            </div>
            <div>
                <div class="cust-dash-brand-name">{{ config('app.name', 'SIPRT') }}</div>
                <div class="cust-dash-brand-sub">Solusi Teknisi Profesional</div>
            </div>
        </div>
        <div class="cust-dash-readonly">
            <i class="ti ti-eye" style="font-size:12px;"></i> Hanya Lihat
        </div>
    </div>

    <div class="cust-dash-title">Laporan Pekerjaan</div>
    <div class="cust-dash-company">{{ $customer->name }}</div>

    <div class="cust-dash-status">
        <div class="cust-dash-status-dot"></div>
        <span class="cust-dash-status-txt">{{ $total }} laporan tersedia</span>
    </div>
</div>

{{-- ── Customer info card ── --}}
@if($customer->address || $customer->phone || $customer->email)
<div class="cust-info-card">
    <div class="cust-info-card-header">
        <i class="ti ti-clipboard-text"></i> Informasi Customer
    </div>
    <div class="cust-info-grid">
        @if($customer->phone)
        <div>
            <div class="cust-info-lbl">Telepon</div>
            <div class="cust-info-val">{{ $customer->phone }}</div>
        </div>
        @endif
        @if($customer->email)
        <div>
            <div class="cust-info-lbl">Email</div>
            <div class="cust-info-val">{{ $customer->email }}</div>
        </div>
        @endif
        @if($customer->address)
        <div style="grid-column: 1 / -1;">
            <div class="cust-info-lbl">Alamat</div>
            <div class="cust-info-val">{{ $customer->address }}</div>
        </div>
        @endif
    </div>
</div>
@endif

{{-- ── Summary metrics ── --}}
<div class="cust-summary-strip">
    <div class="cust-summary-card">
        <div class="cust-summary-val c-blue">{{ $total }}</div>
        <div class="cust-summary-lbl">Total Laporan</div>
    </div>
    <div class="cust-summary-card">
        <div class="cust-summary-val c-green">{{ $approved }}</div>
        <div class="cust-summary-lbl">Disetujui</div>
    </div>
    <div class="cust-summary-card">
        <div class="cust-summary-val c-yellow">{{ $submitted }}</div>
        <div class="cust-summary-lbl">Menunggu</div>
    </div>
    <div class="cust-summary-card">
        <div class="cust-summary-val c-gray">{{ $other }}</div>
        <div class="cust-summary-lbl">Lainnya</div>
    </div>
</div>

{{-- ── Report list ── --}}
<div class="cust-sec-row">
    <span class="cust-sec-title">Semua Laporan</span>
    @if($total)
        <span class="cust-sec-count">{{ $total }} laporan</span>
    @endif
</div>

@forelse($reports as $report)
@php
    $bar   = $statusBar[$report->status]   ?? 'bar-gray';
    $badge = $statusBadge[$report->status] ?? 'badge-submitted';
    $label = $statusLabel[$report->status] ?? ucfirst($report->status);
@endphp
<a href="{{ route('laporan.show', $report) }}" class="cust-report-item">
    <div class="cust-report-item-bar {{ $bar }}"></div>
    <div class="cust-report-item-body">
        <div class="cust-report-item-head">
            <div style="display:flex;align-items:flex-start;gap:10px;flex:1;min-width:0;">
                @if(!empty($report->photos))
                    <img src="{{ asset('storage/' . $report->photos[0]) }}"
                         alt="Foto" class="cust-report-photo">
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

<div style="height:.5rem;"></div>
@endsection
