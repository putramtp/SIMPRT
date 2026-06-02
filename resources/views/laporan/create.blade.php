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

/* Multi-photo grid */
.photos-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}
.photo-thumb-item {
    position: relative;
    width: 80px;
    height: 80px;
    flex-shrink: 0;
}
.photo-thumb-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    display: block;
}
.photo-thumb-remove {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #e53e3e;
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    cursor: pointer;
    z-index: 2;
    padding: 0;
    line-height: 1;
}
.photo-thumb-remove:hover { background: #c53030; }
.photos-count {
    font-size: .78rem;
    color: var(--text-secondary);
    margin-top: 6px;
}

/* Template fields */
.template-section-heading {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--blue);
    padding: 0 0 6px;
    border-bottom: 1px solid var(--border-light);
    margin-bottom: 12px;
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
                    <label class="form-label">Foto Dokumentasi <span class="text-muted">(opsional · maks. 10 foto)</span></label>
                    {{-- Drag-drop zone --}}
                    <div class="drop-zone" id="dropZone">
                        <input type="file" name="photos[]" id="f_photos" accept="image/*" multiple>
                        <i class="ti ti-cloud-upload drop-zone-icon"></i>
                        <div class="drop-zone-title">Drag & drop foto di sini</div>
                        <div class="drop-zone-sub">atau klik untuk memilih · JPG, PNG, WEBP · Maks. 2MB per foto</div>
                    </div>
                    <div class="photos-grid" id="photosGrid"></div>
                    <div class="photos-count" id="photosCount" style="display:none;"></div>
                    @error('photos')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    @error('photos.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

            </div>
        </div>

        {{-- Dynamic template fields (shown when selected task has a template) --}}
        <div class="form-card" id="templateSection" style="display:none;">
            <div class="form-card-header">
                <i class="ti ti-layout-list"></i>
                <span id="templateSectionTitle">Formulir Tugas</span>
            </div>
            <div class="form-card-body" id="templateFields"></div>
        </div>

        {{-- Customer signature --}}
        <div class="sig-section">
            <div class="sig-section-header">
                <i class="ti ti-writing"></i> Tanda Tangan Customer
                <span style="font-size:.7rem;color:var(--text-secondary);margin-left:auto;">Gunakan mouse/jari untuk menandatangani</span>
            </div>
            <div class="sig-pad-wrap">
                <div class="sig-pad-label">Tanda Tangan Perwakilan Customer</div>
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

        {{-- Submit — bottom of form, after signatures --}}
        <div class="d-flex gap-2 mt-2 mb-1">
            <button type="submit" class="btn btn-primary" style="flex:1;">
                <i class="ti ti-send me-1"></i>Kirim Laporan
            </button>
            <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">Batal</a>
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
                    <span class="preview-section-val" id="p_photoCount"></span>
                </div>
                <div class="preview-section" id="p_templateSection" style="display:none;">
                    <span class="preview-section-key">Formulir</span>
                    <span class="preview-section-val" id="p_templateCount"></span>
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
       MULTIPLE PHOTO UPLOAD
    ══════════════════════════════ */
    var $zone       = $('#dropZone');
    var $input      = $('#f_photos');
    var $grid       = $('#photosGrid');
    var $countLabel = $('#photosCount');
    var filesArr    = []; // Array<File>

    $zone.on('dragover dragenter', function(e) {
        e.preventDefault(); e.stopPropagation();
        $zone.addClass('drag-active');
    }).on('dragleave dragend drop', function(e) {
        e.preventDefault(); e.stopPropagation();
        $zone.removeClass('drag-active');
    }).on('drop', function(e) {
        var files = e.originalEvent.dataTransfer.files;
        addFiles(files);
    });

    $input.on('change', function() {
        var captured = Array.from(this.files); // copy refs before clearing
        this.value = '';                        // reset so same file can be re-selected
        captured.forEach(function(f) {
            if (f.type.startsWith('image/') && filesArr.length < 10) {
                filesArr.push(f);
            }
        });
        renderGrid();
    });

    function addFiles(fileList) {
        Array.from(fileList).forEach(function(f) {
            if (f.type.startsWith('image/') && filesArr.length < 10) {
                filesArr.push(f);
            }
        });
        renderGrid();
    }

    function renderGrid() {
        $grid.empty();
        filesArr.forEach(function(file, idx) {
            var url  = URL.createObjectURL(file);
            var $item = $('<div class="photo-thumb-item">'
                + '<img class="photo-thumb-img" src="' + url + '" alt="foto ' + (idx+1) + '">'
                + '<button type="button" class="photo-thumb-remove" data-idx="' + idx + '" title="Hapus foto">'
                + '<i class="ti ti-x"></i></button>'
                + '</div>');
            $grid.append($item);
        });

        if (filesArr.length > 0) {
            $countLabel.text(filesArr.length + ' foto dipilih').show();
            $('#p_photoSection').show();
            $('#p_photoCount').text(filesArr.length + ' foto');
        } else {
            $countLabel.hide();
            $('#p_photoSection').hide();
        }
        syncFileInput();
        syncPreview();
    }

    function syncFileInput() {
        var dt = new DataTransfer();
        filesArr.forEach(function(f) { dt.items.add(f); });
        document.getElementById('f_photos').files = dt.files;
    }

    $grid.on('click', '.photo-thumb-remove', function() {
        var idx = parseInt($(this).data('idx'), 10);
        filesArr.splice(idx, 1);
        renderGrid();
    });

    /* ══════════════════════════════
       TEMPLATE FIELDS
    ══════════════════════════════ */
    var taskTemplates = @json($taskTemplates);

    function renderTemplate(taskId) {
        var tpl = taskId ? taskTemplates[taskId] : null;
        if (!tpl || !tpl.sections || tpl.sections.length === 0) {
            $('#templateSection').hide();
            $('#p_templateSection').hide();
            return;
        }
        $('#templateSection').show();
        $('#templateSectionTitle').text(tpl.name);

        var $body = $('#templateFields').empty();
        var fieldCount = 0;

        tpl.sections.forEach(function(section) {
            var fields = section.fields || [];
            var visibleFields = fields.filter(function(f) {
                return f.type !== 'photo' && f.type !== 'signature';
            });
            if (visibleFields.length === 0) return;

            if (section.title) {
                $body.append('<div class="template-section-heading">' + escHtml(section.title) + '</div>');
            }
            visibleFields.forEach(function(field) {
                $body.append(buildField(field));
                fieldCount++;
            });
        });

        if (fieldCount === 0) {
            $('#templateSection').hide();
            $('#p_templateSection').hide();
        } else {
            $('#p_templateSection').show();
            $('#p_templateCount').text(fieldCount + ' field formulir');
        }
    }

    function escHtml(str) {
        return $('<span>').text(str || '').html();
    }

    function buildField(field) {
        var name  = 'template_data[' + field.id + ']';
        var req   = field.required ? ' required' : '';
        var phAtt = field.placeholder ? ' placeholder="' + escHtml(field.placeholder) + '"' : '';
        var label = '<label class="form-label">' + escHtml(field.label)
            + (field.required ? ' <span class="text-danger">*</span>' : '') + '</label>';
        var input = '';

        switch (field.type) {
            case 'text':
                input = '<input type="text" name="' + name + '" class="form-control"' + phAtt + req + '>';
                break;
            case 'textarea':
                input = '<textarea name="' + name + '" class="form-control" rows="3"' + phAtt + req + '></textarea>';
                break;
            case 'number':
                input = '<input type="number" name="' + name + '" class="form-control"' + phAtt + req + '>';
                break;
            case 'date':
                input = '<input type="date" name="' + name + '" class="form-control"' + req + '>';
                break;
            case 'checkbox':
                return '<div class="mb-3"><div class="form-check">'
                    + '<input type="hidden" name="' + name + '" value="0">'
                    + '<input type="checkbox" name="' + name + '" value="1" class="form-check-input" id="td_' + field.id + '">'
                    + '<label class="form-check-label" for="td_' + field.id + '">' + escHtml(field.label) + '</label>'
                    + '</div></div>';
            case 'select':
                var opts = (field.options || '').split(/[\n,]/).map(function(o) { return o.trim(); }).filter(Boolean);
                var optHtml = opts.map(function(o) {
                    return '<option value="' + escHtml(o) + '">' + escHtml(o) + '</option>';
                }).join('');
                input = '<select name="' + name + '" class="form-select"' + req + '>'
                    + '<option value="">-- Pilih --</option>' + optHtml + '</select>';
                break;
            default:
                input = '<input type="text" name="' + name + '" class="form-control"' + phAtt + req + '>';
        }
        return '<div class="mb-3">' + label + input + '</div>';
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

    SigPad('sigCustCanvas', 'h_sig_cust', 'sigCustWrap', 'sigCustEmpty', 'sigCustStatus', 'sigCustClear');

    /* ══════════════════════════════
       LIVE PREVIEW SYNC
    ══════════════════════════════ */
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

        // Signature
        if ($('#h_sig_cust').val()) {
            $('#p_sigStatus').text('Perwakilan ✓');
            $('#p_sigSection').show();
        } else {
            $('#p_sigSection').hide();
        }
    }

    $('#f_task').on('change', function() {
        syncPreview();
        renderTemplate($(this).val());
    });
    $('#f_desc').on('input', syncPreview);

    // Init on load (task pre-selected)
    var initTaskId = $('#f_task').val();
    if (initTaskId) renderTemplate(initTaskId);
    syncPreview();

    /* ══════════════════════════════
       BACKGROUND SYNC (offline submit)
    ══════════════════════════════ */
    $('#laporanForm').on('submit', function(e) {
        if (navigator.onLine) return;

        e.preventDefault();
        var $btn = $(this).find('[type=submit]');
        $btn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i>Menyimpan offline…');

        var taskId    = $('#f_task').val();
        var desc    = $('#f_desc').val();
        var sigCust = $('#h_sig_cust').val() || '';
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

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
                signature_cust: sigCust,
                csrf_token:     csrfToken,
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
