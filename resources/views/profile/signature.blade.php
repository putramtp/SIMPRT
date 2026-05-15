@extends('layouts.app')

@section('css')
<style>
.sig-setup-wrap {
    max-width: 520px;
    margin: 3rem auto 0;
}
.sig-setup-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.sig-setup-header {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border-light);
    background: var(--bg);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .82rem;
    font-weight: 700;
    color: var(--text);
}
.sig-setup-body {
    padding: 1.5rem 1.25rem;
}
.sig-setup-info {
    font-size: .84rem;
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    line-height: 1.6;
}
@media (min-width: 640px) {
    .sig-setup-body { padding: 2rem 1.75rem; }
}
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-writing"></i>
    <span>Atur Tanda Tangan</span>
</div>

<div class="sig-setup-wrap">
    <div class="sig-setup-card">
        <div class="sig-setup-header">
            <i class="ti ti-writing"></i> Atur Tanda Tangan
        </div>
        <div class="sig-setup-body">

            <p class="sig-setup-info">
                Tanda tangan Anda akan otomatis terisi di setiap laporan. Cukup diatur sekali — Anda bisa memperbaruinya kapan saja dari halaman ini.
            </p>

            @if(session('success'))
            <div class="alert alert-success py-2 mb-3" style="font-size:.85rem;">
                <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
            </div>
            @endif

            @error('signature')
            <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;">{{ $message }}</div>
            @enderror

            <form action="{{ route('profile.signature.store') }}" method="POST" id="sigForm">
                @csrf
                <input type="hidden" name="signature" id="h_signature">

                <div class="sig-pad-wrap mb-3">
                    <div class="sig-canvas-container" id="sigWrap">
                        <canvas id="sigCanvas" height="150"></canvas>
                        <div class="sig-canvas-empty" id="sigEmpty">
                            <i class="ti ti-pencil"></i> Tanda tangan di sini
                        </div>
                    </div>
                    <div class="sig-toolbar">
                        <span class="sig-status" id="sigStatus">
                            <i class="ti ti-clock"></i> Belum ditandatangani
                        </span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="sigClear">
                            <i class="ti ti-eraser me-1"></i>Hapus
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                    <i class="ti ti-check me-1"></i>Simpan Tanda Tangan
                </button>
            </form>

        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(function () {
    var canvas  = document.getElementById('sigCanvas');
    var ctx     = canvas.getContext('2d');
    var drawing = false;
    var hasSig  = false;

    function resize() {
        var rect  = canvas.parentElement.getBoundingClientRect();
        var ratio = window.devicePixelRatio || 1;
        canvas.width  = rect.width * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        ctx.scale(ratio, ratio);
        ctx.strokeStyle = '#1a1d23';
        ctx.lineWidth   = 2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
    }
    resize();
    window.addEventListener('resize', function() { resize(); if (hasSig) markSigned(); });

    @if(auth()->user()->signature)
    (function() {
        var img = new Image();
        img.onload = function() {
            var ratio = window.devicePixelRatio || 1;
            ctx.drawImage(img, 0, 0, canvas.width / ratio, canvas.height / ratio);
            markSigned();
            $('#h_signature').val(img.src);
        };
        img.src = @json(auth()->user()->signature);
    })();
    @endif

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var src  = e.touches ? e.touches[0] : e;
        return { x: src.clientX - rect.left, y: src.clientY - rect.top };
    }

    function markSigned() {
        hasSig = true;
        $('#sigWrap').addClass('has-sig');
        $('#sigEmpty').hide();
        $('#sigStatus').html('<i class="ti ti-check" style="color:var(--green);"></i> <span style="color:var(--green);">Ditandatangani</span>');
        $('#submitBtn').prop('disabled', false);
    }

    canvas.addEventListener('mousedown', function(e) {
        e.preventDefault();
        drawing = true;
        var p = getPos(e);
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
        if (!hasSig) markSigned();
    });
    canvas.addEventListener('mousemove', function(e) {
        if (!drawing) return;
        e.preventDefault();
        var p = getPos(e);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
    });
    canvas.addEventListener('mouseup',    function(e) { if (!drawing) return; drawing = false; ctx.closePath(); $('#h_signature').val(canvas.toDataURL('image/png')); });
    canvas.addEventListener('mouseleave', function(e) { if (!drawing) return; drawing = false; ctx.closePath(); $('#h_signature').val(canvas.toDataURL('image/png')); });
    canvas.addEventListener('touchstart', function(e) { e.preventDefault(); drawing = true; var p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); if (!hasSig) markSigned(); }, { passive: false });
    canvas.addEventListener('touchmove',  function(e) { if (!drawing) return; e.preventDefault(); var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }, { passive: false });
    canvas.addEventListener('touchend',   function(e) { if (!drawing) return; drawing = false; ctx.closePath(); $('#h_signature').val(canvas.toDataURL('image/png')); });

    $('#sigClear').on('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSig = false;
        $('#sigWrap').removeClass('has-sig');
        $('#sigEmpty').show();
        $('#sigStatus').html('<i class="ti ti-clock"></i> Belum ditandatangani');
        $('#h_signature').val('');
        $('#submitBtn').prop('disabled', true);
    });
});
</script>
@endsection
