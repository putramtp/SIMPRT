@php
    $code    = '403';
    $icon    = 'ti-shield-off';
    $title   = 'Akses Ditolak';
    $message = $exception?->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini. Hubungi administrator jika ini adalah kesalahan.';
@endphp
@include('errors.layout')
