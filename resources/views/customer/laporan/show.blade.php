@extends('layouts.customer')

@section('css')
<style>
.report-desc-text { font-size:.875rem; line-height:1.7; color:var(--text); white-space:pre-line; }
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <a href="{{ route('customer.laporan') }}">Laporan Saya</a>
    <i class="ti ti-chevron-right"></i>
    <span>Detail</span>
</div>

{{-- Hero header --}}
<div class="report-hero">
    <div class="report-hero-tag">
        <i class="ti ti-file-text"></i> Laporan Teknisi
    </div>
    <h1 class="report-hero-title">{{ $laporan->task->title }}</h1>
    <div class="report-hero-meta">
        <span><i class="ti ti-user"></i> {{ $laporan->teknisi->name }}</span>
        <span><i class="ti ti-building"></i> {{ $laporan->task->customer->name }}</span>
        <span><i class="ti ti-calendar"></i> {{ $laporan->created_at->format('d M Y, H:i') }}</span>
    </div>
    <div class="report-hero-actions">
        <span class="report-status-pill {{ $laporan->status }}">
            @if($laporan->status === 'approved')
                <i class="ti ti-circle-check"></i> Disetujui
            @elseif($laporan->status === 'rejected')
                <i class="ti ti-circle-x"></i> Ditolak
            @else
                <i class="ti ti-send"></i> Terkirim
            @endif
        </span>
        <button onclick="window.print()" class="report-hero-pill">
            <i class="ti ti-printer"></i> Cetak
        </button>
        <a href="{{ route('laporan.pdf', $laporan) }}" class="report-hero-pill" target="_blank">
            <i class="ti ti-file-download"></i> PDF
        </a>
        <a href="{{ route('customer.laporan') }}" class="report-hero-pill">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- 2-col body --}}
<div class="report-body">

    <div class="report-body-main">

        <div class="report-info-card mb-3">
            <div class="report-info-card-header">
                <i class="ti ti-file-description"></i> Deskripsi Pekerjaan
            </div>
            <div class="report-info-card-body">
                <p class="report-desc-text mb-0">{{ $laporan->description }}</p>
            </div>
        </div>

        <div class="report-info-card mb-3">
            <div class="report-info-card-header">
                <i class="ti ti-info-circle"></i> Info Tugas
            </div>
            <div class="report-info-card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="report-field">
                            <span class="report-field-key">Customer</span>
                            <span class="report-field-val">{{ $laporan->task->customer->name }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="report-field">
                            <span class="report-field-key">Teknisi</span>
                            <span class="report-field-val">{{ $laporan->teknisi->name }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="report-field">
                            <span class="report-field-key">Status Laporan</span>
                            <span class="report-field-val">
                                <span class="report-status-pill {{ $laporan->status }}">{{ ucfirst($laporan->status) }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="report-field">
                            <span class="report-field-key">Tanggal Kirim</span>
                            <span class="report-field-val">{{ $laporan->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                    @if($laporan->task->due_date)
                    <div class="col-6">
                        <div class="report-field">
                            <span class="report-field-key">Deadline Tugas</span>
                            <span class="report-field-val">{{ $laporan->task->due_date->format('d M Y') }}</span>
                        </div>
                    </div>
                    @endif
                    @if($laporan->task->description)
                    <div class="col-12">
                        <div class="report-field">
                            <span class="report-field-key">Deskripsi Tugas</span>
                            <span class="report-field-val">{{ $laporan->task->description }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($laporan->signature_tech || $laporan->signature_cust)
        <div class="report-info-card mb-3">
            <div class="report-info-card-header">
                <i class="ti ti-writing"></i> Tanda Tangan
            </div>
            <div class="report-info-card-body">
                <div class="row g-3">
                    @if($laporan->signature_tech)
                    <div class="col-6">
                        <div class="report-field">
                            <span class="report-field-key">Teknisi</span>
                            <img src="{{ $laporan->signature_tech }}" alt="TTD Teknisi"
                                 style="max-height:80px;border:1px solid var(--border);border-radius:var(--radius-md);padding:4px;background:#fff;">
                        </div>
                    </div>
                    @endif
                    @if($laporan->signature_cust)
                    <div class="col-6">
                        <div class="report-field">
                            <span class="report-field-key">Customer</span>
                            <img src="{{ $laporan->signature_cust }}" alt="TTD Customer"
                                 style="max-height:80px;border:1px solid var(--border);border-radius:var(--radius-md);padding:4px;background:#fff;">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>

    <div class="report-body-aside">

        @php $allPhotos = $laporan->photos ?? []; @endphp
        <div class="report-info-card mb-3">
            <div class="report-info-card-header">
                <i class="ti ti-photo"></i> Foto Dokumentasi
                @if(count($allPhotos) > 0)
                <span style="margin-left:auto;font-size:.72rem;color:var(--text-secondary);">{{ count($allPhotos) }} foto</span>
                @endif
            </div>
            @if(count($allPhotos) > 0)
            <div style="padding:.75rem;display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($allPhotos as $i => $photoPath)
                <div class="photo-gallery-item"
                     data-src="{{ asset('storage/' . $photoPath) }}"
                     data-caption="Foto {{ $i + 1 }} — {{ $laporan->task->title }}"
                     style="flex:0 0 auto;width:{{ count($allPhotos) === 1 ? '100%' : 'calc(50% - 4px)' }};cursor:pointer;position:relative;border-radius:var(--radius-md);overflow:hidden;">
                    <img src="{{ asset('storage/' . $photoPath) }}" alt="Foto {{ $i + 1 }}"
                         style="width:100%;height:{{ count($allPhotos) === 1 ? '260px' : '140px' }};object-fit:cover;display:block;">
                    <div class="photo-gallery-overlay"><i class="ti ti-zoom-in"></i></div>
                </div>
                @endforeach
            </div>
            @else
            <div style="padding:2rem;text-align:center;color:var(--text-secondary);">
                <i class="ti ti-photo-off" style="font-size:2rem;opacity:.25;display:block;margin-bottom:.5rem;"></i>
                <span style="font-size:.8rem;">Tidak ada foto</span>
            </div>
            @endif
        </div>

        <div class="report-info-card">
            <div class="report-info-card-header">
                <i class="ti ti-settings"></i> Aksi
            </div>
            <div style="padding:.875rem;display:flex;flex-direction:column;gap:.5rem;">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="ti ti-printer me-1"></i>Cetak Laporan
                </button>
                <a href="{{ route('laporan.pdf', $laporan) }}" class="btn btn-outline-secondary btn-sm w-100" target="_blank">
                    <i class="ti ti-file-download me-1"></i>Download PDF
                </a>
            </div>
        </div>

    </div>

</div>

{{-- Lightbox --}}
<div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()"><i class="ti ti-x"></i></button>
    <img class="lightbox-img" id="lightboxImg" src="" alt="" onclick="event.stopPropagation()">
    <div class="lightbox-caption" id="lightboxCaption"></div>
</div>

@endsection

@section('js')
<script>
function openLightbox(src, caption) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxCaption').textContent = caption || '';
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.getElementById('lightboxImg').src = '';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });
$(function () {
    $(document).on('click', '.photo-gallery-item', function() {
        openLightbox($(this).data('src'), $(this).data('caption'));
    });
});
</script>
@endsection
