@extends('layouts.app')

@section('css')
<style>
    div.dataTables_wrapper div.dataTables_filter { text-align: right; }
    div.dataTables_wrapper { padding: .75rem; }
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-users"></i>
    <span>Customer</span>
</div>

<div class="page-header">
    <h4>Daftar Customer</h4>
    <div class="d-flex gap-2">
        @can('edit customers')
        <a href="{{ route('customers.report-access') }}" class="btn btn-outline-primary btn-sm">
            <i class="ti ti-lock-open me-1"></i>Akses Laporan
        </a>
        @endcan
        @can('create customers')
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus me-1"></i>Tambah Customer
        </a>
        @endcan
    </div>
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
            <table id="customerTable" class="table table-hover mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Alamat</th>
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
    $('#customerTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route("customers.index") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '40px' },
            { data: 'name',    name: 'name' },
            { data: 'phone',   name: 'phone', defaultContent: '-' },
            { data: 'email',   name: 'email', defaultContent: '-' },
            { data: 'address', name: 'address', defaultContent: '-' },
            { data: 'action',  name: 'action', orderable: false, searchable: false },
        ],
        language: {
            processing:  'Memuat data…',
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty:   'Tidak ada data',
            emptyTable:  'Belum ada customer.',
            zeroRecords: 'Tidak ditemukan hasil.',
            paginate: { previous: '&laquo;', next: '&raquo;' },
        },
    });
});
</script>
@endsection
