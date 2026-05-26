@extends('layouts.app')

@section('css')
<style>
    body { background: var(--bg); }
    .card { border: 1px solid var(--border); border-radius: var(--radius-lg); }
    .form-label { font-size: .8rem; font-weight: 600; color: var(--text-secondary); }
    .form-control, .form-select { border-radius: var(--radius-md); border-color: var(--border); font-size: .9rem; }
    .form-control:focus, .form-select:focus { border-color: var(--blue); box-shadow: 0 0 0 .2rem var(--blue-light); }
    h4 { color: var(--text); font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('tugas.index') }}" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0">Edit Tugas</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tugas.update', $task) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Judul Tugas</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $task->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select" required>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $task->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teknisi <span class="text-danger">*</span></label>
                    @error('assignees')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                    @php $oldAssignees = old('assignees', $selectedTeknisi); @endphp
                    <div style="border:1px solid var(--bs-border-color);border-radius:.375rem;padding:.5rem .75rem;display:flex;flex-direction:column;gap:.35rem;">
                        @foreach($teknisi as $t)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="assignees[]" value="{{ $t->id }}"
                                   id="tek_{{ $t->id }}"
                                   {{ in_array($t->id, array_map('intval', $oldAssignees)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tek_{{ $t->id }}" style="font-size:.9rem;">
                                {{ $t->name }}
                                @if($t->active_tasks > 0)
                                <span class="text-muted" style="font-size:.78rem;">({{ $t->active_tasks }} tugas aktif)</span>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach(['pending','in_progress','completed'] as $s)
                            <option value="{{ $s }}" {{ old('status', $task->status) === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Prioritas</label>
                    <select name="priority" class="form-select">
                        @foreach(['low' => 'Rendah', 'normal' => 'Normal', 'high' => 'Tinggi', 'urgent' => 'Urgent'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('priority', $task->priority ?? 'normal') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Template Laporan</label>
                    <select name="template_id" class="form-select">
                        <option value="">-- Tanpa Template --</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}" {{ old('template_id', $task->template_id) == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </form>
        </div>
    </div>
</div>
@endsection
