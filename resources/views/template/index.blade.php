@extends('layouts.app')

@section('css')
<style>
/* ── Builder page overrides ── */
.builder-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: .75rem;
}
.builder-page-header h4 { margin: 0; }

/* ── Palette card ── */
.palette-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.palette-card-header {
    padding: 10px 14px;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-light);
    background: var(--bg);
}
.palette-list { padding: 10px; display: flex; flex-direction: column; gap: 6px; }
.palette-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: border-color .15s, background .15s;
    user-select: none;
}
.palette-item:hover { border-color: var(--blue); background: var(--blue-light); }
.palette-icon {
    width: 30px; height: 30px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.palette-item-info .palette-name  { font-size: .8rem; font-weight: 600; color: var(--text); }
.palette-item-info .palette-sub   { font-size: .68rem; color: var(--text-secondary); }

/* ── Canvas card ── */
.canvas-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.canvas-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    border-bottom: 1px solid var(--border-light);
    background: var(--bg);
    gap: .5rem;
    flex-wrap: wrap;
}
.canvas-toolbar-left { display: flex; align-items: center; gap: .5rem; }
.canvas-name-input {
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 5px 10px;
    font-size: .82rem;
    font-weight: 600;
    color: var(--text);
    background: var(--white);
    min-width: 180px;
}
.canvas-name-input:focus { outline: none; border-color: var(--blue); }
.canvas-body { padding: 14px; display: flex; flex-direction: column; gap: 10px; min-height: 340px; }
.canvas-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 240px;
    gap: .75rem;
    color: var(--text-secondary);
    text-align: center;
}
.canvas-empty i { font-size: 2.5rem; opacity: .2; }
.canvas-empty p { font-size: .82rem; margin: 0; }

/* ── Builder section block ── */
.b-section { background: var(--bg); border-radius: var(--radius-md); border: 1px solid var(--border-light); overflow: hidden; }
.b-section-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: var(--blue);
    color: #fff;
    font-size: .8rem;
    font-weight: 600;
}
.b-section-title-input {
    background: transparent;
    border: none;
    color: #fff;
    font-size: .8rem;
    font-weight: 600;
    flex: 1;
    padding: 0;
    outline: none;
}
.b-section-title-input::placeholder { color: rgba(255,255,255,.5); }
.b-section-actions { display: flex; gap: 6px; color: rgba(255,255,255,.75); font-size: 14px; margin-left: auto; }
.b-section-actions button { background: none; border: none; color: rgba(255,255,255,.8); cursor: pointer; padding: 2px; font-size: 14px; }
.b-section-actions button:hover { color: #fff; }
.b-fields { padding: 8px; display: flex; flex-direction: column; gap: 6px; }
.b-fields-empty {
    border: 1.5px dashed var(--border);
    border-radius: var(--radius-md);
    padding: 14px;
    text-align: center;
    font-size: .75rem;
    color: var(--text-secondary);
}

/* ── Builder field row ── */
.b-field {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 10px;
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: border-color .15s;
    position: relative;
}
.b-field:hover    { border-color: var(--blue-light); }
.b-field.selected { border-color: var(--blue); border-width: 1.5px; }
.b-field.selected::before {
    content: '';
    position: absolute;
    left: 0; top: 6px; bottom: 6px;
    width: 3px;
    background: var(--blue);
    border-radius: 0 2px 2px 0;
}
.b-field-drag { font-size: 16px; color: var(--border); cursor: grab; flex-shrink: 0; }
.b-field-info { flex: 1; min-width: 0; }
.b-field-name { font-size: .8rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.b-field-meta { display: flex; align-items: center; gap: 4px; margin-top: 2px; }
.b-field-type {
    font-size: .65rem;
    font-weight: 600;
    border-radius: 4px;
    padding: 1px 5px;
    background: var(--blue-light);
    color: #0C447C;
}
.b-field-req { font-size: .65rem; color: var(--red); }
.b-field-actions { display: flex; gap: 4px; flex-shrink: 0; }
.b-field-actions button {
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    font-size: 14px;
    padding: 2px 4px;
    border-radius: var(--radius-sm);
    transition: color .15s, background .15s;
}
.b-field-actions button:hover { background: var(--bg); color: var(--text); }
.b-field-actions .del-btn:hover { color: var(--red); background: #fee2e2; }

/* ── Add section button ── */
.add-section-btn {
    width: 100%;
    background: var(--white);
    border: 1.5px dashed var(--border);
    border-radius: var(--radius-md);
    padding: 10px;
    font-size: .8rem;
    color: var(--blue);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: border-color .15s, background .15s;
}
.add-section-btn:hover { border-color: var(--blue); background: var(--blue-light); }

/* ── Drag-over state ── */
.b-fields.drag-over { background: var(--blue-light); border-radius: var(--radius-md); }

/* ── Saved templates list ── */
.saved-tpl-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-top: .5rem;
}
.saved-tpl-list {
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 5px;
    max-height: 280px;
    overflow-y: auto;
}
.saved-tpl-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 10px;
    background: var(--bg);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    transition: border-color .15s;
}
.saved-tpl-item:hover { border-color: var(--blue); }
.saved-tpl-name { flex: 1; font-size: .8rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.saved-tpl-actions { display: flex; gap: 4px; flex-shrink: 0; }
.saved-tpl-actions button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    padding: 2px 5px;
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    transition: color .15s, background .15s;
}
.saved-tpl-actions .load-tpl-btn:hover { color: var(--blue); background: var(--blue-light); }
.saved-tpl-actions .del-tpl-btn:hover  { color: var(--red); background: #fee2e2; }
.saved-tpl-empty { font-size: .75rem; color: var(--text-secondary); text-align: center; padding: 10px 0; }

/* ── Mobile: palette on top, canvas below ── */
@media (max-width: 639px) {
    .palette-list { flex-direction: row; flex-wrap: wrap; }
    .palette-item  { flex: 0 0 calc(50% - 3px); }
}
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-template"></i>
    <span>Custom Template</span>
</div>

<div class="builder-page-header">
    <h4>Custom Template Builder</h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" id="clearBtn">
            <i class="ti ti-trash me-1"></i>Reset
        </button>
        <button class="btn btn-primary btn-sm" id="saveBtn">
            <i class="ti ti-device-floppy me-1"></i>Simpan Template
        </button>
    </div>
</div>

<div class="bl-layout">

    {{-- ── Left: Field palette ── --}}
    <div class="bl-palette">
        <div class="palette-card">
            <div class="palette-card-header"><i class="ti ti-layout-grid me-1"></i>Tipe Field</div>
            <div class="palette-list" id="fieldPalette">
                <div class="palette-item" data-type="text" data-icon="ti-text-size" data-label="Teks Pendek" data-sub="Input satu baris">
                    <div class="palette-icon" style="background:#E3F2FD;color:#1565C0;"><i class="ti ti-text-size"></i></div>
                    <div class="palette-item-info">
                        <div class="palette-name">Teks Pendek</div>
                        <div class="palette-sub">Input satu baris</div>
                    </div>
                </div>
                <div class="palette-item" data-type="textarea" data-icon="ti-text-wrap" data-label="Teks Panjang" data-sub="Area teks multi-baris">
                    <div class="palette-icon" style="background:#E8F5E9;color:#2E7D32;"><i class="ti ti-text-wrap"></i></div>
                    <div class="palette-item-info">
                        <div class="palette-name">Teks Panjang</div>
                        <div class="palette-sub">Area teks multi-baris</div>
                    </div>
                </div>
                <div class="palette-item" data-type="number" data-icon="ti-123" data-label="Angka" data-sub="Input numerik">
                    <div class="palette-icon" style="background:#FFF8E1;color:#F9A825;"><i class="ti ti-123"></i></div>
                    <div class="palette-item-info">
                        <div class="palette-name">Angka</div>
                        <div class="palette-sub">Input numerik</div>
                    </div>
                </div>
                <div class="palette-item" data-type="date" data-icon="ti-calendar" data-label="Tanggal" data-sub="Pemilih tanggal">
                    <div class="palette-icon" style="background:#FCE4EC;color:#C2185B;"><i class="ti ti-calendar"></i></div>
                    <div class="palette-item-info">
                        <div class="palette-name">Tanggal</div>
                        <div class="palette-sub">Pemilih tanggal</div>
                    </div>
                </div>
                <div class="palette-item" data-type="checkbox" data-icon="ti-checkbox" data-label="Centang" data-sub="Ya / Tidak">
                    <div class="palette-icon" style="background:#E8F5E9;color:#2E7D32;"><i class="ti ti-checkbox"></i></div>
                    <div class="palette-item-info">
                        <div class="palette-name">Centang</div>
                        <div class="palette-sub">Ya / Tidak</div>
                    </div>
                </div>
                <div class="palette-item" data-type="select" data-icon="ti-list" data-label="Pilihan" data-sub="Dropdown opsi">
                    <div class="palette-icon" style="background:#EDE7F6;color:#5E35B1;"><i class="ti ti-list"></i></div>
                    <div class="palette-item-info">
                        <div class="palette-name">Pilihan</div>
                        <div class="palette-sub">Dropdown opsi</div>
                    </div>
                </div>
                <div class="palette-item" data-type="photo" data-icon="ti-camera" data-label="Foto" data-sub="Upload gambar">
                    <div class="palette-icon" style="background:#FBE9E7;color:#E65100;"><i class="ti ti-camera"></i></div>
                    <div class="palette-item-info">
                        <div class="palette-name">Foto</div>
                        <div class="palette-sub">Upload gambar</div>
                    </div>
                </div>
                <div class="palette-item" data-type="signature" data-icon="ti-pencil" data-label="Tanda Tangan" data-sub="Kanvas tanda tangan">
                    <div class="palette-icon" style="background:#E3F2FD;color:#1565C0;"><i class="ti ti-pencil"></i></div>
                    <div class="palette-item-info">
                        <div class="palette-name">Tanda Tangan</div>
                        <div class="palette-sub">Kanvas tanda tangan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Saved templates list --}}
    <div class="saved-tpl-card">
        <div class="palette-card-header"><i class="ti ti-bookmark me-1"></i>Template Tersimpan</div>
        <div class="saved-tpl-list" id="savedTemplatesList">
            @forelse($templates as $t)
            <div class="saved-tpl-item" data-id="{{ $t->id }}">
                <span class="saved-tpl-name">{{ $t->name }}</span>
                <div class="saved-tpl-actions">
                    <button class="load-tpl-btn" data-id="{{ $t->id }}" title="Muat template"><i class="ti ti-folder-open"></i></button>
                    <button class="del-tpl-btn" data-id="{{ $t->id }}" title="Hapus template"><i class="ti ti-trash"></i></button>
                </div>
            </div>
            @empty
            <div class="saved-tpl-empty" id="noTemplatesMsg">Belum ada template tersimpan.</div>
            @endforelse
        </div>
    </div>
</div>{{-- /.bl-palette --}}

    {{-- ── Center: Canvas ── --}}
    <div class="bl-canvas">
        <div class="canvas-card">
            <div class="canvas-toolbar">
                <div class="canvas-toolbar-left">
                    <i class="ti ti-template" style="color:var(--blue);font-size:18px;"></i>
                    <input type="text" class="canvas-name-input" id="templateName"
                           placeholder="Nama Template…" value="">
                </div>
                <div style="font-size:.75rem;color:var(--text-secondary);" id="canvasInfo">0 field, 0 section</div>
            </div>
            <div class="canvas-body" id="canvasBody">
                <div class="canvas-empty" id="canvasEmpty">
                    <i class="ti ti-layout-board"></i>
                    <p>Klik field dari palette untuk menambahkan ke template</p>
                    <button class="btn btn-outline-primary btn-sm" id="addFirstSectionBtn">
                        <i class="ti ti-plus me-1"></i>Tambah Seksi
                    </button>
                </div>
            </div>
        </div>
    </div>{{-- /.bl-canvas --}}

    {{-- ── Right: Property panel (desktop only) ── --}}
    <div class="bl-props">
        <div class="prop-panel-card">
            <div class="prop-panel-header">
                <i class="ti ti-settings"></i> Properti Field
            </div>
            <div class="prop-panel-body" id="propPanelBody">
                <div class="prop-panel-empty" id="propPanelEmpty">
                    <i class="ti ti-cursor-text"></i>
                    <p>Pilih field di canvas untuk mengedit propertinya</p>
                </div>
                <div id="propPanelFields" style="display:none;">
                    <div class="prop-field">
                        <label class="prop-label">Label Field</label>
                        <input type="text" class="prop-input" id="prop_label" placeholder="Nama label…">
                    </div>
                    <div class="prop-field">
                        <label class="prop-label">Placeholder</label>
                        <input type="text" class="prop-input" id="prop_placeholder" placeholder="Teks petunjuk…">
                    </div>
                    <div class="prop-field">
                        <label class="prop-check-row">
                            <input type="checkbox" id="prop_required">
                            <span>Wajib diisi</span>
                        </label>
                    </div>
                    <div class="prop-field" id="prop_optionsGroup" style="display:none;">
                        <label class="prop-label">Opsi (satu per baris)</label>
                        <textarea class="prop-input" id="prop_options" rows="4"
                                  placeholder="Opsi 1&#10;Opsi 2&#10;Opsi 3"
                                  style="resize:vertical;"></textarea>
                    </div>
                    <div class="prop-field" style="margin-top:.5rem;">
                        <span class="prop-label">Tipe</span>
                        <span style="font-size:.8rem;font-weight:600;color:var(--blue);" id="prop_type_label">—</span>
                    </div>
                    <button class="btn btn-danger btn-sm w-100 mt-2" id="prop_deleteBtn">
                        <i class="ti ti-trash me-1"></i>Hapus Field
                    </button>
                </div>
            </div>
        </div>
    </div>{{-- /.bl-props --}}

</div>{{-- /.bl-layout --}}

@endsection

@section('js')
<script>
(function () {
'use strict';

/* ── State ── */
var sections = [];        // [{id, title, fields:[{id, type, label, placeholder, required, options}]}]
var selectedFieldId = null;
var selectedSectionId = null;
var sectionCounter = 0;
var fieldCounter   = 0;

var typeLabels = {
    text:      'Teks Pendek',
    textarea:  'Teks Panjang',
    number:    'Angka',
    date:      'Tanggal',
    checkbox:  'Centang',
    select:    'Pilihan',
    photo:     'Foto',
    signature: 'Tanda Tangan',
};

/* ── DOM refs ── */
var $canvasBody  = $('#canvasBody');
var $canvasEmpty = $('#canvasEmpty');
var $canvasInfo  = $('#canvasInfo');

/* ── Palette click → add to last section (or create first) ── */
$('#fieldPalette').on('click', '.palette-item', function () {
    var type  = $(this).data('type');
    var label = $(this).data('label');

    if (sections.length === 0) {
        addSection();
    }
    var section = sections[sections.length - 1];
    addField(section, type, label);
    render();
    selectField(section.id, section.fields[section.fields.length - 1].id);
});

/* ── Add first section button ── */
$(document).on('click', '#addFirstSectionBtn', function () {
    addSection();
    render();
});

/* ── Data helpers ── */
function addSection() {
    sectionCounter++;
    sections.push({
        id: 's' + sectionCounter,
        title: 'Seksi ' + sectionCounter,
        fields: []
    });
}

function addField(section, type, label) {
    fieldCounter++;
    section.fields.push({
        id: 'f' + fieldCounter,
        type: type,
        label: label,
        placeholder: '',
        required: false,
        options: '',
    });
}

function findField(fieldId) {
    for (var i = 0; i < sections.length; i++) {
        var s = sections[i];
        for (var j = 0; j < s.fields.length; j++) {
            if (s.fields[j].id === fieldId) return { section: s, field: s.fields[j], sIdx: i, fIdx: j };
        }
    }
    return null;
}

function deleteField(fieldId) {
    for (var i = 0; i < sections.length; i++) {
        var idx = sections[i].fields.findIndex(function(f){ return f.id === fieldId; });
        if (idx > -1) {
            sections[i].fields.splice(idx, 1);
            break;
        }
    }
    selectedFieldId = null;
    render();
    updatePropPanel(null);
}

function deleteSection(sectionId) {
    sections = sections.filter(function(s){ return s.id !== sectionId; });
    if (selectedSectionId === sectionId) { selectedFieldId = null; selectedSectionId = null; }
    render();
    updatePropPanel(null);
}

/* ── Render canvas ── */
function render() {
    var totalFields = 0;
    sections.forEach(function(s){ totalFields += s.fields.length; });

    if (sections.length === 0) {
        $canvasEmpty.show();
        $('.b-section').remove();
        $('hr.canvas-sep').remove();
        $canvasInfo.text('0 field, 0 section');
        return;
    }
    $canvasEmpty.hide();
    $canvasInfo.text(totalFields + ' field, ' + sections.length + ' section');

    // Remove and re-render sections
    $canvasBody.find('.b-section, .add-section-btn').remove();

    sections.forEach(function(section) {
        var sectionHtml = renderSection(section);
        $canvasBody.append(sectionHtml);
    });

    $canvasBody.append(
        '<button class="add-section-btn" id="addSectionBtn"><i class="ti ti-plus"></i> Tambah Seksi</button>'
    );

    // Re-attach selection state
    if (selectedFieldId) {
        $canvasBody.find('[data-field-id="' + selectedFieldId + '"]').addClass('selected');
    }

    // Section title edit
    $canvasBody.find('.b-section-title-input').on('input', function() {
        var sid = $(this).closest('.b-section').data('section-id');
        var s = sections.find(function(sec){ return sec.id === sid; });
        if (s) s.title = $(this).val();
    });

    // Add field via canvas add button
    $canvasBody.on('click', '.add-field-btn', function() {
        var sid = $(this).data('section-id');
        var s = sections.find(function(sec){ return sec.id === sid; });
        if (s) { addField(s, 'text', 'Field Baru'); render(); selectField(s.id, s.fields[s.fields.length - 1].id); }
    });

    // Delete section
    $canvasBody.on('click', '.del-section-btn', function(e) {
        e.stopPropagation();
        var sid = $(this).closest('.b-section').data('section-id');
        if (confirm('Hapus seksi ini?')) deleteSection(sid);
    });

    // Field click → select
    $canvasBody.on('click', '.b-field', function(e) {
        if ($(e.target).closest('button').length) return;
        var fid = $(this).data('field-id');
        var sid = $(this).closest('.b-section').data('section-id');
        selectField(sid, fid);
    });

    // Delete field button
    $canvasBody.on('click', '.del-field-btn', function(e) {
        e.stopPropagation();
        deleteField($(this).data('field-id'));
    });

    // Add section button
    $canvasBody.on('click', '#addSectionBtn', function() {
        addSection();
        render();
    });
}

function renderSection(section) {
    var fieldsHtml = section.fields.length === 0
        ? '<div class="b-fields-empty">Klik tipe field di palette untuk menambahkan ke seksi ini</div>'
        : section.fields.map(renderField).join('');

    return '<div class="b-section" data-section-id="' + section.id + '">'
        + '<div class="b-section-header">'
        + '<i class="ti ti-grip-vertical" style="opacity:.5;"></i>'
        + '<input class="b-section-title-input" type="text" value="' + escHtml(section.title) + '" placeholder="Nama Seksi…">'
        + '<div class="b-section-actions">'
        + '<button class="add-field-btn" data-section-id="' + section.id + '" title="Tambah field"><i class="ti ti-plus"></i></button>'
        + '<button class="del-section-btn" title="Hapus seksi"><i class="ti ti-trash"></i></button>'
        + '</div>'
        + '</div>'
        + '<div class="b-fields" data-section-id="' + section.id + '">'
        + fieldsHtml
        + '</div>'
        + '</div>';
}

function renderField(field) {
    var isSelected = field.id === selectedFieldId;
    return '<div class="b-field' + (isSelected ? ' selected' : '') + '" data-field-id="' + field.id + '">'
        + '<span class="b-field-drag"><i class="ti ti-grip-vertical"></i></span>'
        + '<div class="b-field-info">'
        + '<div class="b-field-name">' + escHtml(field.label) + '</div>'
        + '<div class="b-field-meta">'
        + '<span class="b-field-type">' + (typeLabels[field.type] || field.type) + '</span>'
        + (field.required ? '<span class="b-field-req">• Wajib</span>' : '')
        + '</div>'
        + '</div>'
        + '<div class="b-field-actions">'
        + '<button class="del-field-btn" data-field-id="' + field.id + '" title="Hapus field"><i class="ti ti-trash"></i></button>'
        + '</div>'
        + '</div>';
}

/* ── Field selection ── */
function selectField(sectionId, fieldId) {
    selectedSectionId = sectionId;
    selectedFieldId   = fieldId;
    $canvasBody.find('.b-field').removeClass('selected');
    $canvasBody.find('[data-field-id="' + fieldId + '"]').addClass('selected');

    var found = findField(fieldId);
    updatePropPanel(found ? found.field : null);
}

function updatePropPanel(field) {
    if (!field) {
        $('#propPanelEmpty').show();
        $('#propPanelFields').hide();
        return;
    }
    $('#propPanelEmpty').hide();
    $('#propPanelFields').show();

    $('#prop_label').val(field.label);
    $('#prop_placeholder').val(field.placeholder);
    $('#prop_required').prop('checked', field.required);
    $('#prop_type_label').text(typeLabels[field.type] || field.type);
    $('#prop_options').val(field.options || '');

    // Show options textarea only for select type
    $('#prop_optionsGroup').toggle(field.type === 'select');
}

/* ── Property panel → sync to state ── */
$('#prop_label').on('input', function() {
    if (!selectedFieldId) return;
    var found = findField(selectedFieldId);
    if (found) { found.field.label = $(this).val(); }
    $canvasBody.find('[data-field-id="' + selectedFieldId + '"] .b-field-name').text($(this).val());
});

$('#prop_placeholder').on('input', function() {
    if (!selectedFieldId) return;
    var found = findField(selectedFieldId);
    if (found) found.field.placeholder = $(this).val();
});

$('#prop_required').on('change', function() {
    if (!selectedFieldId) return;
    var found = findField(selectedFieldId);
    if (found) { found.field.required = $(this).is(':checked'); render(); selectField(selectedSectionId, selectedFieldId); }
});

$('#prop_options').on('input', function() {
    if (!selectedFieldId) return;
    var found = findField(selectedFieldId);
    if (found) found.field.options = $(this).val();
});

$('#prop_deleteBtn').on('click', function() {
    if (selectedFieldId) deleteField(selectedFieldId);
});

/* ── Save / Reset ── */
$('#saveBtn').on('click', function() {
    var name = $('#templateName').val().trim();
    if (!name) { alert('Masukkan nama template terlebih dahulu.'); $('#templateName').focus(); return; }
    if (sections.length === 0) { alert('Tambahkan minimal satu seksi dan field.'); return; }

    var $btn = $(this);
    $btn.html('<i class="ti ti-loader me-1"></i>Menyimpan…').prop('disabled', true);

    $.ajax({
        url: '{{ route("template.store") }}',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        contentType: 'application/json',
        data: JSON.stringify({ name: name, fields: sections }),
        success: function(resp) {
            addSavedTemplateToList(resp.id, resp.name);
            $btn.html('<i class="ti ti-check me-1"></i>Tersimpan!').removeClass('btn-primary').addClass('btn-success');
            setTimeout(function() {
                $btn.html('<i class="ti ti-device-floppy me-1"></i>Simpan Template').removeClass('btn-success').addClass('btn-primary').prop('disabled', false);
            }, 2000);
        },
        error: function() {
            alert('Gagal menyimpan template.');
            $btn.html('<i class="ti ti-device-floppy me-1"></i>Simpan Template').prop('disabled', false);
        }
    });
});

/* ── Template list: load ── */
$(document).on('click', '.load-tpl-btn', function() {
    if (sections.length > 0 && !confirm('Muat template ini? Canvas saat ini akan dihapus.')) return;
    loadTemplate($(this).data('id'));
});

/* ── Template list: delete ── */
$(document).on('click', '.del-tpl-btn', function() {
    var id = $(this).data('id');
    if (!confirm('Hapus template ini?')) return;
    $.ajax({
        url: '{{ url("/template") }}/' + id,
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function() {
            $('[data-id="' + id + '"].saved-tpl-item').remove();
            if ($('#savedTemplatesList .saved-tpl-item').length === 0) {
                $('#savedTemplatesList').html('<div class="saved-tpl-empty" id="noTemplatesMsg">Belum ada template tersimpan.</div>');
            }
        }
    });
});

function loadTemplate(id) {
    $.get('{{ url("/template") }}/' + id, function(data) {
        if (!data || !data.fields) return;
        sections = data.fields;
        var maxS = 0, maxF = 0;
        sections.forEach(function(s) {
            var sn = parseInt((s.id || '').replace('s', '')) || 0;
            maxS = Math.max(maxS, sn);
            (s.fields || []).forEach(function(f) {
                var fn = parseInt((f.id || '').replace('f', '')) || 0;
                maxF = Math.max(maxF, fn);
            });
        });
        sectionCounter = maxS;
        fieldCounter   = maxF;
        selectedFieldId = null;
        selectedSectionId = null;
        $('#templateName').val(data.name);
        render();
        updatePropPanel(null);
    });
}

function addSavedTemplateToList(id, name) {
    $('#noTemplatesMsg').remove();
    var html = '<div class="saved-tpl-item" data-id="' + id + '">'
        + '<span class="saved-tpl-name">' + escHtml(name) + '</span>'
        + '<div class="saved-tpl-actions">'
        + '<button class="load-tpl-btn" data-id="' + id + '" title="Muat template"><i class="ti ti-folder-open"></i></button>'
        + '<button class="del-tpl-btn" data-id="' + id + '" title="Hapus template"><i class="ti ti-trash"></i></button>'
        + '</div>'
        + '</div>';
    $('#savedTemplatesList').prepend(html);
}

$('#clearBtn').on('click', function() {
    if (sections.length === 0) return;
    if (!confirm('Reset canvas? Semua field akan dihapus.')) return;
    sections = [];
    selectedFieldId = null;
    selectedSectionId = null;
    render();
    updatePropPanel(null);
    $('#templateName').val('');
});

/* ── Utility ── */
function escHtml(str) {
    return (str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Initial render
render();

})();
</script>
@endsection
