<!-- Detail Modal (Premium Design) -->
            <div x-show="showActivityModal" 
                 class="fixed inset-0 z-[9999] overflow-y-auto" 
                 x-cloak
                 x-cloak
                 @keydown.escape.window="showActivityModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-secondary-900/60 backdrop-blur-sm" @click="showActivityModal = false" aria-hidden="true"></div>

                    <div class="relative inline-block w-full max-w-lg overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:max-w-2xl border border-secondary-100"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                        
                        {{-- Header --}}
                        <div class="bg-secondary-50/50 px-6 py-4 border-b border-secondary-100 flex justify-between items-center">
                            <h3 class="text-base font-bold text-secondary-900">{{ __('ui.activity_detail_title') }}</h3>
                            <button @click="showActivityModal = false" class="text-secondary-400 hover:text-secondary-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        {{-- Content --}}
                        <div class="px-6 py-6" x-show="selectedActivity">
                            {{-- Activity Summary --}}
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 flex-shrink-0 ring-4 ring-primary-50">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-lg font-bold text-secondary-900 leading-tight mb-1" x-text="selectedActivity?.description"></p>
                                    <span class="badge badge-secondary text-[10px] uppercase font-bold tracking-widest" x-text="selectedActivity?.action"></span>
                                </div>
                            </div>

                            {{-- Properties Table (The Audit Core) --}}
                            <div class="mb-4" x-show="selectedActivity && hasVisibleProperties(selectedActivity.properties)">
                                <h4 class="text-xs font-bold text-secondary-400 uppercase tracking-widest mb-3">{{ __('ui.change_details') }}</h4>
                                <div class="overflow-hidden border border-secondary-200 rounded-xl shadow-sm bg-white">
                                    <table class="min-w-full divide-y divide-secondary-200">
                                        <thead class="bg-secondary-50/50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-widest">{{ __('ui.column_label') }}</th>
                                                <th class="px-4 py-2 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-widest bg-red-50/30">{{ __('ui.before_label') }}</th>
                                                <th class="px-4 py-2 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-widest bg-green-50/30">{{ __('ui.after_label') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-secondary-100">
                                            <template x-if="selectedActivity && selectedActivity.properties">
                                                <template x-for="(values, key) in selectedActivity.properties" :key="key">
                                                    <template x-if="key !== 'ip' && key !== 'user_agent'">
                                                        <tr class="hover:bg-secondary-50/30 transition-colors">
                                                            <td class="px-4 py-3 text-xs font-bold text-secondary-700 capitalize" x-text="formatKey(key)"></td>
                                                            <td class="px-4 py-3 text-xs text-red-600 bg-red-50/10 break-all italic" x-text="formatValue(values.old)"></td>
                                                            <td class="px-4 py-3 text-xs text-green-700 bg-green-50/10 font-bold break-all" x-text="formatValue(values.new)"></td>
                                                        </tr>
                                                    </template>
                                                </template>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Metadata Info --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3 bg-white rounded-2xl border border-secondary-200 shadow-sm flex flex-col min-w-0">
                                    <p class="text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">{{ __('ui.user_label') }}</p>
                                    <p class="text-sm font-bold text-secondary-900 leading-tight break-all" x-text="selectedActivity?.user?.name || selectedActivity?.user_name || 'System'"></p>
                                    <p class="text-[10px] text-secondary-500 font-mono mt-1 break-all" x-text="selectedActivity?.user?.email || selectedActivity?.user_email || ''"></p>
                                </div>
                                <div class="p-3 bg-white rounded-2xl border border-secondary-200 shadow-sm flex flex-col min-w-0">
                                    <p class="text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">{{ __('ui.precise_time') }}</p>
                                    <p class="text-sm font-bold text-secondary-900" 
                                       x-text="selectedActivity ? new Date(selectedActivity.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' }) : '-'"></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

