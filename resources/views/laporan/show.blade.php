@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    .card-body strong { color: var(--text-secondary); font-size: .8rem; }
    .badge { border-radius: 20px; font-size: .7rem; padding: .3em .75em; }
    h4 { color: var(--text); font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0">Detail Laporan</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Tugas:</strong> {{ $laporan->task->title }}<br>
                    <strong>Customer:</strong> {{ $laporan->task->customer->name }}<br>
                    <strong>Teknisi:</strong> {{ $laporan->teknisi->name }}<br>
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong> <span class="badge bg-info">{{ ucfirst($laporan->status) }}</span><br>
                    <strong>Tanggal:</strong> {{ $laporan->created_at->format('d/m/Y H:i') }}<br>
                </div>
            </div>
            <div class="mb-3">
                <strong>Deskripsi:</strong>
                <p>{{ $laporan->description }}</p>
            </div>
            @if($laporan->photo)
                <div>
                    <strong>Foto:</strong><br>
                    <img src="{{ asset('storage/' . $laporan->photo) }}" class="img-fluid mt-2" style="max-height:300px">
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
