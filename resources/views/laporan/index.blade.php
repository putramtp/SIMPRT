@extends('layouts.app')

@section('css')
<style>
    div.dataTables_wrapper div.dataTables_filter { text-align: right; }
    div.dataTables_wrapper { padding: .75rem; }
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-file-text"></i>
    <span>Laporan</span>
</div>

<div class="page-header">
    <h4>Daftar Laporan</h4>
    @role('teknisi')
    <a href="{{ route('laporan.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i>Buat Laporan
    </a>
    @endrole
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
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
