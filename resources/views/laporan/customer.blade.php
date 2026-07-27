@extends('layouts.app')

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex no-print">
    <a href="{{ route('customers.index') }}">Customer</a>
    <i class="ti ti-chevron-right"></i>
    <span>{{ $customer->name }}</span>
    <i class="ti ti-chevron-right"></i>
    <span>Laporan</span>
</div>

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
            <a href="{{ route('customers.show', $customer) }}" class="report-hero-pill">
                <i class="ti ti-user"></i> Profil
            </a>
            <button class="report-hero-pill" data-bs-toggle="modal" data-bs-target="#shareModal">
                <i class="ti ti-share"></i> Bagikan
            </button>
        </div>
    </div>
</div>

@if($reports->isEmpty())
<div style="text-align:center;padding:3rem 1rem;color:var(--text-secondary);">
    <i class="ti ti-file-off" style="font-size:3rem;opacity:.2;display:block;margin-bottom:.75rem;"></i>
    <p style="font-size:.9rem;">Belum ada laporan untuk customer ini.</p>
    @can('create customers')
    <a href="{{ route('tugas.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i>Buat Tugas Baru
    </a>
    @endcan
</div>
@else

{{-- Report cards grid --}}
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
         data-photo="{{ !empty($report->photos) ? asset('storage/' . $report->photos[0]) : '' }}"
         data-detail-url="{{ route('laporan.show', $report) }}">

        {{-- Thumb --}}
        <div class="cust-report-thumb">
            @if(!empty($report->photos))
                <img src="{{ asset('storage/' . $report->photos[0]) }}" alt="Foto Laporan">
            @else
                <i class="ti ti-file-text"></i>
            @endif
        </div>

        {{-- Body --}}
        <div class="cust-report-card-body">
            <div class="cust-report-card-title">{{ $report->task?->title ?? '-' }}</div>
            <div class="cust-report-card-sub">
                <i class="ti ti-user" style="font-size:.72rem;"></i> {{ $report->teknisi?->name ?? '-' }}
            </div>
            <div class="cust-report-card-desc">{{ $report->description }}</div>
        </div>

        {{-- Footer --}}
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

{{-- Share link modal --}}
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:1px solid var(--border);">
            <div class="modal-header" style="border-bottom:1px solid var(--border-light);">
                <h6 class="modal-title" style="font-weight:700;color:var(--text);">
                    <i class="ti ti-share me-2" style="color:var(--blue);"></i>Bagikan Laporan Customer
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:.82rem;color:var(--text-secondary);margin-bottom:.75rem;">
                    Link berikut memberi akses baca kepada customer selama <strong>30 hari</strong>.
                    Setelah itu link otomatis kadaluarsa.
                </p>
                <div class="input-group">
                    <input type="text" class="form-control" id="shareUrlInput"
                           value="{{ $signedUrl }}" readonly
                           style="font-size:.75rem;border-radius:var(--radius-md) 0 0 var(--radius-md);border-color:var(--border);">
                    <button class="btn btn-primary" id="copyShareBtn" type="button"
                            style="border-radius:0 var(--radius-md) var(--radius-md) 0;">
                        <i class="ti ti-copy me-1"></i>Salin
                    </button>
                </div>
                <div id="copyFeedback" style="font-size:.75rem;color:var(--green);margin-top:.4rem;display:none;">
                    <i class="ti ti-check"></i> Link disalin!
                </div>
                @can('edit customers')
                <div style="margin-top:1rem;padding-top:.85rem;border-top:1px solid var(--border-light);">
                    <button class="btn btn-outline-danger btn-sm" id="regenerateShareBtn" type="button">
                        <i class="ti ti-refresh me-1"></i>Perbarui Link
                    </button>
                    <div style="font-size:.75rem;color:var(--text-secondary);margin-top:.5rem;">
                        <i class="ti ti-alert-triangle" style="color:var(--orange);"></i>
                        Membuat link baru dan <strong>langsung menonaktifkan link lama</strong> yang pernah dibagikan.
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>

{{-- Report detail modal --}}
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
            <a id="rModalDetailLink" href="#" class="btn btn-primary btn-sm">
                <i class="ti ti-external-link me-1"></i>Lihat Detail Penuh
            </a>
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
    /* Copy share link */
    $('#copyShareBtn').on('click', function () {
        var url = $('#shareUrlInput').val();
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(function () {
                $('#copyFeedback').fadeIn(150).delay(2000).fadeOut(300);
            });
        } else {
            $('#shareUrlInput').select();
            document.execCommand('copy');
            $('#copyFeedback').fadeIn(150).delay(2000).fadeOut(300);
        }
    });

    /* Regenerate share link (invalidates the old one) */
    $('#regenerateShareBtn').on('click', function () {
        if (!confirm('Buat link baru? Link lama yang sudah dibagikan akan langsung tidak berlaku lagi.')) {
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        var csrf = document.querySelector('meta[name="csrf-token"]').content;
        fetch('{{ route("customers.report-token.regenerate", $customer) }}', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (data) {
            $('#shareUrlInput').val(data.signed_url);
            $('#copyFeedback').text('Link baru dibuat!').fadeIn(150).delay(2000).fadeOut(300, function () {
                $(this).text('Link disalin!');
            });
        })
        .catch(function () {
            alert('Gagal membuat link baru. Coba lagi.');
        })
        .finally(function () {
            $btn.prop('disabled', false);
        });
    });

    var statusLabels = { submitted: 'Terkirim', approved: 'Disetujui' };
    var statusClass  = { submitted: 'submitted', approved: 'approved' };

    /* Open report modal */
    $(document).on('click', '.cust-report-card', function () {
        var $c = $(this);
        $('#rModalTitle').text($c.data('task'));
        $('#rModalSub').text($c.data('customer'));
        $('#rModalTech').text($c.data('tech'));
        $('#rModalDate').text($c.data('date'));
        $('#rModalDesc').text($c.data('desc'));
        $('#rModalDetailLink').attr('href', $c.data('detail-url'));

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

    /* Photo click in modal → lightbox */
    $('#rModalPhoto').on('click', function () {
        openLightbox($('#rModalPhotoImg').attr('src'), $('#rModalTitle').text());
    });
});

function closeModal() {
    document.getElementById('rModalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

function openLightbox(src, caption) {
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
