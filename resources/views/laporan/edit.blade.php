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
    .sig-canvas-wrap {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: #fff;
        position: relative;
        height: 140px;
    }
    .sig-canvas-wrap canvas { display: block; }
    .sig-empty-hint {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-secondary); font-size: .82rem;
        pointer-events: none;
    }
</style>
@endsection

@section('content')
<div class="container" style="max-width:640px;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-secondary me-2">
            <i class="ti ti-arrow-left"></i>
        </a>
        <h4 class="mb-0">
            {{ $laporan->status === 'draft' ? 'Lengkapi Laporan' : 'Edit Laporan' }}
        </h4>
    </div>

    @if($laporan->status === 'draft')
    <div class="alert alert-warning py-2 mb-3" style="font-size:.84rem;">
        <i class="ti ti-signature me-1"></i>
        Laporan tersimpan sebagai <strong>draft</strong>.
        Tambahkan tanda tangan customer untuk mengirim.
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info py-2 mb-3" style="font-size:.84rem;">
        <i class="ti ti-info-circle me-1"></i>{{ session('info') }}
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('laporan.update', $laporan) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="signature_cust" id="h_sig_cust">

                <div class="mb-3">
                    <label class="form-label">Deskripsi Laporan</label>
                    <textarea name="description" class="form-control" rows="5"
                              required>{{ old('description', $laporan->description) }}</textarea>
                </div>

                @if(Auth::user()->hasAnyRole(['admin', 'sales']))
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['draft', 'submitted', 'approved'] as $s)
                            <option value="{{ $s }}"
                                {{ old('status', $laporan->status) === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($laporan->photos && count($laporan->photos))
                <div class="mb-3">
                    <label class="form-label">Foto Saat Ini</label>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:6px;">
                        @foreach($laporan->photos as $path)
                        <img src="{{ asset('storage/' . $path) }}"
                             style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
                        @endforeach
                    </div>
                    <small class="text-muted">Upload foto baru di bawah untuk mengganti semua foto saat ini.</small>
                </div>
                @endif
                <div class="mb-4">
                    <label class="form-label">Upload Foto Baru
                        <span class="text-muted">(opsional · maks. 10 foto)</span>
                    </label>
                    <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                </div>

                @if($laporan->status === 'draft')
                {{-- Customer signature canvas — required to submit --}}
                <div class="mb-3">
                    <label class="form-label">
                        Tanda Tangan Customer
                        <span class="text-danger">*</span>
                        <span class="text-muted fw-normal">(wajib untuk mengirim laporan)</span>
                    </label>
                    <div class="sig-canvas-wrap" id="sigCustWrap">
                        <canvas id="sigCustCanvas"></canvas>
                        <div class="sig-empty-hint" id="sigCustEmpty">
                            <i class="ti ti-pencil me-1"></i> Tanda tangan di sini
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <span style="font-size:.75rem;color:var(--text-secondary);" id="sigCustStatus">
                            <i class="ti ti-clock"></i> Belum ditandatangani
                        </span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="sigCustClear">
                            <i class="ti ti-eraser me-1"></i>Hapus
                        </button>
                    </div>
                </div>

                @elseif($laporan->signature_cust)
                <div class="mb-3">
                    <label class="form-label">Tanda Tangan Customer</label>
                    <img src="{{ asset('storage/' . $laporan->signature_cust) }}" alt="TTD Customer"
                         style="max-height:80px;border:1px solid var(--border);border-radius:6px;padding:4px;background:#fff;display:block;">
                </div>
                @endif

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        @if($laporan->status === 'draft')
                            <i class="ti ti-send me-1"></i>Kirim Laporan
                        @else
                            <i class="ti ti-check me-1"></i>Perbarui
                        @endif
                    </button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@if($laporan->status === 'draft')
@section('js')
<script>
$(function () {
    var canvas  = document.getElementById('sigCustCanvas');
    var ctx     = canvas.getContext('2d');
    var drawing = false;
    var hasSig  = false;

    function resize() {
        var wrap  = canvas.parentElement;
        var ratio = window.devicePixelRatio || 1;
        canvas.width  = wrap.clientWidth  * ratio;
        canvas.height = wrap.clientHeight * ratio;
        ctx.scale(ratio, ratio);
        ctx.strokeStyle = '#1a1d23';
        ctx.lineWidth   = 2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
    }
    resize();
    window.addEventListener('resize', resize);

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var src  = e.touches ? e.touches[0] : e;
        return { x: src.clientX - rect.left, y: src.clientY - rect.top };
    }
    function startDraw(e) {
        e.preventDefault();
        drawing = true;
        var p = getPos(e);
        ctx.beginPath(); ctx.moveTo(p.x, p.y);
        if (!hasSig) {
            hasSig = true;
            $('#sigCustEmpty').hide();
            $('#sigCustStatus').html('<i class="ti ti-check" style="color:var(--green);"></i> <span style="color:var(--green);">Ditandatangani</span>');
        }
    }
    function draw(e) {
        if (!drawing) return;
        e.preventDefault();
        var p = getPos(e);
        ctx.lineTo(p.x, p.y); ctx.stroke();
    }
    function endDraw() {
        if (!drawing) return;
        drawing = false; ctx.closePath();
        $('#h_sig_cust').val(canvas.toDataURL('image/png'));
    }

    canvas.addEventListener('mousedown',  startDraw);
    canvas.addEventListener('mousemove',  draw);
    canvas.addEventListener('mouseup',    endDraw);
    canvas.addEventListener('mouseleave', endDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove',  draw,      { passive: false });
    canvas.addEventListener('touchend',   endDraw);

    $('#sigCustClear').on('click', function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSig = false;
        $('#sigCustEmpty').show();
        $('#sigCustStatus').html('<i class="ti ti-clock"></i> Belum ditandatangani');
        $('#h_sig_cust').val('');
    });
});
</script>
@endsection
@endif
