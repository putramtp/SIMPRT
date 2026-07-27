@extends('layouts.app')

@section('css')
<style>
    div.dataTables_wrapper div.dataTables_filter { text-align: right; }
    div.dataTables_wrapper { padding: .75rem; }
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-clipboard-list"></i>
    <span>Tugas</span>
</div>

<div class="page-header">
    <h4>Daftar Tugas</h4>
    @can('create customers')
    <a href="{{ route('tugas.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i>Buat Tugas
    </a>
    @endcan
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
            <table id="tugasTable" class="table table-hover mb-0 w-100">
                <thead class="table-light">
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
</div>
@endsection

@section('js')
<script>
$(function () {
    $('#tugasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("tugas.index") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '40px' },
            { data: 'title',         name: 'title' },
            { data: 'customer_name', name: 'customer.name', searchable: false },
            { data: 'assignee_name', name: 'assignees.name', searchable: false, orderable: false },
            { data: 'status_badge',  name: 'status', searchable: false },
            { data: 'due_date_fmt',  name: 'due_date', searchable: false },
            { data: 'action',        name: 'action', orderable: false, searchable: false },
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
    });
});
</script>
@endsection
