@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    .form-label { font-size: .8rem; font-weight: 600; color: var(--text-secondary); }
    .form-control, .form-select { border-radius: var(--radius-md); border-color: var(--border); font-size: .9rem; }
    .form-control:focus, .form-select:focus { border-color: var(--blue); box-shadow: 0 0 0 .2rem var(--blue-light); }
    input[type="file"].form-control { padding: .45rem .75rem; cursor: pointer; }
    h4 { color: var(--text); font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0">Edit Laporan</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('laporan.update', $laporan) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Deskripsi Laporan</label>
                    <textarea name="description" class="form-control" rows="5" required>{{ old('description', $laporan->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach(['draft','submitted','approved'] as $s)
                            <option value="{{ $s }}" {{ old('status', $laporan->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto (opsional)</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </form>
        </div>
    </div>
</div>
@endsection
