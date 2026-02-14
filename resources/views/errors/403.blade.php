@extends('layouts.error')

@section('title', __('ui.error_403_title'))

@section('image')
    <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center mx-auto text-warning-500 shadow-xl border-4 border-white relative z-10">
        <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
    </div>
@endsection

@section('code', '403')
@section('message', __('ui.error_403_title'))
@section('description', __('ui.error_403_desc'))
