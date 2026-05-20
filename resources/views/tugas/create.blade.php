@extends('layouts.app')

@section('css')
<style>
/* ── Form Buat Tugas PWA — scoped ─────────────────────────────── */

/* Hero app-bar */
.ftg-hero {
    background: var(--blue);
    margin: -1rem -1rem 0;
    padding: 14px 1rem 18px;
    border-radius: 0 0 20px 20px;
    display: flex; align-items: center; gap: 12px;
}
@media (min-width: 640px) {
    .ftg-hero { margin: -.75rem -1.5rem 0; padding: 14px 1.5rem 18px; }
}
@media (min-width: 1024px) {
    .ftg-hero { margin: -.75rem -2rem 0; padding: 14px 2rem 18px; }
}
.ftg-hero-back {
    width: 32px; height: 32px; border-radius: 50%;
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    color: #fff; text-decoration: none; flex-shrink: 0;
}
.ftg-hero-title { font-size: 16px; font-weight: 600; color: #fff; line-height: 1.2; }
.ftg-hero-sub   { font-size: 11px; color: #90CAF9; }

/* Step indicator */
.ftg-steps {
    display: flex; align-items: center;
    padding: 16px 0 0; margin-bottom: 16px;
}
.ftg-step {
    display: flex; flex-direction: column; align-items: center; gap: 4px;
}
.ftg-step-circle {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 600; transition: all .2s;
}
.ftg-step-circle.done   { background: var(--blue); color: #fff; }
.ftg-step-circle.active { background: var(--blue); color: #fff; }
.ftg-step-circle.todo   { background: var(--white); border: 1.5px solid var(--border); color: var(--text-secondary); }
.ftg-step-label { font-size: 10px; font-weight: 500; transition: color .2s; }
.ftg-step-label.active { color: var(--blue); }
.ftg-step-label.todo   { color: var(--text-secondary); }
.ftg-step-line {
    flex: 1; height: 2px; margin-bottom: 14px; transition: background .2s;
}
.ftg-step-line.done   { background: var(--blue); }
.ftg-step-line.todo   { background: var(--border-light); }

/* Section card */
.ftg-card {
    background: var(--white); border: 1px solid var(--border-light);
    border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 12px;
}
.ftg-card-header {
    background: var(--blue-light); padding: 9px 14px;
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 600; color: #0C447C;
}
.ftg-card-header i { font-size: 15px; color: var(--blue); }
.ftg-card-body { padding: 14px; display: flex; flex-direction: column; gap: 12px; }

/* Field label */
.ftg-label {
    font-size: 12px; font-weight: 600; color: var(--text-secondary);
    margin-bottom: 4px; display: block;
}
.ftg-label .req { color: var(--red); }

/* Priority toggle */
.ftg-priority-group { display: flex; gap: 6px; }
.ftg-priority-btn {
    flex: 1; padding: 8px 4px; border-radius: var(--radius-md);
    font-size: 11px; font-weight: 500; text-align: center;
    border: 1px solid var(--border); background: var(--bg);
    color: var(--text-secondary); cursor: pointer; transition: all .15s;
    user-select: none;
}
.ftg-priority-btn.sel-low    { background: var(--green-light); border-color: var(--green); color: var(--green); }
.ftg-priority-btn.sel-normal { background: var(--blue);        border-color: var(--blue);  color: #fff; }
.ftg-priority-btn.sel-high   { background: var(--yellow-light);border-color: var(--yellow);color: #633806; }
.ftg-priority-btn.sel-urgent { background: #fef2f2;            border-color: var(--red);   color: var(--red); }

/* Teknisi cards */
.ftg-tek-card {
    display: flex; align-items: center; gap: 10px;
    border: 1px solid var(--border-light); border-radius: var(--radius-md);
    padding: 10px 12px; cursor: pointer; transition: all .15s;
}
.ftg-tek-card.selected {
    border-color: var(--blue); border-width: 1.5px;
    background: var(--blue-light);
}
.ftg-tek-card.busy { opacity: .55; cursor: not-allowed; }
.ftg-tek-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: var(--blue-light);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: var(--blue); flex-shrink: 0;
}
.ftg-tek-card.selected .ftg-tek-avatar { background: var(--blue); color: #fff; }
.ftg-tek-name { font-size: 13px; font-weight: 600; color: var(--text); }
.ftg-tek-sub  { font-size: 11px; color: var(--text-secondary); margin-top: 1px; }
.ftg-tek-badges { display: flex; gap: 5px; margin-top: 4px; flex-wrap: wrap; }
.ftg-tek-avail {
    font-size: 10px; border-radius: 20px; padding: 1px 8px;
    background: var(--green-light); color: var(--green); font-weight: 500;
}
.ftg-tek-avail.busy { background: #fef2f2; color: var(--red); }
.ftg-tek-count { font-size: 10px; color: var(--text-secondary); }
.ftg-tek-check {
    width: 20px; height: 20px; border-radius: 50%; flex-shrink: 0;
    border: 1.5px solid var(--border); margin-left: auto;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
}
.ftg-tek-card.selected .ftg-tek-check {
    background: var(--blue); border-color: var(--blue);
}
.ftg-tek-card.selected .ftg-tek-check i { display: block; }
.ftg-tek-check i { display: none; font-size: 11px; color: #fff; }

/* CTA row */
.ftg-cta { display: flex; gap: 10px; margin-top: 4px; }
.ftg-btn-secondary {
    flex: 1; background: var(--white); border: 1px solid var(--border);
    border-radius: var(--radius-md); padding: 11px;
    font-size: 13px; color: var(--text-secondary); cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.ftg-btn-primary {
    flex: 2; background: var(--blue); border: none;
    border-radius: var(--radius-md); padding: 11px;
    font-size: 13px; color: #fff; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.ftg-btn-primary:disabled { opacity: .55; cursor: not-allowed; }

/* Step sections */
.ftg-section { display: none; }
.ftg-section.active { display: block; }

/* Validation error */
.ftg-error { font-size: 12px; color: var(--red); margin-top: 4px; display: none; }
</style>
@endsection

@section('content')

{{-- Hero --}}
<div class="ftg-hero">
    <a href="{{ route('tugas.index') }}" class="ftg-hero-back">
        <i class="ti ti-arrow-left" style="font-size:18px;"></i>
    </a>
    <div>
        <div class="ftg-hero-title">Buat Tugas Baru</div>
        <div class="ftg-hero-sub">Isi detail pekerjaan di bawah ini</div>
    </div>
</div>

{{-- Step indicator --}}
<div class="ftg-steps" id="stepIndicator">
    <div class="ftg-step">
        <div class="ftg-step-circle active" id="sc1"><i class="ti ti-check" style="font-size:13px;display:none;" id="sc1-check"></i><span id="sc1-num">1</span></div>
        <span class="ftg-step-label active" id="sl1">Detail</span>
    </div>
    <div class="ftg-step-line todo" id="line12"></div>
    <div class="ftg-step">
        <div class="ftg-step-circle todo" id="sc2"><span id="sc2-num">2</span></div>
        <span class="ftg-step-label todo" id="sl2">Teknisi</span>
    </div>
    <div class="ftg-step-line todo" id="line23"></div>
    <div class="ftg-step">
        <div class="ftg-step-circle todo" id="sc3"><span id="sc3-num">3</span></div>
        <span class="ftg-step-label todo" id="sl3">Template</span>
    </div>
</div>

<form action="{{ route('tugas.store') }}" method="POST" id="tugasForm">
@csrf
<input type="hidden" name="priority"    id="f_priority"    value="{{ old('priority', 'normal') }}">
<input type="hidden" name="assigned_to" id="f_assigned_to" value="{{ old('assigned_to') }}">

{{-- ── STEP 1: Detail Pekerjaan ── --}}
<div class="ftg-section active" id="step1">
    <div class="ftg-card">
        <div class="ftg-card-header">
            <i class="ti ti-file-text"></i> Detail Pekerjaan
        </div>
        <div class="ftg-card-body">

            <div>
                <label class="ftg-label">Nama Pekerjaan <span class="req">*</span></label>
                <input type="text" name="title" id="f_title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}"
                       placeholder="Contoh: Instalasi CCTV 8 Titik">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="ftg-error" id="err_title">Judul wajib diisi.</div>
            </div>

            <div>
                <label class="ftg-label">Customer / Klien <span class="req">*</span></label>
                <select name="customer_id" id="f_customer"
                        class="form-select @error('customer_id') is-invalid @enderror">
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}"
                                data-address="{{ $c->address }}"
                                {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
                @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="ftg-error" id="err_customer">Customer wajib dipilih.</div>
            </div>

            {{-- Location display from customer --}}
            <div id="locationRow" style="display:none;">
                <label class="ftg-label">Lokasi</label>
                <div class="form-control d-flex align-items-center justify-content-between"
                     style="background:var(--bg);cursor:default;">
                    <span id="locationText" style="font-size:13px;color:var(--text);"></span>
                    <i class="ti ti-map-pin" style="color:var(--blue);font-size:15px;"></i>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-12 col-sm-6">
                    <label class="ftg-label">Deadline</label>
                    <input type="date" name="due_date" id="f_due_date"
                           class="form-control" value="{{ old('due_date') }}">
                </div>
            </div>

            <div>
                <label class="ftg-label">Prioritas</label>
                <div class="ftg-priority-group" id="priorityGroup">
                    <div class="ftg-priority-btn" data-val="low">Rendah</div>
                    <div class="ftg-priority-btn sel-normal" data-val="normal">Normal</div>
                    <div class="ftg-priority-btn" data-val="high">Tinggi</div>
                    <div class="ftg-priority-btn" data-val="urgent">Urgent</div>
                </div>
            </div>

            <div>
                <label class="ftg-label">Deskripsi Pekerjaan</label>
                <textarea name="description" id="f_desc" class="form-control" rows="3"
                          placeholder="Jelaskan detail pekerjaan yang perlu dilakukan…">{{ old('description') }}</textarea>
            </div>

        </div>
    </div>

    <div class="ftg-cta">
        <a href="{{ route('tugas.index') }}" class="ftg-btn-secondary">Batal</a>
        <button type="button" class="ftg-btn-primary" onclick="goStep(2)">
            Pilih Teknisi <i class="ti ti-arrow-right"></i>
        </button>
    </div>
</div>

{{-- ── STEP 2: Pilih Teknisi ── --}}
<div class="ftg-section" id="step2">
    <div class="ftg-card">
        <div class="ftg-card-header">
            <i class="ti ti-users"></i> Pilih Teknisi <span class="req ms-1" style="font-size:11px;">*</span>
        </div>
        <div class="ftg-card-body" id="teknisiList">
            @forelse($teknisi as $t)
            @php
                $initials = strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', trim($t->name)))));
                $initials = mb_substr($initials, 0, 2);
                $busy = $t->active_tasks >= 7;
            @endphp
            <div class="ftg-tek-card {{ $busy ? 'busy' : '' }} {{ old('assigned_to') == $t->id ? 'selected' : '' }}"
                 data-id="{{ $t->id }}"
                 onclick="{{ $busy ? '' : 'selectTeknisi(this)' }}">
                <div class="ftg-tek-avatar">{{ $initials }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="ftg-tek-name">{{ $t->name }}</div>
                    <div class="ftg-tek-badges">
                        <span class="ftg-tek-avail {{ $busy ? 'busy' : '' }}">
                            {{ $busy ? 'Sibuk ('.$t->active_tasks.' tugas)' : 'Tersedia' }}
                        </span>
                        @if($t->active_tasks > 0 && !$busy)
                        <span class="ftg-tek-count">{{ $t->active_tasks }} tugas aktif</span>
                        @endif
                    </div>
                </div>
                <div class="ftg-tek-check">
                    <i class="ti ti-check"></i>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:2rem;color:var(--text-secondary);font-size:13px;">
                <i class="ti ti-user-off" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.25;"></i>
                Belum ada teknisi terdaftar.
            </div>
            @endforelse
        </div>
        <div class="ftg-error ms-3 mb-3" id="err_teknisi" style="display:none;">
            Pilih salah satu teknisi untuk melanjutkan.
        </div>
    </div>

    <div class="ftg-cta">
        <button type="button" class="ftg-btn-secondary" onclick="goStep(1)">
            <i class="ti ti-arrow-left"></i> Kembali
        </button>
        <button type="button" class="ftg-btn-primary" onclick="goStep(3)">
            Pilih Template <i class="ti ti-arrow-right"></i>
        </button>
    </div>
</div>

{{-- ── STEP 3: Template & Submit ── --}}
<div class="ftg-section" id="step3">

    <div class="ftg-card">
        <div class="ftg-card-header">
            <i class="ti ti-template"></i> Template Laporan
        </div>
        <div class="ftg-card-body">
            <div>
                <label class="ftg-label">Template yang digunakan</label>
                <select name="template_id" id="f_template" class="form-select">
                    <option value="">-- Tanpa Template (form bebas) --</option>
                    @foreach($templates as $tpl)
                        <option value="{{ $tpl->id }}"
                                data-fields="{{ $tpl->fields ? count($tpl->fields) : 0 }}"
                                {{ old('template_id') == $tpl->id ? 'selected' : '' }}>
                            {{ $tpl->name }}
                        </option>
                    @endforeach
                </select>
                <div style="font-size:11px;color:var(--text-secondary);margin-top:5px;">
                    Template menentukan field yang wajib diisi teknisi saat membuat laporan.
                </div>
            </div>

            {{-- Template preview --}}
            <div id="tplPreview" style="display:none;">
                <label class="ftg-label">Field dalam template</label>
                <div id="tplFieldList" style="display:flex;flex-direction:column;gap:6px;"></div>
            </div>

            @if($templates->isEmpty())
            <div style="background:var(--yellow-light);border-radius:var(--radius-md);padding:10px 12px;font-size:12px;color:#633806;display:flex;align-items:flex-start;gap:8px;">
                <i class="ti ti-info-circle" style="font-size:15px;flex-shrink:0;margin-top:1px;"></i>
                <span>Belum ada template. Buat template di menu <a href="{{ route('template.index') }}" style="color:#633806;font-weight:600;">Custom Template</a> terlebih dahulu, atau lanjutkan tanpa template.</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Ringkasan sebelum submit --}}
    <div class="ftg-card">
        <div class="ftg-card-header">
            <i class="ti ti-file-description"></i> Ringkasan Tugas
        </div>
        <div class="ftg-card-body" style="gap:8px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--text-secondary);">Judul</span>
                <span style="font-size:13px;font-weight:600;color:var(--text);text-align:right;max-width:60%;" id="sum_title">—</span>
            </div>
            <div style="border-top:1px solid var(--border-light);padding-top:8px;display:flex;justify-content:space-between;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--text-secondary);">Customer</span>
                <span style="font-size:13px;color:var(--text);text-align:right;" id="sum_customer">—</span>
            </div>
            <div style="border-top:1px solid var(--border-light);padding-top:8px;display:flex;justify-content:space-between;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--text-secondary);">Teknisi</span>
                <span style="font-size:13px;color:var(--text);text-align:right;" id="sum_teknisi">—</span>
            </div>
            <div style="border-top:1px solid var(--border-light);padding-top:8px;display:flex;justify-content:space-between;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--text-secondary);">Deadline</span>
                <span style="font-size:13px;color:var(--text);" id="sum_deadline">—</span>
            </div>
            <div style="border-top:1px solid var(--border-light);padding-top:8px;display:flex;justify-content:space-between;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--text-secondary);">Prioritas</span>
                <span style="font-size:13px;" id="sum_priority">—</span>
            </div>
        </div>
    </div>

    <div class="ftg-cta">
        <button type="button" class="ftg-btn-secondary" onclick="goStep(2)">
            <i class="ti ti-arrow-left"></i> Kembali
        </button>
        <button type="submit" class="ftg-btn-primary" id="submitBtn">
            <i class="ti ti-send"></i> Kirim Tugas
        </button>
    </div>
</div>

</form>

<div style="height:.5rem;"></div>
@endsection

@section('js')
<script>
(function () {
    var currentStep = 1;

    /* ── Priority toggle ── */
    var priorityColors = {
        low:    'sel-low',
        normal: 'sel-normal',
        high:   'sel-high',
        urgent: 'sel-urgent',
    };
    var priorityLabels = { low: 'Rendah', normal: 'Normal', high: 'Tinggi', urgent: '🔴 Urgent' };

    $('#priorityGroup .ftg-priority-btn').on('click', function () {
        var val = $(this).data('val');
        $('#priorityGroup .ftg-priority-btn')
            .removeClass('sel-low sel-normal sel-high sel-urgent');
        $(this).addClass(priorityColors[val]);
        $('#f_priority').val(val);
    });

    // Set initial priority from old() value
    var initPriority = $('#f_priority').val() || 'normal';
    $('#priorityGroup .ftg-priority-btn').removeClass('sel-low sel-normal sel-high sel-urgent');
    $('#priorityGroup [data-val="' + initPriority + '"]').addClass(priorityColors[initPriority]);

    /* ── Customer → location ── */
    $('#f_customer').on('change', function () {
        var addr = $('option:selected', this).data('address') || '';
        if (addr) {
            $('#locationText').text(addr);
            $('#locationRow').show();
        } else {
            $('#locationRow').hide();
        }
    }).trigger('change');

    /* ── Teknisi card selection ── */
    window.selectTeknisi = function (el) {
        $('.ftg-tek-card').removeClass('selected');
        $(el).addClass('selected');
        $('#f_assigned_to').val($(el).data('id'));
        $('#err_teknisi').hide();
    };

    // Pre-select on old()
    var oldTeknisi = '{{ old("assigned_to") }}';
    if (oldTeknisi) {
        $('.ftg-tek-card[data-id="' + oldTeknisi + '"]').addClass('selected');
    }

    /* ── Template preview ── */
    var templateData = @json($templates->keyBy('id')->map(fn($t) => $t->fields));

    $('#f_template').on('change', function () {
        var id = $(this).val();
        if (id && templateData[id] && templateData[id].length) {
            var sections = templateData[id];
            var html = '';
            sections.forEach(function (section) {
                if (!section.fields || !section.fields.length) return;
                html += '<div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;'
                    + 'color:var(--text-secondary);padding:6px 2px 3px;">'
                    + $('<span>').text(section.title || 'Seksi').html() + '</div>';
                section.fields.forEach(function (field) {
                    html += '<div style="display:flex;align-items:center;gap:8px;padding:7px 10px;'
                        + 'background:var(--bg);border-radius:var(--radius-md);">'
                        + '<i class="ti ti-point-filled" style="font-size:10px;color:var(--blue);"></i>'
                        + '<span style="font-size:12px;color:var(--text);">'
                        + $('<span>').text(field.label || field.type).html()
                        + '</span></div>';
                });
            });
            $('#tplFieldList').html(html);
            $('#tplPreview').show();
        } else {
            $('#tplPreview').hide();
        }
    });

    /* ── Step navigation ── */
    window.goStep = function (target) {
        // Validate before advancing
        if (target > currentStep) {
            if (currentStep === 1) {
                var ok = true;
                if (!$('#f_title').val().trim()) {
                    $('#err_title').show(); $('#f_title').addClass('is-invalid'); ok = false;
                } else {
                    $('#err_title').hide(); $('#f_title').removeClass('is-invalid');
                }
                if (!$('#f_customer').val()) {
                    $('#err_customer').show(); $('#f_customer').addClass('is-invalid'); ok = false;
                } else {
                    $('#err_customer').hide(); $('#f_customer').removeClass('is-invalid');
                }
                if (!ok) return;
            }
            if (currentStep === 2) {
                if (!$('#f_assigned_to').val()) {
                    $('#err_teknisi').show(); return;
                }
                $('#err_teknisi').hide();
                refreshSummary();
            }
        }

        // Update UI
        $('#step' + currentStep).removeClass('active');
        $('#step' + target).addClass('active');
        currentStep = target;
        updateStepIndicator();

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    function updateStepIndicator() {
        for (var i = 1; i <= 3; i++) {
            var $circle = $('#sc' + i);
            var $label  = $('#sl' + i);
            $circle.removeClass('done active todo');
            $label.removeClass('active todo');

            if (i < currentStep) {
                $circle.addClass('done');
                $('#sc' + i + '-check').show();
                $('#sc' + i + '-num').hide();
                $label.addClass('active');
            } else if (i === currentStep) {
                $circle.addClass('active');
                $('#sc' + i + '-check').hide();
                $('#sc' + i + '-num').show();
                $label.addClass('active');
            } else {
                $circle.addClass('todo');
                $('#sc' + i + '-check').hide();
                $('#sc' + i + '-num').show();
                $label.addClass('todo');
            }
        }
        // Lines
        $('#line12').removeClass('done todo').addClass(currentStep > 1 ? 'done' : 'todo');
        $('#line23').removeClass('done todo').addClass(currentStep > 2 ? 'done' : 'todo');
    }

    function refreshSummary() {
        $('#sum_title').text($('#f_title').val() || '—');
        var custTxt = $('#f_customer option:selected').text();
        $('#sum_customer').text(custTxt === '-- Pilih Customer --' ? '—' : custTxt);
        var tekCard = $('.ftg-tek-card.selected');
        $('#sum_teknisi').text(tekCard.length ? tekCard.find('.ftg-tek-name').text() : '—');
        var due = $('#f_due_date').val();
        $('#sum_deadline').text(due ? new Date(due).toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' }) : 'Tidak ditentukan');
        var prio = $('#f_priority').val();
        var prioColors = { low: 'var(--green)', normal: 'var(--blue)', high: 'var(--yellow)', urgent: 'var(--red)' };
        $('#sum_priority').text(priorityLabels[prio] || 'Normal').css('color', prioColors[prio] || 'var(--blue)');
    }

    // If returning from validation error, go to correct step
    @if($errors->has('title') || $errors->has('customer_id') || $errors->has('description') || $errors->has('due_date'))
        goStep(1);
    @elseif($errors->has('assigned_to'))
        goStep(2);
    @elseif($errors->has('template_id'))
        goStep(3);
    @endif

})();
</script>
@endsection
