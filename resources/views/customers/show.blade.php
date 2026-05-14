@extends('layouts.app')

@section('css')
<style>
dl dt { font-size: .72rem; color: var(--text-secondary); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
dl dd { font-size: .9rem; color: var(--text); }
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <a href="{{ route('customers.index') }}">Customer</a>
    <i class="ti ti-chevron-right"></i>
    <span>Detail</span>
</div>

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary me-2">
        <i class="ti ti-arrow-left"></i>
    </a>
    <h4 class="mb-0">Detail Customer</h4>
</div>

<div class="panel-card">
    <div class="panel-card-header">
        <i class="ti ti-building"></i> Informasi Customer
    </div>
    <div style="padding: 1.25rem 1rem;">
        <dl class="row mb-0">
            <dt class="col-sm-3 col-4 mb-2">Nama</dt>
            <dd class="col-sm-9 col-8 mb-2" style="font-weight:600;">{{ $customer->name }}</dd>
            <dt class="col-sm-3 col-4 mb-2">Telepon</dt>
            <dd class="col-sm-9 col-8 mb-2">{{ $customer->phone ?? '—' }}</dd>
            <dt class="col-sm-3 col-4 mb-2">Email</dt>
            <dd class="col-sm-9 col-8 mb-2">{{ $customer->email ?? '—' }}</dd>
            <dt class="col-sm-3 col-4 mb-0">Alamat</dt>
            <dd class="col-sm-9 col-8 mb-0">{{ $customer->address ?? '—' }}</dd>
        </dl>
    </div>
    <div style="padding:.875rem 1rem;border-top:1px solid var(--border-light);display:flex;gap:.5rem;flex-wrap:wrap;">
        @can('edit customers')
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm">
            <i class="ti ti-pencil me-1"></i>Edit
        </a>
        @endcan
        @can('view customer reports')
        <a href="{{ route('customers.laporan', $customer) }}" class="btn btn-outline-primary btn-sm">
            <i class="ti ti-file-text me-1"></i>Lihat Laporan
        </a>
        @endcan
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>
@endsection
