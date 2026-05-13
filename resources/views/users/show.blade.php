@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    .card-footer { background: var(--bg); border-top: 1px solid var(--border); border-radius: 0 0 var(--radius-lg) var(--radius-lg) !important; }
    dl dt { font-size: .75rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; }
    dl dd { font-size: .9rem; color: var(--text); }
    h4 { color: var(--text); font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0">Detail User</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-4">Nama</dt><dd class="col-8">{{ $user->name }}</dd>
                <dt class="col-4">Email</dt><dd class="col-8">{{ $user->email }}</dd>
                <dt class="col-4">Role</dt><dd class="col-8">{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</dd>
                <dt class="col-4">Bergabung</dt><dd class="col-8">{{ $user->created_at->format('d/m/Y') }}</dd>
            </dl>
        </div>
        @can('edit users')
        <div class="card-footer">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">Edit</a>
        </div>
        @endcan
    </div>
</div>
@endsection
