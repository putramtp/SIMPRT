@extends('layouts.app')

@section('css')
<style>
    div.dataTables_wrapper div.dataTables_filter { text-align: right; }
    div.dataTables_wrapper { padding: .75rem; }
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-user-cog"></i>
    <span>Manage User</span>
</div>

<div class="page-header">
    <h4>Manajemen User</h4>
    @can('create users')
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i>Tambah User
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
            <table id="userTable" class="table table-hover mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
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
    $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route("users.index") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '40px' },
            { data: 'name',   name: 'name' },
            { data: 'email',  name: 'email' },
            { data: 'role',   name: 'role', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        language: {
            processing:  'Memuat data…',
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty:   'Tidak ada data',
            emptyTable:  'Belum ada user.',
            zeroRecords: 'Tidak ditemukan hasil.',
            paginate: { previous: '&laquo;', next: '&raquo;' },
        },
    });
});
</script>
@endsection
