<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-secondary-200">
    @if (isset($header))
        <div class="px-6 py-4 border-b border-secondary-200">
            <h3 class="font-semibold text-lg text-secondary-800 leading-tight">
                {{ $header }}
            </h3>
        </div>
    @endif

    <div class="p-6 text-secondary-900">
        {{ $slot }}
    </div>
</div>