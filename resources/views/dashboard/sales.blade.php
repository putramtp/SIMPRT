@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .stat-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1rem; text-align: center; }
    .stat-num  { font-size: 2rem; font-weight: 700; line-height: 1; }
    .stat-label{ font-size: .75rem; color: var(--text-secondary); margin-top: .25rem; }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    .card-header { background: var(--blue-light); color: #0C447C; font-weight: 600; border-bottom: 1px solid var(--border); border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important; }
    .table th { font-size: .75rem; color: var(--text-secondary); border-bottom: 1px solid var(--border); }
    .table td { font-size: .85rem; vertical-align: middle; }
    .badge { border-radius: 20px; font-size: .7rem; padding: .3em .75em; }
</style>
@endsection

@section('content')
<div class="container">
    <h4 class="mb-4">Dashboard Sales</h4>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-num" style="color:var(--blue)">{{ $totalTasks }}</div>
                <div class="stat-label">Total Tugas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-num" style="color:var(--yellow)">{{ $pendingTasks }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-num" style="color:var(--green)">{{ $completedTasks }}</div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-num" style="color:var(--orange)">{{ $totalCustomers }}</div>
                <div class="stat-label">Customer</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Tugas Terbaru</span>
            <a href="{{ route('tugas.create') }}" class="btn btn-sm btn-primary">+ Buat Tugas</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Customer</th>
                            <th>Teknisi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->customer->name }}</td>
                            <td>{{ $task->assignee->name }}</td>
                            <td>
                                <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada tugas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
