@extends('layouts.app')

@section('css')
<style>
/* ── Tek PWA Dashboard — scoped styles, not in public.css ─────── */

/* Hero */
.tek-hero {
    background: var(--blue-dark);
    margin: -1rem -1rem 16px;
    padding: 16px 1rem 18px;
    border-radius: 0 0 20px 20px;
}
@media (min-width: 640px) {
    .tek-hero { margin: -.75rem -1.5rem 20px; padding: 16px 1.5rem 18px; }
}
@media (min-width: 1024px) {
    .tek-hero { margin: -.75rem -2rem 20px; padding: 16px 2rem 18px; }
}
.tek-hero-row {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 14px;
}
.tek-greeting  { font-size: 11px; color: #90CAF9; margin-bottom: 2px; }
.tek-username  { font-size: 17px; font-weight: 600; color: #fff; }
.tek-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: #42A5F5;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 14px; color: #042C53; flex-shrink: 0;
}
.tek-status-pill {
    background: rgba(255,255,255,.13);
    border-radius: 50px; padding: 9px 12px;
    display: flex; align-items: center; gap: 8px;
}
.tek-status-dot  { width: 8px; height: 8px; border-radius: 50%; background: #69F0AE; flex-shrink: 0; }
.tek-status-text { font-size: 13px; color: #fff; }

/* Metric cards */
.tek-metrics {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    gap: 8px; margin-bottom: 20px;
}
@media (min-width: 640px) { .tek-metrics { gap: 12px; } }
.tek-metric {
    background: var(--white); border: 1px solid var(--border-light);
    border-radius: var(--radius-md); padding: 10px; text-align: center;
}
.tek-metric-val { font-size: 20px; font-weight: 700; line-height: 1.1; }
.tek-metric-lbl { font-size: 10px; color: var(--text-secondary); margin-top: 3px; }
.c-blue   { color: var(--blue); }
.c-green  { color: var(--green); }
.c-yellow { color: var(--yellow); }

/* Section header row */
.tek-sec-row {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 10px;
}
.tek-sec-title { font-size: 13px; font-weight: 600; color: var(--text); }
.tek-sec-badge {
    background: var(--blue-light); color: #0C447C;
    border-radius: 20px; padding: 3px 10px;
    font-size: 11px; font-weight: 600;
}
.tek-sec-link { font-size: 12px; color: var(--blue); text-decoration: none; }

/* Task card */
.tek-card {
    background: var(--white); border: 1px solid var(--border-light);
    border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 10px;
}
.tek-card-bar { height: 3px; }
.bar-blue   { background: var(--blue); }
.bar-yellow { background: var(--yellow); }
.bar-gray   { background: var(--border); }
.bar-green  { background: var(--green); }

.tek-card-body { padding: 12px; }
.tek-card-head {
    display: flex; justify-content: space-between; align-items: flex-start;
    gap: 8px; margin-bottom: 8px;
}
.tek-card-title { font-size: 13px; font-weight: 600; color: var(--text); line-height: 1.4; text-decoration: none; }
.tek-card-title:hover { color: var(--blue); text-decoration: none; }

.tek-badge {
    border-radius: 20px; padding: 2px 8px;
    font-size: 10px; font-weight: 600; white-space: nowrap; flex-shrink: 0;
}
.badge-in_progress { background: var(--blue-light);   color: #0C447C; }
.badge-pending     { background: var(--yellow-light); color: #633806; }
.badge-completed   { background: var(--green-light);  color: #27500A; }

.tek-meta { display: flex; flex-direction: column; gap: 5px; margin-bottom: 10px; }
.tek-meta-row {
    display: flex; align-items: flex-start; gap: 6px;
    font-size: 12px; color: var(--text-secondary);
}
.tek-meta-row i { font-size: 13px; flex-shrink: 0; margin-top: 1px; }

/* Buttons inside card */
.tek-card-actions { display: flex; gap: 8px; }
.tek-btn-nav {
    background: var(--bg); border: 1px solid var(--border);
    border-radius: var(--radius-md); padding: 8px 6px;
    font-size: 12px; color: var(--text-secondary);
    display: inline-flex; align-items: center; justify-content: center;
    gap: 4px; text-decoration: none;
}
.tek-btn-primary {
    background: var(--blue); border: none;
    border-radius: var(--radius-md); padding: 8px;
    font-size: 12px; color: #fff; font-weight: 600;
    display: inline-flex; align-items: center; justify-content: center;
    gap: 4px; text-decoration: none;
}
.tek-btn-warning {
    background: var(--yellow); border: none;
    border-radius: var(--radius-md); padding: 8px;
    font-size: 12px; color: #412402; font-weight: 600;
    display: inline-flex; align-items: center; justify-content: center;
    gap: 4px; text-decoration: none;
}

/* History list */
.tek-history {
    background: var(--white); border: 1px solid var(--border-light);
    border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 16px;
}
.tek-history-row {
    display: flex; align-items: center; gap: 10px; padding: 10px 12px;
    border-bottom: 1px solid var(--border-light);
    text-decoration: none; color: inherit;
}
.tek-history-row:last-child { border-bottom: none; }
.tek-history-icon {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--green-light);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.tek-history-info { flex: 1; min-width: 0; }
.tek-history-name { font-size: 12px; font-weight: 600; color: var(--text); }
.tek-history-sub  { font-size: 11px; color: var(--text-secondary); margin-top: 1px; }
.tek-history-status { font-size: 11px; font-weight: 600; color: var(--green); white-space: nowrap; }

/* Empty state */
.tek-empty {
    text-align: center; padding: 2.5rem 1rem;
    color: var(--text-secondary); font-size: 13px;
}
.tek-empty i { font-size: 2rem; display: block; margin-bottom: 8px; opacity: .25; }
</style>
@endsection

@section('content')
@php
    $hour    = (int) date('H');
    $salam   = $hour < 11 ? 'Selamat pagi'
             : ($hour < 15 ? 'Selamat siang'
             : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $initial = strtoupper(mb_substr(Auth::user()->name, 0, 2));

    $activeTasks    = $myTasks->whereIn('status', ['pending', 'in_progress']);
    $completedCount = $myTasks->where('status', 'completed')->count();

    // in_progress first, then pending
    $sorted = $activeTasks->sortBy(fn($t) => $t->status === 'in_progress' ? 0 : 1)->values();
@endphp

@if(session('success'))
<div class="alert alert-success alert-dismissible py-2 mb-3" style="font-size:.84rem;">
    <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('info'))
<div class="alert alert-info alert-dismissible py-2 mb-3" style="font-size:.84rem;">
    <i class="ti ti-info-circle me-1"></i>{{ session('info') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Hero ── --}}
<div class="tek-hero">
    <div class="tek-hero-row">
        <div>
            <div class="tek-greeting">{{ $salam }},</div>
            <div class="tek-username">{{ Auth::user()->name }}</div>
        </div>
        <div class="tek-avatar">{{ $initial }}</div>
    </div>
    <div class="tek-status-pill">
        <div class="tek-status-dot"></div>
        <span class="tek-status-text">Status: <strong>Aktif &amp; Tersedia</strong></span>
    </div>
</div>

{{-- ── Metrics ── --}}
<div class="tek-metrics">
    <div class="tek-metric">
        <div class="tek-metric-val c-blue">{{ $activeTasks->count() }}</div>
        <div class="tek-metric-lbl">Tugas Aktif</div>
    </div>
    <div class="tek-metric">
        <div class="tek-metric-val c-green">{{ $completedCount }}</div>
        <div class="tek-metric-lbl">Selesai</div>
    </div>
    <div class="tek-metric">
        <div class="tek-metric-val c-yellow">{{ $myTasks->count() }}</div>
        <div class="tek-metric-lbl">Total Tugas</div>
    </div>
</div>

{{-- ── Active tasks ── --}}
<div class="tek-sec-row">
    <span class="tek-sec-title">Tugas Saya</span>
    @if($activeTasks->count())
        <span class="tek-sec-badge">{{ $activeTasks->count() }} tugas</span>
    @endif
</div>

@forelse($sorted as $task)
@php
    $bar   = $task->status === 'in_progress' ? 'bar-blue' : 'bar-yellow';
    $label = $task->status === 'in_progress' ? 'Berlangsung' : 'Menunggu';
    $addr  = $task->customer->address ?? $task->customer->name;
    $mapsQ = urlencode($addr);
@endphp
<div class="tek-card">
    <div class="tek-card-bar {{ $bar }}"></div>
    <div class="tek-card-body">
        <div class="tek-card-head">
            <a href="{{ route('tugas.show', $task) }}" class="tek-card-title">{{ $task->title }}</a>
            <span class="tek-badge badge-{{ $task->status }}">{{ $label }}</span>
        </div>
        <div class="tek-meta">
            <div class="tek-meta-row">
                <i class="ti ti-building"></i>
                <span>{{ $task->customer->name }}</span>
            </div>
            @if($task->customer->address)
            <div class="tek-meta-row">
                <i class="ti ti-map-pin"></i>
                <span>{{ $task->customer->address }}</span>
            </div>
            @endif
            @if($task->due_date)
            <div class="tek-meta-row">
                <i class="ti ti-calendar"></i>
                <span>Deadline: {{ $task->due_date->format('d M Y') }}</span>
            </div>
            @endif
        </div>
        <div class="tek-card-actions">
            <a href="https://www.google.com/maps/search/?api=1&query={{ $mapsQ }}"
               target="_blank" rel="noopener" class="tek-btn-nav" style="flex:1;">
                <i class="ti ti-map"></i> Navigasi
            </a>
            @if($task->status === 'in_progress')
                <a href="{{ route('laporan.create', ['task_id' => $task->id]) }}" class="tek-btn-primary" style="flex:1;">
                    <i class="ti ti-file-plus"></i> Isi Laporan
                </a>
            @else
                <form action="{{ route('tugas.start', $task) }}" method="POST" style="flex:1;display:flex;">
                    @csrf @method('PATCH')
                    <button type="submit" class="tek-btn-primary" style="flex:1;cursor:pointer;">
                        <i class="ti ti-player-play"></i> Mulai Tugas
                    </button>
                </form>
            @endif
            <a href="{{ route('tugas.show', $task) }}" class="tek-btn-warning" style="flex:1;">
                <i class="ti ti-eye"></i> Detail
            </a>
        </div>
    </div>
</div>
@empty
<div class="tek-empty">
    <i class="ti ti-clipboard-list"></i>
    Belum ada tugas aktif yang diberikan.
</div>
@endforelse

{{-- ── Recent reports ── --}}
@if($myReports->count())
<div class="tek-sec-row mt-3">
    <span class="tek-sec-title">Riwayat Terbaru</span>
    <a href="{{ route('laporan.index') }}" class="tek-sec-link">Lihat semua</a>
</div>
<div class="tek-history">
    @foreach($myReports as $report)
    <a href="{{ route('laporan.show', $report) }}" class="tek-history-row">
        <div class="tek-history-icon">
            <i class="ti ti-check" style="font-size:16px;color:var(--green);"></i>
        </div>
        <div class="tek-history-info">
            <div class="tek-history-name">{{ $report->task->title }}</div>
            <div class="tek-history-sub">
                {{ $report->created_at->format('d M Y') }}
                @if($report->task->customer) &middot; {{ $report->task->customer->name }}@endif
            </div>
        </div>
        <span class="tek-history-status">Selesai</span>
    </a>
    @endforeach
</div>
@endif

<div style="height:.5rem;"></div>
@endsection
