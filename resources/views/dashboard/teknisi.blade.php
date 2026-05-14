@extends('layouts.app')

@section('css')
<style>
.section-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: .75rem;
}
.section-label-row h6 {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--text-secondary);
    margin: 0;
}
.panel-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 1rem;
}
.panel-card-header {
    padding: 10px 14px;
    font-size: .78rem;
    font-weight: 700;
    color: var(--text);
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--bg);
}
.task-detail-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.task-detail-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
    color: var(--text-secondary);
    text-align: center;
    gap: .5rem;
}
.task-detail-placeholder i { font-size: 2.5rem; opacity: .25; }
.task-detail-placeholder p { font-size: .82rem; margin: 0; }
.task-detail-header {
    background: var(--blue);
    color: #fff;
    padding: 14px 16px;
}
.task-detail-header h6 { font-size: 1rem; font-weight: 700; margin: 0 0 4px; }
.task-detail-header .sub { font-size: .78rem; opacity: .8; }
.task-detail-body { padding: 14px 16px; display: flex; flex-direction: column; gap: 12px; }
.detail-row { display: flex; flex-direction: column; gap: 2px; }
.detail-key { font-size: .72rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .04em; }
.detail-val { font-size: .875rem; color: var(--text); }
.status-badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 20px; padding: 3px 10px; font-size: .72rem; font-weight: 600; }
.status-badge.pending     { background: var(--yellow-light); color: #633806; }
.status-badge.in_progress { background: var(--blue-light);   color: #0C447C; }
.status-badge.completed   { background: var(--green-light);  color: #27500A; }

/* Tablet split layout for teknisi dashboard */
@media (min-width: 640px) {
    .tek-layout { display: flex; gap: 1.25rem; align-items: flex-start; }
    .tek-main   { flex: 1; min-width: 0; }
    .tek-detail { width: 300px; flex-shrink: 0; position: sticky; top: calc(var(--topbar-h) + 1rem); }
}
@media (min-width: 1024px) {
    .tek-detail { width: 340px; }
}
@media (max-width: 639px) {
    .tek-detail { display: none; }
}
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-tool"></i>
    <span>Dashboard Teknisi</span>
</div>

<div class="tek-layout">

    {{-- ── Main: task list + reports ── --}}
    <div class="tek-main">

        {{-- My tasks --}}
        <div class="page-header">
            <h4>Tugas Saya</h4>
            <a href="{{ route('laporan.create') }}" class="btn btn-primary btn-sm">
                <i class="ti ti-plus me-1"></i>Buat Laporan
            </a>
        </div>

        <div class="panel-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="taskTable">
                    <thead class="table-light" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:var(--text-secondary);">
                        <tr>
                            <th>Judul</th>
                            <th class="d-mobile-none">Customer</th>
                            <th class="d-mobile-none">Deadline</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myTasks as $task)
                        <tr class="task-row" data-id="{{ $task->id }}"
                            data-title="{{ e($task->title) }}"
                            data-customer="{{ e($task->customer->name) }}"
                            data-desc="{{ e($task->description ?? '-') }}"
                            data-status="{{ $task->status }}"
                            data-deadline="{{ $task->due_date?->format('d M Y') ?? '-' }}"
                            style="cursor:pointer;">
                            <td style="font-weight:600;font-size:.875rem;">{{ $task->title }}</td>
                            <td class="d-mobile-none" style="font-size:.82rem;color:var(--text-secondary);">{{ $task->customer->name }}</td>
                            <td class="d-mobile-none" style="font-size:.82rem;">{{ $task->due_date?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <span class="status-badge {{ $task->status }}">
                                    @if($task->status === 'completed') <i class="ti ti-circle-check"></i>
                                    @elseif($task->status === 'in_progress') <i class="ti ti-loader"></i>
                                    @else <i class="ti ti-clock"></i> @endif
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('tugas.show', $task) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4" style="color:var(--text-secondary);font-size:.875rem;">Belum ada tugas yang diberikan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent reports --}}
        <div class="section-label-row mt-2">
            <h6>Laporan Terakhir</h6>
            <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-secondary">Semua</a>
        </div>

        <div class="panel-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:var(--text-secondary);">
                        <tr>
                            <th>Tugas</th>
                            <th>Status</th>
                            <th class="d-mobile-none">Tanggal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myReports as $report)
                        <tr>
                            <td style="font-size:.875rem;font-weight:600;">{{ $report->task->title }}</td>
                            <td>
                                <span class="badge rounded-pill bg-info bg-opacity-10 text-info fw-semibold" style="font-size:.7rem;">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="d-mobile-none" style="font-size:.82rem;color:var(--text-secondary);">{{ $report->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('laporan.show', $report) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4" style="color:var(--text-secondary);font-size:.875rem;">Belum ada laporan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /.tek-main --}}

    {{-- ── Task detail panel (tablet/desktop) ── --}}
    <div class="tek-detail">
        <div class="task-detail-card">
            <div id="taskDetailPlaceholder" class="task-detail-placeholder">
                <i class="ti ti-clipboard-list"></i>
                <p>Pilih tugas untuk melihat detail</p>
            </div>
            <div id="taskDetailContent" style="display:none;">
                <div class="task-detail-header">
                    <h6 id="detailTitle">—</h6>
                    <div class="sub" id="detailCustomer">—</div>
                </div>
                <div class="task-detail-body">
                    <div class="detail-row">
                        <span class="detail-key">Status</span>
                        <span class="detail-val" id="detailStatus">—</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-key">Deadline</span>
                        <span class="detail-val" id="detailDeadline">—</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-key">Deskripsi</span>
                        <span class="detail-val" id="detailDesc" style="white-space:pre-line;">—</span>
                    </div>
                    <div style="margin-top:.5rem;">
                        <a id="detailLink" href="#" class="btn btn-primary btn-sm w-100">
                            <i class="ti ti-eye me-1"></i>Lihat Detail
                        </a>
                        <a id="reportLink" href="{{ route('laporan.create') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                            <i class="ti ti-file-plus me-1"></i>Buat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>{{-- /.tek-detail --}}

</div>{{-- /.tek-layout --}}

@endsection

@section('js')
<script>
$(function () {
    var routes = {
        tugasShow: '{{ url("tugas") }}',
    };

    $('#taskTable .task-row').on('click', function () {
        var $row = $(this);
        $('#taskTable .task-row').removeClass('table-active');
        $row.addClass('table-active');

        var status     = $row.data('status');
        var statusMap  = { pending: 'Pending', in_progress: 'Berjalan', completed: 'Selesai' };
        var classBadge = `<span class="status-badge ${status}">${statusMap[status] || status}</span>`;

        $('#detailTitle').text($row.data('title'));
        $('#detailCustomer').text($row.data('customer'));
        $('#detailStatus').html(classBadge);
        $('#detailDeadline').text($row.data('deadline'));
        $('#detailDesc').text($row.data('desc'));
        $('#detailLink').attr('href', routes.tugasShow + '/' + $row.data('id'));

        $('#taskDetailPlaceholder').hide();
        $('#taskDetailContent').show();
    });
});
</script>
@endsection
