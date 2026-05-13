@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    .table th { font-size: .75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .04em; border-bottom: 1px solid var(--border); }
    .table td { font-size: .85rem; vertical-align: middle; color: var(--text); }
    .table-hover tbody tr:hover { background: var(--blue-light); }
    .badge { border-radius: 20px; font-size: .7rem; padding: .3em .75em; }
    h4 { color: var(--text); font-weight: 600; }
    div.dataTables_wrapper div.dataTables_filter { text-align: right; }
    div.dataTables_wrapper { padding: .75rem; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Laporan</h4>
        <a href="{{ route('laporan.create') }}" class="btn btn-primary">+ Buat Laporan</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="laporanTable" class="table table-hover mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tugas</th>
                            <th>Customer</th>
                            <th>Teknisi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(function () {
    $('#laporanTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route("laporan.index") }}',
        columns: [
            { data: 'DT_RowIndex',   name: 'id', orderable: false, searchable: false, width: '40px' },
            { data: 'task_title',    name: 'task.title', searchable: false },
            { data: 'customer_name', name: 'customer.name', searchable: false },
            { data: 'teknisi_name',  name: 'teknisi.name', searchable: false },
            { data: 'status_badge',  name: 'status', searchable: false },
            { data: 'tanggal',       name: 'created_at', searchable: false },
            { data: 'action',        name: 'action', orderable: false, searchable: false },
        ],
        language: {
            processing:  'Memuat data…',
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty:   'Tidak ada data',
            emptyTable:  'Belum ada laporan.',
            zeroRecords: 'Tidak ditemukan hasil.',
            paginate: { previous: '&laquo;', next: '&raquo;' },
        },
    });
});
</script>
@endsection
