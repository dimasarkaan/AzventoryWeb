@extends('layouts.error')

@section('title', 'Pembayaran Diperlukan')

@section('image')
    <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center mx-auto text-primary-500 shadow-xl border-4 border-white relative z-10">
        <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
    </div>
@endsection

@section('code', '402')
@section('message', 'Pembayaran Diperlukan')
@section('description', 'Maaf, halaman ini memerlukan pembayaran atau langganan aktif.')
