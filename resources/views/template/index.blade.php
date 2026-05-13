@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    h4 { color: var(--text); font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container">
    <h4 class="mb-4">Custom Template</h4>

    <div class="card">
        <div class="card-body">
            <p class="text-muted">Halaman template laporan kustom. Tambahkan template laporan di sini.</p>
        </div>
    </div>
</div>
@endsection
