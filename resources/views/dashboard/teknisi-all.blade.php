@extends('layouts.app')

@section('css')
<style>
.stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: .875rem 1rem;
    display: flex;
    align-items: center;
    gap: .875rem;
    height: 100%;
}
.stat-icon {
    width: 44px; height: 44px;
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 1.3rem;
}
.stat-val { font-size: 1.6rem; font-weight: 700; line-height: 1.1; }
.stat-lbl { font-size: .7rem; color: var(--text-secondary); margin-top: 3px; text-transform: uppercase; letter-spacing: .05em; }

.chart-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1rem 1.25rem;
    height: 100%;
}
.chart-card-title {
    font-size: .72rem; font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .05em;
    margin-bottom: .75rem;
}

.dt-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.dt-card-header {
    padding: .625rem 1rem;
    border-bottom: 1px solid var(--border-light);
    font-size: .72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--text-secondary);
    background: var(--bg);
    display: flex; align-items: center; gap: 6px;
}
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-tool"></i>
    <span>Dashboard Teknisi</span>
</div>

<div class="page-header">
    <h4>Semua Tugas Teknisi</h4>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-sm-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(21,101,192,.1);">
                <i class="ti ti-clipboard-list" style="color:#1565C0;"></i>
            </div>
            <div>
                <div class="stat-val" style="color:#1565C0;">{{ $totalTasks }}</div>
                <div class="stat-lbl">Total Tugas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(255,160,0,.12);">
                <i class="ti ti-clock" style="color:#FFA000;"></i>
            </div>
            <div>
                <div class="stat-val" style="color:#FFA000;">{{ $pendingTasks }}</div>
                <div class="stat-lbl">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(21,101,192,.1);">
                <i class="ti ti-loader" style="color:#1565C0;"></i>
            </div>
            <div>
                <div class="stat-val" style="color:#1565C0;">{{ $inProgressTasks }}</div>
                <div class="stat-lbl">Berjalan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(56,142,60,.1);">
                <i class="ti ti-circle-check" style="color:#388E3C;"></i>
            </div>
            <div>
                <div class="stat-val" style="color:#388E3C;">{{ $completedTasks }}</div>
                <div class="stat-lbl">Selesai</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Charts ── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="chart-card">
            <div class="chart-card-title"><i class="ti ti-chart-donut me-1"></i>Distribusi Status</div>
            <div style="position:relative;height:210px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="chart-card">
            <div class="chart-card-title"><i class="ti ti-chart-bar me-1"></i>Beban Tugas per Teknisi</div>
            <div style="position:relative;height:210px;">
                <canvas id="teknisiChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── DataTable ── --}}
<div class="dt-card">
    <div class="dt-card-header">
        <i class="ti ti-table"></i> Daftar Semua Tugas
    </div>
    <div class="table-responsive" style="padding:.75rem 1rem 1rem;">
        <table id="allTasksTable" class="table table-hover align-middle" style="width:100%">
            <thead class="table-light" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:var(--text-secondary);">
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Customer</th>
                    <th>Teknisi</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
$(function () {

    // ── DataTable (Ajax) ───────────────────────────────────────────
    $('#allTasksTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("dashboard.teknisi.all") }}',
        columns: [
            { data: 'DT_RowIndex',   name: 'DT_RowIndex',   orderable: false, searchable: false, width: '44px' },
            { data: 'title',         name: 'title' },
            { data: 'customer_name', name: 'customer.name' },
            { data: 'assignee_name', name: 'assignee.name', orderable: false },
            { data: 'status_badge',  name: 'status' },
            { data: 'due_date_fmt',  name: 'due_date' },
            { data: 'action',        name: 'action', orderable: false, searchable: false, className: 'text-end' },
        ],
        language: {
            processing:   'Memuat data…',
            search:       'Cari:',
            lengthMenu:   'Tampilkan _MENU_ baris',
            info:         'Menampilkan _START_–_END_ dari _TOTAL_ tugas',
            infoEmpty:    'Tidak ada data',
            infoFiltered: '(dari _MAX_ total)',
            paginate:     { previous: '‹', next: '›' },
            emptyTable:   'Belum ada tugas',
            zeroRecords:  'Tidak ditemukan hasil',
        },
        order: [[0, 'asc']],
        pageLength: 10,
        responsive: true,
    });

    // ── Status Doughnut Chart ──────────────────────────────────────
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Berjalan', 'Selesai'],
            datasets: [{
                data: [{{ $pendingTasks }}, {{ $inProgressTasks }}, {{ $completedTasks }}],
                backgroundColor: ['#FFA000', '#1565C0', '#388E3C'],
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, padding: 14, usePointStyle: true },
                },
                tooltip: {
                    callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed },
                },
            },
            responsive: true,
            maintainAspectRatio: false,
        },
    });

    // ── Teknisi Workload Bar Chart ─────────────────────────────────
    new Chart(document.getElementById('teknisiChart'), {
        type: 'bar',
        data: {
            labels:   {!! json_encode($teknisiList->pluck('name')) !!},
            datasets: [
                {
                    label: 'Pending',
                    data: {!! json_encode($teknisiList->pluck('pending_count')->map(fn($v) => (int)$v)) !!},
                    backgroundColor: '#FFA000',
                    borderRadius: 4,
                },
                {
                    label: 'Berjalan',
                    data: {!! json_encode($teknisiList->pluck('progress_count')->map(fn($v) => (int)$v)) !!},
                    backgroundColor: '#1565C0',
                    borderRadius: 4,
                },
                {
                    label: 'Selesai',
                    data: {!! json_encode($teknisiList->pluck('completed_count')->map(fn($v) => (int)$v)) !!},
                    backgroundColor: '#388E3C',
                    borderRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, padding: 14, usePointStyle: true },
                },
            },
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false },
                    ticks: { font: { size: 11 } },
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: { precision: 0, font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,.06)' },
                },
            },
        },
    });

});
</script>
@endsection
