@extends('errors.layout')

@section('title', '403 — Akses Dibatasi')
@section('code', '403')
@section('dot-color', 'bg-status-kurang_berkas-dot')
@section('status-badge', 'Kode Status: 403 • Otoritas Khusus Perangkat Desa')
@section('heading', 'Akses Dibatasi Khusus Aparatur')
@section('message', 'Halaman formulir dinas atau modul ini hanya diperuntukkan bagi Kepala Kampung atau Kasi Pelayanan yang memiliki akun terverifikasi.')

@section('icon')
<svg class="w-8 h-8 text-status-kurang_berkas-dot" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
</svg>
@endsection
