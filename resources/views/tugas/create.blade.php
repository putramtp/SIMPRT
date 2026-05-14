@extends('layouts.app')

@section('css')
<style>
.form-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
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
@media (min-width: 640px) {
    .form-card-body { padding: 1.5rem; }
}
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <a href="{{ route('tugas.index') }}">Tugas</a>
    <i class="ti ti-chevron-right"></i>
    <span>Buat Tugas</span>
</div>

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('tugas.index') }}" class="btn btn-sm btn-outline-secondary me-2">
        <i class="ti ti-arrow-left"></i>
    </a>
    <h4 class="mb-0">Buat Tugas Baru</h4>
</div>

<div class="sp-layout">

    {{-- ── Form (main) ── --}}
    <div class="sp-main">
        <div class="form-card">
            <div class="form-card-header">
                <i class="ti ti-clipboard-plus"></i> Detail Tugas
            </div>
            <div class="form-card-body">
                <form action="{{ route('tugas.store') }}" method="POST" id="tugasForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="f_title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="Masukkan judul tugas…"
                               required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="f_desc"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Jelaskan detail pekerjaan yang perlu dilakukan…">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" id="f_customer"
                                    class="form-select @error('customer_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}"
                                            {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Teknisi <span class="text-danger">*</span></label>
                            <select name="assigned_to" id="f_teknisi"
                                    class="form-select @error('assigned_to') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Teknisi --</option>
                                @foreach($teknisi as $t)
                                    <option value="{{ $t->id }}"
                                            {{ old('assigned_to') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="due_date" id="f_deadline"
                                   class="form-control"
                                   value="{{ old('due_date') }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Prioritas</label>
                            <select name="priority" id="f_priority" class="form-select">
                                <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high"   {{ old('priority') == 'high'   ? 'selected' : '' }}>Tinggi</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>Simpan Tugas
                        </button>
                        <a href="{{ route('tugas.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>{{-- /.sp-main --}}

    {{-- ── Live summary (aside) ── --}}
    <div class="sp-aside">
        <div class="summary-card">
            <div class="summary-card-header">
                <i class="ti ti-file-description"></i> Ringkasan Tugas
            </div>
            <div class="summary-card-body">
                <div class="summary-row">
                    <span class="summary-key">Judul</span>
                    <span class="summary-val placeholder" id="s_title">Belum diisi…</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row">
                    <span class="summary-key">Customer</span>
                    <span class="summary-val placeholder" id="s_customer">Belum dipilih</span>
                </div>
                <div class="summary-row">
                    <span class="summary-key">Teknisi</span>
                    <span class="summary-val placeholder" id="s_teknisi">Belum dipilih</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row">
                    <span class="summary-key">Deadline</span>
                    <span class="summary-val placeholder" id="s_deadline">Tidak ditentukan</span>
                </div>
                <div class="summary-row">
                    <span class="summary-key">Prioritas</span>
                    <span class="summary-val" id="s_priority">Normal</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row">
                    <span class="summary-key">Deskripsi</span>
                    <span class="summary-val placeholder" id="s_desc" style="font-size:.8rem;white-space:pre-line;max-height:120px;overflow-y:auto;">Belum diisi…</span>
                </div>
            </div>
        </div>
    </div>{{-- /.sp-aside --}}

</div>{{-- /.sp-layout --}}

@endsection

@section('js')
<script>
$(function () {
    var priorityLabel = { normal: 'Normal', high: 'Tinggi', urgent: '🔴 Urgent' };

    function update(id, val) {
        var $el = $(id);
        if (val && val.trim() !== '') {
            $el.text(val).removeClass('placeholder');
        } else {
            var defaults = {
                '#s_title': 'Belum diisi…',
                '#s_customer': 'Belum dipilih',
                '#s_teknisi': 'Belum dipilih',
                '#s_deadline': 'Tidak ditentukan',
                '#s_desc': 'Belum diisi…',
            };
            $el.text(defaults[id] || '—').addClass('placeholder');
        }
    }

    function syncAll() {
        update('#s_title',    $('#f_title').val());
        update('#s_customer', $('#f_customer option:selected').text() === '-- Pilih Customer --' ? '' : $('#f_customer option:selected').text());
        update('#s_teknisi',  $('#f_teknisi option:selected').text()  === '-- Pilih Teknisi --'  ? '' : $('#f_teknisi option:selected').text());
        update('#s_deadline', $('#f_deadline').val() ? new Date($('#f_deadline').val()).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}) : '');
        update('#s_desc',     $('#f_desc').val());
        $('#s_priority').text(priorityLabel[$('#f_priority').val()] || 'Normal');
    }

    $('#f_title, #f_desc, #f_deadline').on('input change', syncAll);
    $('#f_customer, #f_teknisi, #f_priority').on('change', syncAll);

    syncAll();
});
</script>
@endsection
