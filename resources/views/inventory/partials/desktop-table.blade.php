<div id="inventory-desktop-container" class="hidden md:block card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-modern w-full table-fixed">
            <thead>
                <tr>
                    @if(auth()->user()->role === \App\Enums\UserRole::SUPERADMIN || (!request('trash') && auth()->user()->role === \App\Enums\UserRole::ADMIN))
                        <th class="w-[5%] px-4 py-3 text-center">
                            <input type="checkbox" id="select-all" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        </th>
                    @endif
                    @if(request('filter') == 'problematic')
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[25%]">{{ __('ui.name') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.condition') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[39%]">Kronologi / Catatan</th>
                    @else
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[20%]">{{ __('ui.name') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.brand') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.category') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.condition') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[10%]">{{ __('ui.color') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.location') }}</th>
                    @endif
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[10%]">{{ __('ui.stock') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.actions') }}</th>
                </tr>
            </thead>
            <tbody id="inventory-desktop-body">
                @forelse ($spareparts as $sparepart)
                    <x-inventory.table-row :sparepart="$sparepart" :trash="request('trash')" />
                @empty
                    <tr>
                        <td colspan="{{ (auth()->user()->role === \App\Enums\UserRole::SUPERADMIN || (!request('trash') && auth()->user()->role === \App\Enums\UserRole::ADMIN)) ? (request('filter') == 'problematic' ? '6' : '9') : (request('filter') == 'problematic' ? '5' : '8') }}" class="py-24" style="text-align:center;">
                            @php
                                $isFiltered = request('search') || request('category') || request('brand') || request('location') || request('color') || request('type') || request('condition');
                                $bgCircle = request('trash') ? 'bg-danger-50 text-danger-500' : ($isFiltered ? 'bg-primary-50 text-primary-500' : 'bg-secondary-50 text-secondary-400');
                            @endphp
                            <div style="display:inline-flex; flex-direction:column; align-items:center; max-width:360px; width:100%;">
                                <div class="w-24 h-24 {{ $bgCircle }} rounded-full flex items-center justify-center mb-5 shadow-none border-0">
                                    @if(request('trash'))
                                        <x-icon.trash class="w-11 h-11" />
                                    @elseif($isFiltered)
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                                        </svg>
                                    @else
                                        <x-icon.box class="w-11 h-11" />
                                    @endif
                                </div>
                                <p class="text-lg font-semibold text-secondary-900 mb-1.5" style="text-align:center;">
                                    @if(request('trash'))
                                        {{ __('ui.trash_empty') }}
                                    @elseif($isFiltered)
                                        {{ __('ui.no_results') }}
                                    @else
                                        {{ __('ui.inventory_empty') }}
                                    @endif
                                </p>
                                <p class="text-sm text-secondary-500 leading-relaxed" style="text-align:center;">
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
                        @if(auth()->user()->role === \App\Enums\UserRole::SUPERADMIN || (!request('trash') && auth()->user()->role === \App\Enums\UserRole::ADMIN))
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
                        @if(request('filter') == 'problematic')
                            <td class="px-4 py-4 text-left">
                                <div class="h-4 w-48 bg-secondary-100 rounded animate-pulse"></div>
                                <div class="h-3 w-32 bg-secondary-50 rounded animate-pulse mt-1"></div>
                            </td>
                        @else
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
                        @endif
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
    <div id="inventory-pagination-desktop" class="inventory-pagination-desktop">
        @if($spareparts->hasPages())
            <div class="bg-secondary-50 px-4 py-3 border-t border-secondary-200 sm:px-6">
                {{ $spareparts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
