@extends('errors.layout')

@section('title', '404 — Halaman Tidak Ditemukan')
@section('code', '404')
@section('dot-color', 'bg-status-ditolak-dot')
@section('status-badge', 'Kode Status: 404 • Halaman atau Arsip Tidak Ditemukan')
@section('heading', 'Halaman Tidak Ditemukan')
@section('message', 'Alamat tautan yang Anda buka tidak ada, telah dipindahkan, atau telah diarsipkan oleh sistem administrasi persuratan digital SIRUKTI Kampung Rukti Harjo.')

@section('icon')
<svg class="w-8 h-8 text-status-ditolak-dot" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
</svg>
@endsection
