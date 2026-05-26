@extends('layouts.customer')

@section('css')
<style>
.pw-wrap { max-width:480px; margin:3rem auto 0; }
.pw-card { background:var(--white); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; }
.pw-header { padding:14px 20px; border-bottom:1px solid var(--border-light); background:var(--bg); display:flex; align-items:center; gap:8px; font-size:.82rem; font-weight:700; color:var(--text); }
.pw-body { padding:1.5rem 1.25rem; }
@media (min-width: 640px) { .pw-body { padding:2rem 1.75rem; } }
.pw-field { margin-bottom:1.1rem; }
.pw-field label { display:block; font-size:.82rem; font-weight:600; color:var(--text); margin-bottom:5px; }
.pw-input-wrap { position:relative; }
.pw-input-wrap input { width:100%; padding:9px 38px 9px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:.88rem; color:var(--text); background:var(--card-bg); transition:border-color .15s; }
.pw-input-wrap input:focus { outline:none; border-color:var(--blue); }
.pw-eye { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-secondary); font-size:1rem; padding:0; line-height:1; }
</style>
@endsection

@section('content')

<div class="pwa-breadcrumb d-none d-sm-flex">
    <i class="ti ti-lock"></i>
    <span>Edit Password</span>
</div>

<div class="pw-wrap">
    <div class="pw-card">
        <div class="pw-header">
            <i class="ti ti-lock"></i> Edit Password
        </div>
        <div class="pw-body">

            @if(session('success'))
            <div class="alert alert-success py-2 mb-3" style="font-size:.85rem;">
                <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;">
                @foreach($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
            @endif

            <form action="{{ route('customer.profile.password.update') }}" method="POST">
                @csrf

                <div class="pw-field">
                    <label for="current_password">Password Saat Ini</label>
                    <div class="pw-input-wrap">
                        <input type="password" id="current_password" name="current_password"
                               autocomplete="current-password" required>
                        <button type="button" class="pw-eye" data-target="current_password">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="pw-field">
                    <label for="password">Password Baru</label>
                    <div class="pw-input-wrap">
                        <input type="password" id="password" name="password"
                               autocomplete="new-password" required minlength="8">
                        <button type="button" class="pw-eye" data-target="password">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="pw-field">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <div class="pw-input-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               autocomplete="new-password" required minlength="8">
                        <button type="button" class="pw-eye" data-target="password_confirmation">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-1">
                    <i class="ti ti-check me-1"></i>Simpan Password
                </button>
            </form>

        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(function () {
    $('.pw-eye').on('click', function () {
        var id  = $(this).data('target');
        var inp = document.getElementById(id);
        var ico = $(this).find('i');
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.removeClass('ti-eye').addClass('ti-eye-off');
        } else {
            inp.type = 'password';
            ico.removeClass('ti-eye-off').addClass('ti-eye');
        }
    });
});
</script>
@endsection
