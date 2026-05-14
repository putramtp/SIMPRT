@extends('layouts.public')

@php $pageTitle = $customer->name . ' — Laporan | SIPRT'; @endphp

@section('content')

{{-- Customer header --}}
<div class="cust-header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.75rem;">
        <div>
            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;opacity:.7;margin-bottom:.25rem;">
                <i class="ti ti-building"></i> Laporan Customer
            </div>
            <div class="cust-header-title">{{ $customer->name }}</div>
            @if($customer->address)
            <div class="cust-header-sub">{{ $customer->address }}</div>
            @endif
            <div class="cust-header-meta">
                @if($customer->phone)
                <span><i class="ti ti-phone"></i> {{ $customer->phone }}</span>
                @endif
                @if($customer->email)
                <span><i class="ti ti-mail"></i> {{ $customer->email }}</span>
                @endif
                <span><i class="ti ti-file-text"></i> {{ $reports->count() }} laporan</span>
            </div>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;" class="no-print">
            <button onclick="window.print()" class="report-hero-pill">
                <i class="ti ti-printer"></i> Cetak
            </button>
        </div>
    </div>
</div>

@if($reports->isEmpty())
<div style="text-align:center;padding:3rem 1rem;color:var(--text-secondary);">
    <i class="ti ti-file-off" style="font-size:3rem;opacity:.2;display:block;margin-bottom:.75rem;"></i>
    <p style="font-size:.9rem;">Belum ada laporan untuk customer ini.</p>
</div>
@else

<div class="cust-report-grid">
    @foreach($reports as $report)
    <div class="cust-report-card"
         data-id="{{ $report->id }}"
         data-task="{{ e($report->task?->title) }}"
         data-customer="{{ e($customer->name) }}"
         data-tech="{{ e($report->teknisi?->name) }}"
         data-date="{{ $report->created_at->format('d M Y, H:i') }}"
         data-status="{{ $report->status }}"
         data-desc="{{ e($report->description) }}"
         data-photo="{{ $report->photo ? asset('storage/' . $report->photo) : '' }}">

        <div class="cust-report-thumb">
            @if($report->photo)
                <img src="{{ asset('storage/' . $report->photo) }}" alt="Foto Laporan">
            @else
                <i class="ti ti-file-text"></i>
            @endif
        </div>

        <div class="cust-report-card-body">
            <div class="cust-report-card-title">{{ $report->task?->title ?? '-' }}</div>
            <div class="cust-report-card-sub">
                <i class="ti ti-user" style="font-size:.72rem;"></i> {{ $report->teknisi?->name ?? '-' }}
            </div>
            <div class="cust-report-card-desc">{{ $report->description }}</div>
        </div>

        <div class="cust-report-card-footer">
            <span>{{ $report->created_at->format('d M Y') }}</span>
            <span class="report-status-pill {{ $report->status }}" style="font-size:.65rem;padding:2px 8px;">
                {{ ucfirst($report->status) }}
            </span>
        </div>
    </div>
    @endforeach
</div>

@endif

{{-- Report detail modal (no "Lihat Detail Penuh" in public view) --}}
<div class="rmodal-overlay" id="rModalOverlay" onclick="closeModal()">
    <div class="rmodal" onclick="event.stopPropagation()">
        <div class="rmodal-header">
            <h5 id="rModalTitle">—</h5>
            <div class="sub" id="rModalSub">—</div>
        </div>
        <button class="rmodal-close" onclick="closeModal()"><i class="ti ti-x"></i></button>
        <div class="rmodal-body">
            <div id="rModalPhoto" class="rmodal-photo" style="display:none;">
                <img id="rModalPhotoImg" src="" alt="Foto Laporan">
                <div class="rmodal-photo-overlay"><i class="ti ti-zoom-in"></i></div>
            </div>
            <div class="rmodal-field">
                <span class="rmodal-key">Teknisi</span>
                <span class="rmodal-val" id="rModalTech">—</span>
            </div>
            <div class="rmodal-divider"></div>
            <div class="rmodal-field">
                <span class="rmodal-key">Status</span>
                <span class="rmodal-val" id="rModalStatus">—</span>
            </div>
            <div class="rmodal-field">
                <span class="rmodal-key">Tanggal</span>
                <span class="rmodal-val" id="rModalDate">—</span>
            </div>
            <div class="rmodal-divider"></div>
            <div class="rmodal-field">
                <span class="rmodal-key">Deskripsi Pekerjaan</span>
                <span class="rmodal-val" id="rModalDesc">—</span>
            </div>
        </div>
        <div class="rmodal-footer">
            <button onclick="closeModal()" class="btn btn-outline-secondary btn-sm no-print">Tutup</button>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()"><i class="ti ti-x"></i></button>
    <img class="lightbox-img" id="lightboxImg" src="" alt="" onclick="event.stopPropagation()">
</div>

@endsection

@section('js')
<script>
$(function () {
    var statusLabels = { submitted: 'Terkirim', approved: 'Disetujui' };
    var statusClass  = { submitted: 'submitted', approved: 'approved' };

    $(document).on('click', '.cust-report-card', function () {
        var $c = $(this);
        $('#rModalTitle').text($c.data('task'));
        $('#rModalSub').text($c.data('customer'));
        $('#rModalTech').text($c.data('tech'));
        $('#rModalDate').text($c.data('date'));
        $('#rModalDesc').text($c.data('desc'));

        var st = $c.data('status');
        $('#rModalStatus').html(
            '<span class="report-status-pill ' + (statusClass[st] || '') + '">' + (statusLabels[st] || st) + '</span>'
        );

        var photo = $c.data('photo');
        if (photo) {
            $('#rModalPhotoImg').attr('src', photo);
            $('#rModalPhoto').show();
        } else {
            $('#rModalPhoto').hide();
        }

        $('#rModalOverlay').addClass('open');
        document.body.style.overflow = 'hidden';
    });

    $('#rModalPhoto').on('click', function () {
        openLightbox($('#rModalPhotoImg').attr('src'));
    });
});

function closeModal() {
    document.getElementById('rModalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.getElementById('lightboxImg').src = '';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeLightbox(); closeModal(); }
});
</script>
@endsection
