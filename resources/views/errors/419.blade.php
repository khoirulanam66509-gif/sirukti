@extends('errors.layout')

@section('title', '419 — Sesi Kedaluwarsa')
@section('code', '419')
@section('dot-color', 'bg-status-menunggu-dot')
@section('status-badge', 'Kode Status: 419 • Sesi Kedaluwarsa')
@section('heading', 'Sesi Halaman Berakhir')
@section('message', 'Sesi keamanan halaman telah berakhir karena tidak ada aktivitas dalam beberapa waktu. Silakan muat ulang halaman atau login kembali.')

@section('icon')
<svg class="w-8 h-8 text-status-menunggu-dot" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>
@endsection
