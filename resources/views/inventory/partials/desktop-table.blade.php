<div class="hidden md:block card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-modern w-full table-fixed">
            <thead>
                <tr>
                    @if(request('trash'))
                        <th class="w-[5%] px-4 py-3 text-center">
                            <input type="checkbox" id="select-all" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        </th>
                    @endif
                    <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[20%]">{{ __('ui.name') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.brand') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.category') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.condition') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[10%]">{{ __('ui.color') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.location') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[10%]">{{ __('ui.stock') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[14%]">{{ __('ui.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($spareparts as $sparepart)
                    <x-inventory.table-row :sparepart="$sparepart" :trash="request('trash')" />
                @empty
                    <tr>
                        <td colspan="{{ request('trash') ? '9' : '8' }}" class="px-6 py-12 text-center text-secondary-500">
                            <div class="flex flex-col items-center justify-center w-full">
                                @php
                                    $isFiltered = request('search') || request('category') || request('brand') || request('location') || request('color');
                                @endphp

                                <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4 shadow-sm border border-secondary-200">
                                    @if(request('trash'))
                                        {{-- Trash Icon --}}
                                        <x-icon.trash class="w-8 h-8 text-danger-400" />
                                    @elseif($isFiltered)
                                        {{-- Search/Filter Icon --}}
                                        <x-icon.search class="w-8 h-8 text-secondary-400" />
                                    @else
                                        {{-- Default Box Icon --}}
                                        <x-icon.box class="w-8 h-8 text-secondary-400" />
                                    @endif
                                </div>

                                <p class="text-lg font-bold text-secondary-900 tracking-tight">
                                    @if(request('trash'))
                                        {{ __('ui.trash_empty') }}
                                    @elseif($isFiltered)
                                        {{ __('ui.no_results') }}
                                    @else
                                        {{ __('ui.inventory_empty') }}
                                    @endif
                                </p>

                                <p class="text-sm mt-1 max-w-sm mx-auto leading-relaxed text-center text-secondary-500">
                                    @if(request('trash'))
                                        {{ __('ui.trash_empty_desc') }}
                                    @elseif($isFiltered)
                                        {{ __('ui.no_results_desc') }}
                                    @else
                                        {{ __('ui.inventory_empty_desc') }}
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            
            <!-- Skeleton Body (Hidden by default) -->
            <!-- High-Quality Skeleton Body -->
            <tbody id="skeleton-body" class="hidden divide-y divide-secondary-100 bg-white">
                @for ($i = 0; $i < 5; $i++)
                    <tr>
                        @if(request('trash'))
                            <td class="px-4 py-4 text-center">
                                <div class="h-4 w-4 bg-secondary-100 rounded animate-pulse mx-auto"></div>
                            </td>
                        @endif
                        <!-- Name & Image Column -->
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full bg-secondary-200 animate-pulse flex-shrink-0"></div> <!-- Status Dot -->
                                <div class="h-10 w-10 bg-secondary-100 rounded-lg animate-pulse flex-shrink-0"></div> <!-- Image -->
                                <div class="space-y-2 flex-1 min-w-0">
                                    <div class="h-4 w-32 bg-secondary-100 rounded animate-pulse"></div> <!-- Name -->
                                    <div class="h-3 w-20 bg-secondary-50 rounded animate-pulse"></div>  <!-- Part Number -->
                                </div>
                            </div>
                        </td>
                        <!-- Brand -->
                        <td class="px-4 py-4 text-center">
                            <div class="h-4 w-20 bg-secondary-50 rounded animate-pulse mx-auto"></div>
                        </td>
                        <!-- Category -->
                        <td class="px-4 py-4 text-center">
                            <div class="h-5 w-24 bg-secondary-100 rounded-full animate-pulse mx-auto"></div>
                        </td>
                        <!-- Color -->
                        <td class="px-4 py-4 text-center">
                                <div class="h-4 w-16 bg-secondary-50 rounded animate-pulse mx-auto"></div>
                        </td>
                        <!-- Location -->
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                    <div class="h-4 w-4 bg-secondary-100 rounded-full animate-pulse"></div>
                                    <div class="h-4 w-16 bg-secondary-50 rounded animate-pulse"></div>
                            </div>
                        </td>
                        <!-- Stock -->
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-baseline justify-center gap-1">
                                <div class="h-5 w-8 bg-secondary-100 rounded animate-pulse"></div>
                                <div class="h-3 w-6 bg-secondary-50 rounded animate-pulse"></div>
                            </div>
                        </td>
                        <!-- Actions -->
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                            </div>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($spareparts->hasPages())
        <div class="bg-secondary-50 px-4 py-3 border-t border-secondary-200 sm:px-6">
            {{ $spareparts->appends(request()->query())->links() }}
        </div>
    @endif
</div>
