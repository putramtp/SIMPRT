@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    .card-header { background: var(--blue-light); color: #0C447C; font-weight: 600; border-bottom: 1px solid var(--border); border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important; }
    .table th { font-size: .75rem; color: var(--text-secondary); border-bottom: 1px solid var(--border); }
    .table td { font-size: .85rem; vertical-align: middle; }
    .badge { border-radius: 20px; font-size: .7rem; padding: .3em .75em; }
    h4 { color: var(--text); }
    h6 { color: var(--text-secondary); font-size: .8rem; text-transform: uppercase; letter-spacing: .04em; }
</style>
@endsection

@section('content')
<div class="container">
    <h4 class="mb-4">Dashboard Teknisi</h4>

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Tugas Saya</h6>
            <a href="{{ route('laporan.create') }}" class="btn btn-sm btn-primary">+ Buat Laporan</a>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul</th>
                                <th>Customer</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myTasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->customer->name }}</td>
                                <td>{{ $task->due_date?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td><a href="{{ route('tugas.show', $task) }}" class="btn btn-sm btn-outline-secondary">Detail</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">Belum ada tugas yang diberikan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <h6 class="mb-2">Laporan Terakhir</h6>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tugas</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myReports as $report)
                        <tr>
                            <td>{{ $report->task->title }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($report->status) }}</span></td>
                            <td>{{ $report->created_at->format('d/m/Y') }}</td>
                            <td><a href="{{ route('laporan.show', $report) }}" class="btn btn-sm btn-outline-secondary">Detail</a></td>
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
