@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    .form-label { font-size: .8rem; font-weight: 600; color: var(--text-secondary); }
    .form-control, .form-select { border-radius: var(--radius-md); border-color: var(--border); font-size: .9rem; }
    .form-control:focus, .form-select:focus { border-color: var(--blue); box-shadow: 0 0 0 .2rem var(--blue-light); }
    h4 { color: var(--text); font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0">Edit User</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </form>
        </div>
    </div>
</div>
@endsection

