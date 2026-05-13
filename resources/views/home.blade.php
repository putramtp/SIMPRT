@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center py-5">
        <p class="text-muted">Mengalihkan ke dashboard…</p>
    </div>
</div>
@endsection

@section('js')
<script>
    // Redirect to the correct dashboard based on role
    @if(Auth::user()->hasRole('teknisi'))
        window.location.replace("{{ route('dashboard.teknisi') }}");
    @else
        window.location.replace("{{ route('dashboard.sales') }}");
    @endif
</script>
@endsection
