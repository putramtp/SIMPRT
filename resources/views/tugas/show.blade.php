@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    .card-body strong { color: var(--text-secondary); font-size: .8rem; }
    .table th { font-size: .75rem; color: var(--text-secondary); border-bottom: 1px solid var(--border); }
    .table td { font-size: .85rem; vertical-align: middle; }
    .badge { border-radius: 20px; font-size: .7rem; padding: .3em .75em; }
    h4 { color: var(--text); font-weight: 600; }
    h5 { color: var(--text); }
    h6 { color: var(--text-secondary); font-size: .8rem; text-transform: uppercase; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('tugas.index') }}" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0">Detail Tugas</h4>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>{{ $task->title }}</h5>
            <p class="text-muted">{{ $task->description ?? '-' }}</p>
            <div class="row">
                <div class="col-6"><strong>Customer:</strong> {{ $task->customer->name }}</div>
                <div class="col-6"><strong>Teknisi:</strong> {{ $task->assignee->name }}</div>
                <div class="col-6 mt-2"><strong>Status:</strong> <span class="badge bg-secondary">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span></div>
                <div class="col-6 mt-2"><strong>Deadline:</strong> {{ $task->due_date?->format('d/m/Y') ?? '-' }}</div>
            </div>
        </div>
    </div>

    <h6>Laporan</h6>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Teknisi</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
                    <tbody>
                        @forelse($task->reports as $report)
                        <tr>
                            <td>{{ $report->teknisi->name }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($report->status) }}</span></td>
                            <td>{{ $report->created_at->format('d/m/Y') }}</td>
                            <td><a href="{{ route('laporan.show', $report) }}" class="btn btn-sm btn-outline-secondary">Lihat</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada laporan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
