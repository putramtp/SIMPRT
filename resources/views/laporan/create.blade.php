@extends('layouts.app')

@section('css')
<style>
.form-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 1rem;
}
.form-card-header {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-light);
    font-size: .78rem;
    font-weight: 700;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--bg);
}
.form-card-body { padding: 1.25rem 1rem; }
@media (min-width: 640px) { .form-card-body { padding: 1.5rem; } }

/* Live preview card */
.preview-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    position: sticky;
    top: calc(var(--topbar-h) + 1rem);
}
.preview-header {
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue) 100%);
    color: #fff;
    padding: 14px 16px;
}
.preview-header .company { font-size: .72rem; opacity: .7; margin-bottom: 2px; }
.preview-header .report-title { font-size: 1rem; font-weight: 700; }
.preview-header .meta { font-size: .75rem; opacity: .75; margin-top: 4px; }
.preview-body { padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; }
.preview-section { display: flex; flex-direction: column; gap: 3px; }
.preview-section-key {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--text-secondary);
}
.preview-section-val { font-size: .8rem; color: var(--text); white-space: pre-line; line-height: 1.5; }
.preview-section-val.empty { color: var(--text-secondary); font-style: italic; }
.preview-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: var(--blue-light);
    color: #0C447C;
    border-radius: 20px;
    padding: 2px 8px;
    font-size: .72rem;
    font-weight: 600;
}
.preview-divider { height: 1px; background: var(--border-light); }
.preview-img-thumb {
    border-radius: var(--radius-md);
    object-fit: cover;
    width: 100%;
    max-height: 120px;
    display: none;
}
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <a href="{{ route('laporan.index') }}">Laporan</a>
    <i class="ti ti-chevron-right"></i>
    <span>Buat Laporan</span>
</div>

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-secondary me-2">
        <i class="ti ti-arrow-left"></i>
    </a>
    <h4 class="mb-0">Form Laporan Teknisi</h4>
</div>

<form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" id="laporanForm">
@csrf
<input type="hidden" name="signature_tech" id="h_sig_tech">
<input type="hidden" name="signature_cust" id="h_sig_cust">
@if(request('task_id'))
<input type="hidden" name="task_id" value="{{ request('task_id') }}">
@endif

<div class="sp-layout">

    {{-- ── Form (main, 55%) ── --}}
    <div class="sp-main">

        {{-- Main form fields --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="ti ti-file-plus"></i> Isi Laporan
            </div>
            <div class="form-card-body">

                <div class="mb-3">
                    <label class="form-label">Tugas <span class="text-danger">*</span></label>
                    <select name="task_id" id="f_task"
                            class="form-select @error('task_id') is-invalid @enderror"
                            {{ request('task_id') ? 'disabled' : '' }}
                            required>
                        <option value="">-- Pilih Tugas --</option>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}"
                                    data-customer="{{ e($task->customer->name) }}"
                                    {{ (old('task_id') ?? request('task_id')) == $task->id ? 'selected' : '' }}>
                                {{ $task->title }} — {{ $task->customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('task_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi Laporan <span class="text-danger">*</span></label>
                    <textarea name="description" id="f_desc"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="6"
                              placeholder="Jelaskan pekerjaan yang telah dilakukan, kondisi lokasi, temuan, dan hasil akhir…"
                              required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Foto Dokumentasi <span class="text-muted">(opsional)</span></label>
                    {{-- Drag-drop zone --}}
                    <div class="drop-zone" id="dropZone">
                        <input type="file" name="photo" id="f_photo" accept="image/*">
                        <i class="ti ti-cloud-upload drop-zone-icon"></i>
                        <div class="drop-zone-title">Drag & drop foto di sini</div>
                        <div class="drop-zone-sub">atau klik untuk memilih · JPG, PNG, WEBP · Maks. 2MB</div>
                    </div>
                    <div class="drop-zone-preview" id="dropPreview">
                        <img id="dropPreviewImg" src="" alt="Preview">
                        <button type="button" class="drop-zone-remove" id="dropRemove" title="Hapus foto">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    @error('photo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-send me-1"></i>Kirim Laporan
                    </button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>

            </div>
        </div>

        {{-- Signature canvas section --}}
        <div class="sig-section">
            <div class="sig-section-header">
                <i class="ti ti-writing"></i> Tanda Tangan
                <span style="font-size:.7rem;color:var(--text-secondary);margin-left:auto;">Gunakan mouse/jari untuk menandatangani</span>
            </div>
            <div class="sig-grid">

                {{-- Teknisi signature --}}
                <div class="sig-pad-wrap">
                    <div class="sig-pad-label">Tanda Tangan Teknisi</div>
                    <div class="sig-canvas-container" id="sigTechWrap">
                        <canvas id="sigTechCanvas" height="120"></canvas>
                        <div class="sig-canvas-empty" id="sigTechEmpty">
                            <i class="ti ti-pencil"></i> Tanda tangan di sini
                        </div>
                    </div>
                    <div class="sig-toolbar">
                        <span class="sig-status" id="sigTechStatus">
                            <i class="ti ti-clock"></i> Belum ditandatangani
                        </span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="sigTechClear">
                            <i class="ti ti-eraser me-1"></i>Hapus
                        </button>
                    </div>
                </div>

                {{-- Customer / site representative signature --}}
                <div class="sig-pad-wrap">
                    <div class="sig-pad-label">Tanda Tangan Perwakilan</div>
                    <div class="sig-canvas-container" id="sigCustWrap">
                        <canvas id="sigCustCanvas" height="120"></canvas>
                        <div class="sig-canvas-empty" id="sigCustEmpty">
                            <i class="ti ti-pencil"></i> Tanda tangan di sini
                        </div>
                    </div>
                    <div class="sig-toolbar">
                        <span class="sig-status" id="sigCustStatus">
                            <i class="ti ti-clock"></i> Belum ditandatangani
                        </span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="sigCustClear">
                            <i class="ti ti-eraser me-1"></i>Hapus
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>{{-- /.sp-main --}}

    {{-- ── Live preview (aside, 45%) ── --}}
    <div class="sp-aside">
        <div class="preview-card">
            <div class="preview-header">
                <div class="company">SIPRT — Laporan Teknisi</div>
                <div class="report-title" id="p_taskTitle">Pilih tugas…</div>
                <div class="meta">
                    Teknisi: {{ Auth::user()->name }} &nbsp;·&nbsp;
                    {{ now()->format('d M Y') }}
                </div>
            </div>
            <div class="preview-body">
                <div class="preview-section">
                    <span class="preview-section-key">Tugas</span>
                    <span class="preview-section-val empty" id="p_task">Belum dipilih</span>
                </div>
                <div class="preview-section">
                    <span class="preview-section-key">Customer</span>
                    <span class="preview-section-val empty" id="p_customer">—</span>
                </div>
                <div class="preview-divider"></div>
                <div class="preview-section">
                    <span class="preview-section-key">Laporan Pekerjaan</span>
                    <span class="preview-section-val empty" id="p_desc">Belum diisi…</span>
                </div>
                <div class="preview-divider"></div>
                <div class="preview-section" id="p_photoSection" style="display:none;">
                    <span class="preview-section-key">Foto</span>
                    <img class="preview-img-thumb" id="p_photoThumb" src="" alt="Preview">
                </div>
                <div class="preview-section" id="p_sigSection" style="display:none;">
                    <span class="preview-section-key">Tanda Tangan</span>
                    <span class="preview-section-val" id="p_sigStatus"></span>
                </div>
            </div>
        </div>
    </div>{{-- /.sp-aside --}}

</div>{{-- /.sp-layout --}}
</form>

@endsection

@section('js')
<script>
$(function () {

    /* ══════════════════════════════
       DRAG-DROP UPLOAD
    ══════════════════════════════ */
    var $zone     = $('#dropZone');
    var $input    = $('#f_photo');
    var $preview  = $('#dropPreview');
    var $previewImg = $('#dropPreviewImg');
    var $remove   = $('#dropRemove');

    $zone.on('dragover dragenter', function(e) {
        e.preventDefault(); e.stopPropagation();
        $zone.addClass('drag-active');
    }).on('dragleave dragend drop', function(e) {
        e.preventDefault(); e.stopPropagation();
        $zone.removeClass('drag-active');
    }).on('drop', function(e) {
        var file = e.originalEvent.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            setFile(file);
        }
    });

    $input.on('change', function() {
        if (this.files[0]) setFile(this.files[0]);
    });

    $remove.on('click', function() {
        $input.val('');
        $preview.hide();
        $previewImg.attr('src', '');
        $('#p_photoSection').hide();
        $('#p_photoThumb').hide().attr('src', '');
        syncPreview();
    });

    function setFile(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $previewImg.attr('src', e.target.result);
            $preview.show();
            // Update live preview
            $('#p_photoThumb').attr('src', e.target.result).show();
            $('#p_photoSection').show();
        };
        reader.readAsDataURL(file);

        // Sync the actual input file (for drop case)
        if ($input[0].files[0] !== file) {
            var dt = new DataTransfer();
            dt.items.add(file);
            $input[0].files = dt.files;
        }
        syncPreview();
    }

    /* ══════════════════════════════
       SIGNATURE PADS
    ══════════════════════════════ */
    function SigPad(canvasId, hiddenId, wrapId, emptyId, statusId, clearBtnId, initialSig) {
        var canvas  = document.getElementById(canvasId);
        var ctx     = canvas.getContext('2d');
        var drawing = false;
        var hasSig  = false;
        var $wrap   = $('#' + wrapId);
        var $empty  = $('#' + emptyId);
        var $status = $('#' + statusId);
        var $hidden = $('#' + hiddenId);

        function resize() {
            var rect  = canvas.parentElement.getBoundingClientRect();
            var ratio = window.devicePixelRatio || 1;
            canvas.width  = rect.width  * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            ctx.scale(ratio, ratio);
            ctx.strokeStyle = '#1a1d23';
            ctx.lineWidth   = 2;
            ctx.lineCap     = 'round';
            ctx.lineJoin    = 'round';
        }
        resize();

        if (initialSig) {
            var img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                var ratio = window.devicePixelRatio || 1;
                ctx.drawImage(img, 0, 0, canvas.width / ratio, canvas.height / ratio);
                hasSig = true;
                $wrap.addClass('has-sig');
                $empty.hide();
                $status.html('<i class="ti ti-check" style="color:var(--green);"></i> <span style="color:var(--green);">Ditandatangani</span>').addClass('signed');
                $hidden.val(canvas.toDataURL('image/png'));
                syncPreview();
            };
            img.src = initialSig;
        }

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
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
            if (!hasSig) {
                hasSig = true;
                $wrap.addClass('has-sig');
                $empty.hide();
                $status.html('<i class="ti ti-check" style="color:var(--green);"></i> <span style="color:var(--green);">Ditandatangani</span>').addClass('signed');
            }
        }
        function draw(e) {
            if (!drawing) return;
            e.preventDefault();
            var p = getPos(e);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
        }
        function endDraw(e) {
            if (!drawing) return;
            drawing = false;
            ctx.closePath();
            // Save to hidden input
            $hidden.val(canvas.toDataURL('image/png'));
            syncPreview();
        }

        canvas.addEventListener('mousedown',  startDraw);
        canvas.addEventListener('mousemove',  draw);
        canvas.addEventListener('mouseup',    endDraw);
        canvas.addEventListener('mouseleave', endDraw);
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove',  draw,      { passive: false });
        canvas.addEventListener('touchend',   endDraw);

        $('#' + clearBtnId).on('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasSig = false;
            $wrap.removeClass('has-sig');
            $empty.show();
            $status.html('<i class="ti ti-clock"></i> Belum ditandatangani').removeClass('signed');
            $hidden.val('');
            syncPreview();
        });
    }

    SigPad('sigTechCanvas', 'h_sig_tech', 'sigTechWrap', 'sigTechEmpty', 'sigTechStatus', 'sigTechClear', @json($userSignature ?? null));
    SigPad('sigCustCanvas', 'h_sig_cust', 'sigCustWrap', 'sigCustEmpty', 'sigCustStatus', 'sigCustClear');

    /* ══════════════════════════════
       LIVE PREVIEW SYNC
    ══════════════════════════════ */
    var statusLabels = {
        submitted: '<i class="ti ti-send"></i> Terkirim',
        approved:  '<i class="ti ti-circle-check"></i> Disetujui',
    };

    function syncPreview() {
        var $taskOpt = $('#f_task option:selected');
        var taskText = $taskOpt.val() ? $taskOpt.text().split(' — ')[0] : '';
        var customer = $taskOpt.data('customer') || '';
        var desc     = $('#f_desc').val();

        if (taskText) {
            $('#p_taskTitle').text(taskText);
            $('#p_task').text(taskText).removeClass('empty');
            $('#p_customer').text(customer).removeClass('empty');
        } else {
            $('#p_taskTitle').text('Pilih tugas…');
            $('#p_task').text('Belum dipilih').addClass('empty');
            $('#p_customer').text('—').addClass('empty');
        }

        if (desc.trim()) {
            $('#p_desc').text(desc).removeClass('empty');
        } else {
            $('#p_desc').text('Belum diisi…').addClass('empty');
        }

        // Signatures
        var sigTech = !!$('#h_sig_tech').val();
        var sigCust = !!$('#h_sig_cust').val();
        if (sigTech || sigCust) {
            var parts = [];
            if (sigTech) parts.push('Teknisi ✓');
            if (sigCust) parts.push('Perwakilan ✓');
            $('#p_sigStatus').text(parts.join(' · '));
            $('#p_sigSection').show();
        } else {
            $('#p_sigSection').hide();
        }
    }

    $('#f_task').on('change', syncPreview);
    $('#f_desc').on('input', syncPreview);

    syncPreview();

    /* ══════════════════════════════
       BACKGROUND SYNC (offline submit)
    ══════════════════════════════ */
    $('#laporanForm').on('submit', function(e) {
        if (navigator.onLine) return; // online — normal submit

        e.preventDefault();
        var $btn = $(this).find('[type=submit]');
        $btn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i>Menyimpan offline…');

        var taskId     = $('#f_task').val();
        var desc       = $('#f_desc').val();
        var sigTech    = $('#h_sig_tech').val() || '';
        var sigCust    = $('#h_sig_cust').val() || '';
        var csrfToken  = $('meta[name="csrf-token"]').attr('content');
        var photoFile  = document.getElementById('f_photo').files[0] || null;

        function saveAndSync(blobData, blobName) {
            var req = indexedDB.open('siprt-offline', 1);
            req.onupgradeneeded = function(ev) {
                var db = ev.target.result;
                if (!db.objectStoreNames.contains('laporan-queue')) {
                    db.createObjectStore('laporan-queue', { keyPath: 'id', autoIncrement: true });
                }
            };
            req.onsuccess = function(ev) {
                var db = ev.target.result;
                var item = {
                    task_id:        taskId,
                    description:    desc,
                    signature_tech: sigTech,
                    signature_cust: sigCust,
                    csrf_token:     csrfToken,
                    photo_blob:     blobData,
                    photo_name:     blobName,
                };
                db.transaction('laporan-queue', 'readwrite').objectStore('laporan-queue').add(item);

                if ('serviceWorker' in navigator && 'SyncManager' in window) {
                    navigator.serviceWorker.ready.then(function(sw) {
                        return sw.sync.register('laporan-sync');
                    }).then(function() {
                        showOfflineToast('Laporan disimpan. Akan dikirim otomatis saat online.');
                    }).catch(function() {
                        showOfflineToast('Laporan disimpan offline.');
                    });
                } else {
                    showOfflineToast('Laporan disimpan. Kirim ulang saat koneksi tersedia.');
                }

                $btn.prop('disabled', false).html('<i class="ti ti-send me-1"></i>Kirim Laporan');
            };
        }

        if (photoFile) {
            saveAndSync(photoFile, photoFile.name);
        } else {
            saveAndSync(null, null);
        }
    });

    function showOfflineToast(msg) {
        var $t = $('<div class="notif-toast" style="position:fixed;bottom:80px;left:50%;transform:translateX(-50%);z-index:9999;min-width:280px;max-width:340px;padding:.75rem 1rem;display:flex;align-items:center;gap:.5rem;font-size:.82rem;">'
            + '<i class="ti ti-wifi-off" style="color:var(--orange);flex-shrink:0;"></i>'
            + '<span>' + msg + '</span></div>').appendTo('body');
        setTimeout(function() { $t.fadeOut(400, function() { $t.remove(); }); }, 5000);
    }
});
</script>
@endsection
