@extends('errors.layout')

@section('title', '500 — Gangguan Server')
@section('code', '500')
@section('dot-color', 'bg-status-diproses-dot')
@section('status-badge', 'Kode Status: 500 • Gangguan Server Sinkronisasi')
@section('heading', 'Gangguan Sinkronisasi Sistem')
@section('message', 'Peladen persuratan digital desa sedang melakukan pemrosesan otomatis atau kendala basis data. Mohon tunggu sejenak dan coba muat ulang halaman.')

@section('icon')
<svg class="w-8 h-8 text-status-diproses-dot" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
</svg>
@endsection
