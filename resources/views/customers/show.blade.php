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

{{-- ── Portal Akses ── --}}
@can('edit customers')
<div class="panel-card mt-3">
    <div class="panel-card-header">
        <i class="ti ti-key"></i> Portal Akses Customer
    </div>

    @if(session('success'))
    <div class="alert alert-success m-3 py-2" style="font-size:.85rem;">
        <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger m-3 py-2" style="font-size:.85rem;">{{ session('error') }}</div>
    @endif

    @if($customer->portalUser)
    {{-- Existing portal user: show info + reset password --}}
    <div style="padding:1rem 1.25rem;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:1rem;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--blue-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ti ti-user" style="color:var(--blue);font-size:18px;"></i>
            </div>
            <div>
                <div style="font-weight:600;font-size:.9rem;">{{ $customer->portalUser->name }}</div>
                <div style="font-size:.8rem;color:var(--text-secondary);">{{ $customer->portalUser->email }}</div>
            </div>
            <a href="{{ route('customer.login') }}" target="_blank"
               style="margin-left:auto;font-size:.75rem;color:var(--blue);">
                Halaman Login <i class="ti ti-external-link" style="font-size:11px;"></i>
            </a>
        </div>

        <details style="border:1px solid var(--border-light);border-radius:var(--radius-md);">
            <summary style="padding:8px 12px;font-size:.82rem;font-weight:600;cursor:pointer;color:var(--text-secondary);">
                <i class="ti ti-lock me-1"></i>Reset Password Portal
            </summary>
            <div style="padding:12px;">
                <form action="{{ route('customers.portal-user.reset-password', $customer) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label style="font-size:.78rem;font-weight:600;color:var(--text-secondary);">Password Baru</label>
                        <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror"
                               required minlength="8" placeholder="Min. 8 karakter">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label style="font-size:.78rem;font-weight:600;color:var(--text-secondary);">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-sm" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="ti ti-refresh me-1"></i>Reset Password
                    </button>
                </form>
            </div>
        </details>
    </div>

    @else
    {{-- No portal user yet: show create form --}}
    <div style="padding:1rem 1.25rem;">
        <p style="font-size:.83rem;color:var(--text-secondary);margin-bottom:1rem;">
            Belum ada akun portal untuk customer ini. Buat akun agar customer dapat login dan melihat laporan mereka.
        </p>
        <form action="{{ route('customers.portal-user.store', $customer) }}" method="POST">
            @csrf
            <div class="mb-2">
                <label style="font-size:.78rem;font-weight:600;color:var(--text-secondary);">Nama</label>
                <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                       value="{{ old('name', $customer->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-2">
                <label style="font-size:.78rem;font-weight:600;color:var(--text-secondary);">Email Login</label>
                <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                       value="{{ old('email', $customer->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-2">
                <label style="font-size:.78rem;font-weight:600;color:var(--text-secondary);">Password</label>
                <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror"
                       required minlength="8" placeholder="Min. 8 karakter">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label style="font-size:.78rem;font-weight:600;color:var(--text-secondary);">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control form-control-sm" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="ti ti-plus me-1"></i>Buat Akses Portal
            </button>
        </form>
    </div>
    @endif
</div>
@endcan

@endsection
