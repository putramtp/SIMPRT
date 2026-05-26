@extends('layouts.app')

@section('css')
<style>
.task-show-hero {
    background: linear-gradient(135deg, var(--blue-dark) 0%, #1976D2 100%);
    border-radius: var(--radius-lg);
    color: #fff;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    position: relative;
    overflow: hidden;
    animation: mblueIn .32s .03s both ease-out;
}
.task-show-hero::before {
    content: '';
    position: absolute;
    right: -30px; top: -30px;
    width: 140px; height: 140px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.task-show-hero-tag { font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; opacity: .7; margin-bottom: .4rem; }
.task-show-hero h4  { color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0 0 .5rem; }
.task-show-hero p   { color: rgba(255,255,255,.72); font-size: .875rem; margin: 0 0 1rem; }
.task-show-hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem .75rem; margin-top: .5rem; }
.task-show-hero-grid .field-label { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; opacity: .65; }
.task-show-hero-grid .field-val   { font-size: .875rem; font-weight: 600; }
.task-show-hero-actions { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: 1rem; }
.task-hero-pill {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(255,255,255,.15); border-radius: 20px;
    padding: 4px 12px; font-size: .75rem; font-weight: 600;
    border: none; color: #fff; text-decoration: none;
    transition: background .15s; cursor: pointer;
}
.task-hero-pill:hover { background: rgba(255,255,255,.28); color: #fff; }
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <a href="{{ route('tugas.index') }}">Tugas</a>
    <i class="ti ti-chevron-right"></i>
    <span>Detail</span>
</div>

{{-- Hero --}}
<div class="task-show-hero">
    <div class="task-show-hero-tag"><i class="ti ti-clipboard-list me-1"></i> Detail Tugas</div>
    <h4>{{ $task->title }}</h4>
    @if($task->description)
    <p>{{ $task->description }}</p>
    @endif
    <div class="task-show-hero-grid">
        <div>
            <div class="field-label">Customer</div>
            <div class="field-val">{{ $task->customer->name ?? "-" }}</div>
        </div>
        <div>
            <div class="field-label">Teknisi</div>
            <div class="field-val">{{ $task->assignees->pluck('name')->join(', ') ?: '—' }}</div>
        </div>
        <div>
            <div class="field-label">Status</div>
            <div class="field-val">
                <span class="badge rounded-pill
                    @if($task->status === 'completed') bg-success
                    @elseif($task->status === 'in_progress') bg-info
                    @else bg-warning @endif">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>
        </div>
        <div>
            <div class="field-label">Deadline</div>
            <div class="field-val">{{ $task->due_date?->format('d M Y') ?? '—' }}</div>
        </div>
    </div>
    <div class="task-show-hero-actions">
        <a href="{{ route('tugas.index') }}" class="task-hero-pill">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
        @can('edit customers')
        <a href="{{ route('tugas.edit', $task) }}" class="task-hero-pill">
            <i class="ti ti-pencil"></i> Edit
        </a>
        @endcan
    </div>
</div>

{{-- Reports --}}
<div class="panel-card">
    <div class="panel-card-header">
        <i class="ti ti-file-text"></i> Daftar Laporan
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 w-100">
            <thead class="table-light">
                <tr>
                    <th>Teknisi</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($task->reports as $report)
                <tr>
                    <td>{{ $report->teknisi->name }}</td>
                    <td><span class="badge bg-info">{{ ucfirst($report->status) }}</span></td>
                    <td>{{ $report->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('laporan.show', $report) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-eye me-1"></i>Lihat
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4" style="color:var(--text-secondary);font-size:.875rem;">
                        <i class="ti ti-file-off" style="font-size:1.5rem;display:block;margin-bottom:.35rem;opacity:.3;"></i>
                        Belum ada laporan untuk tugas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
