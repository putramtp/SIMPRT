@extends('layouts.app')

@section('css')
<style>
.kpi-icon.blue   { background: var(--blue-light); color: var(--blue); }
.kpi-icon.green  { background: var(--green-light); color: var(--green); }
.kpi-icon.yellow { background: var(--yellow-light); color: var(--yellow); }
.kpi-icon.orange { background: #FBE9E7; color: var(--orange); }
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
div.dataTables_wrapper div.dataTables_filter { text-align: right; }
div.dataTables_wrapper { padding: .5rem .75rem .75rem; }
</style>
@endsection

@section('content')

{{-- Breadcrumb (tablet+) --}}
<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-layout-dashboard"></i>
    <span>Dashboard Sales</span>
</div>

<div class="db-layout">

    {{-- ── Main content ── --}}
    <div class="db-main">

        {{-- KPI cards --}}
        <div class="kpi-grid mb-4">
            <div class="kpi-card">
                <div class="kpi-icon blue"><i class="ti ti-clipboard-list"></i></div>
                <div class="kpi-num" style="color:var(--blue)">{{ $totalTasks }}</div>
                <div class="kpi-label">Total Tugas</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon yellow"><i class="ti ti-clock"></i></div>
                <div class="kpi-num" style="color:var(--yellow)">{{ $pendingTasks }}</div>
                <div class="kpi-label">Pending</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon green"><i class="ti ti-circle-check"></i></div>
                <div class="kpi-num" style="color:var(--green)">{{ $completedTasks }}</div>
                <div class="kpi-label">Selesai</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon orange"><i class="ti ti-users"></i></div>
                <div class="kpi-num" style="color:var(--orange)">{{ $totalCustomers }}</div>
                <div class="kpi-label">Customer</div>
            </div>
        </div>

        {{-- Recent tasks --}}
        <div class="page-header">
            <h4>Tugas Terbaru</h4>
            @can('create customers')
            <a href="{{ route('tugas.create') }}" class="btn btn-primary btn-sm">
                <i class="ti ti-plus me-1"></i>Buat Tugas
            </a>
            @endcan
        </div>

        <div class="panel-card">
            <div class="panel-card-header">
                <i class="ti ti-list"></i> Daftar Tugas
            </div>
            <div class="table-responsive">
                <table id="salesTaskTable" class="table table-hover mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Judul</th>
                            <th>Customer</th>
                            <th class="d-mobile-none">Teknisi</th>
                            <th>Status</th>
                            <th class="d-mobile-none">Deadline</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>{{-- /.db-main --}}

    {{-- ── Right panel (desktop only) ── --}}
    <div class="db-panel">

        {{-- Quick stats --}}
        <div class="panel-card mb-3">
            <div class="panel-card-header">
                <i class="ti ti-chart-bar"></i> Ringkasan
            </div>
            <div style="padding:12px 14px;display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:.82rem;">
                    <span style="color:var(--text-secondary)">Tugas Berjalan</span>
                    <span style="font-weight:700;color:var(--blue)">{{ $inProgressTasks ?? 0 }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:.82rem;">
                    <span style="color:var(--text-secondary)">Tingkat Selesai</span>
                    <span style="font-weight:700;color:var(--green)">
                        {{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%
                    </span>
                </div>
                @if($totalTasks > 0)
                <div>
                    <div style="height:6px;background:var(--border-light);border-radius:4px;overflow:hidden;margin-top:2px;">
                        <div style="height:100%;width:{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%;background:var(--green);border-radius:4px;"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Technician status --}}
        <div class="panel-card">
            <div class="panel-card-header">
                <i class="ti ti-users"></i> Status Teknisi
            </div>
            <div class="tech-panel-list">
                @forelse($teknisiList ?? [] as $tech)
                <div class="tech-panel-row">
                    <div class="tech-panel-avatar">{{ strtoupper(substr($tech->name, 0, 1)) }}</div>
                    <div class="tech-panel-info">
                        <div class="tech-panel-name">{{ $tech->name }}</div>
                        <div class="tech-panel-sub">
                            {{ $tech->tasks_count ?? 0 }} tugas aktif
                        </div>
                    </div>
                    <div class="online-dot {{ ($tech->tasks_count ?? 0) > 0 ? 'busy' : '' }}"></div>
                </div>
                @empty
                <div style="padding:1rem;text-align:center;font-size:.78rem;color:var(--text-secondary);">
                    Belum ada teknisi
                </div>
                @endforelse
            </div>
        </div>

    </div>{{-- /.db-panel --}}

</div>{{-- /.db-layout --}}

@endsection

@section('js')
<script>
$(function () {
    $('#salesTaskTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route("tugas.index") }}',
        columns: [
            { data: 'DT_RowIndex',     name: 'id',           orderable: false, searchable: false, width: '40px' },
            { data: 'title',           name: 'title' },
            { data: 'customer_name',   name: 'customer.name', searchable: false },
            { data: 'assignee_name',   name: 'assignee.name', searchable: false, className: 'd-mobile-none' },
            { data: 'status_badge',    name: 'status',        searchable: false },
            { data: 'due_date_fmt',    name: 'due_date',      searchable: false, className: 'd-mobile-none', defaultContent: '-' },
            { data: 'action',          name: 'action',        orderable: false, searchable: false },
        ],
        language: {
            processing:  'Memuat data…',
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty:   'Tidak ada data',
            emptyTable:  'Belum ada tugas.',
            zeroRecords: 'Tidak ditemukan hasil.',
            paginate: { previous: '&laquo;', next: '&raquo;' },
        },
        pageLength: 10,
        order: [[0, 'desc']],
    });
});
</script>
@endsection
