@extends('layouts.app')

@section('css')
<style>
    div.dataTables_wrapper div.dataTables_filter { text-align: right; }
    div.dataTables_wrapper { padding: .75rem; }
    .access-switch { cursor: pointer; width: 2.4em; height: 1.3em; }
    .ra-stat {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--blue-light); color: var(--blue);
        font-size: .8rem; font-weight: 600;
        padding: .35rem .75rem; border-radius: var(--radius-md);
    }
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <a href="{{ route('customers.index') }}">Customer</a>
    <i class="ti ti-chevron-right"></i>
    <span>Akses Laporan</span>
</div>

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary me-2">
        <i class="ti ti-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0">Akses Laporan Customer</h4>
        <div style="font-size:.82rem;color:var(--text-secondary);">
            Pilih customer yang boleh melihat laporan mereka di portal customer.
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body d-flex align-items-center flex-wrap gap-2" style="padding:.875rem 1rem;">
        <span class="ra-stat">
            <i class="ti ti-eye"></i>
            <span id="grantedCount">{{ $granted }}</span>&nbsp;dari&nbsp;{{ $total }}&nbsp;customer memiliki akses laporan
        </span>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="reportAccessTable" class="table table-hover mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Customer</th>
                        <th>Email</th>
                        <th>Akun Portal</th>
                        <th>Jumlah Tugas</th>
                        <th class="text-center">Akses Laporan</th>
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
    $('#reportAccessTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("customers.report-access") }}',
        columns: [
            { data: 'DT_RowIndex',   name: 'id', orderable: false, searchable: false, width: '40px' },
            { data: 'name',          name: 'name' },
            { data: 'email',         name: 'email', defaultContent: '-' },
            { data: 'portal_badge',  name: 'portal_badge', orderable: false, searchable: false },
            { data: 'tasks_count',   name: 'tasks_count', orderable: false, searchable: false, width: '90px' },
            { data: 'access_toggle', name: 'report_access', orderable: false, searchable: false, width: '110px' },
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

    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var baseUrl = '{{ url("customers") }}';

    $('#reportAccessTable').on('change', '.access-switch', function () {
        var el = this;
        el.disabled = true;
        fetch(baseUrl + '/' + el.dataset.id + '/report-access', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ access: el.checked }),
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (data) {
            document.getElementById('grantedCount').textContent = data.granted;
        })
        .catch(function () {
            el.checked = !el.checked; // revert on failure
            alert('Gagal mengubah akses laporan. Coba lagi.');
        })
        .finally(function () {
            el.disabled = false;
        });
    });
});
</script>
@endsection
