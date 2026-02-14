@extends('layouts.error')

@section('title', __('ui.error_429_title'))

@section('image')
    <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center mx-auto text-indigo-500 shadow-xl border-4 border-white relative z-10">
        <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
@endsection

@section('code', '429')
@section('message', __('ui.error_429_title'))
@section('description', __('ui.error_429_desc'))
