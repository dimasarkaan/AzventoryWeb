@extends('layouts.error')

@section('title', __('ui.error_404_title'))

@section('image')
    <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center mx-auto text-primary-500 shadow-xl border-4 border-white relative z-10">
        <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
@endsection

@section('code', '404')
@section('message', __('ui.error_404_title'))
@section('description', __('ui.error_404_desc'))
