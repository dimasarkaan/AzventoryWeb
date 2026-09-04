<script>
        function activityLogComponent() {
            return {
                userRole: '{{ auth()->user()->role->value }}',
                showFilters: false,
                showActivityModal: false,
                selectedActivity: null,
                logs: @js($activityLogs->getCollection()->keyBy('id')),
                lastId: {{ $activityLogs->first()?->id ?? 0 }},
                isPolling: false,

                init() {
                    console.log('[Alpine] Activity Log Initialized');
                    
                    // Polling Fallback (setiap 15 detik)
                    setInterval(() => {
                        this.fetchNewLogs();
                    }, 15000);

                    if (window.Echo) {
                        console.log('[Alpine] Echo found, listening for activity-logs...');
                        window.Echo.channel('activity-logs')
                            .listen('.ActivityLogged', (e) => {
                                console.log('[Alpine] Event received:', e);
                                
                                // Mapping payload dari broadcastWith ke format log lokal
                                const activity = {
                                    id: e.id,
                                    action: e.action,
                                    description: e.description,
                                    user_name: e.user_name,
                                    user_email: e.user_email || '-',
                                    created_at: e.created_at,
                                    properties: e.properties || {}
                                };
                                
                                if (!this.logs[activity.id]) {
                                    console.log('[Alpine] New log added to UI:', activity.id);
                                    this.logs[activity.id] = activity;
                                    this.appendLogToUI(activity);
                                    
                                    // Update lastId agar polling tidak menduplikasi
                                    if (activity.id > this.lastId) {
                                        this.lastId = activity.id;
                                    }
                                }
                            });
                    }
                },

                async fetchNewLogs() {
                    if (this.isPolling) return;
                    this.isPolling = true;

                    try {
                        // Hanya fetch jika sedang di halaman 1 atau tidak ada filter aktif yang rumit
                        const params = new URLSearchParams(window.location.search);
                        if (params.has('page') && params.get('page') !== '1') {
                            this.isPolling = false;
                            return;
                        }

                        const response = await fetch(`${window.location.pathname}?wantsJson=1&since_id=${this.lastId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            if (data.activityLogs && data.activityLogs.data.length > 0) {
                                const newLogs = data.activityLogs.data.filter(log => log.id > this.lastId);
                                
                                if (newLogs.length > 0) {
                                    this.lastId = Math.max(this.lastId, ...newLogs.map(l => l.id));
                                    newLogs.reverse().forEach(log => {
                                        if (!this.logs[log.id]) {
                                            this.logs[log.id] = log;
                                            this.appendLogToUI(log);
                                        }
                                    });
                                }
                            }
                        }
                    } catch (e) {
                        console.error('[Polling] Error:', e);
                    } finally {
                        this.isPolling = false;
                    }
                },
                appendLogToUI(activity) {
                    const desktopBody = document.getElementById('desktop-logs-body');
                    const mobileContainer = document.getElementById('mobile-logs-container');
                    
                    const action = (activity.action || '').toLowerCase();
                    let badgeColor = 'bg-secondary-50 text-secondary-700 border-secondary-200';
                    if (action.includes('buat') || action.includes('create')) {
                        badgeColor = 'bg-success-50 text-success-700 border-success-200';
                    } else if (action.includes('update') || action.includes('perbarui') || action.includes('edit')) {
                        badgeColor = 'bg-primary-50 text-primary-700 border-primary-200';
                    } else if (action.includes('hapus') || action.includes('delete') || action.includes('reject') || action.includes('tolak')) {
                        badgeColor = 'bg-red-50 text-red-700 border-red-200';
                    }

                    if (desktopBody) {
                        const emptyRow = desktopBody.querySelector('td[colspan]');
                        if (emptyRow) emptyRow.closest('tr').remove();
                        
                        const newRowHTML = `
                            <tr class="group hover:bg-secondary-50 transition-colors animate-highlight" id="log-${activity.id}">
                                ${this.userRole !== 'operator' ? `
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                            <span class="font-bold text-xs">${(activity.user_name || 'S').charAt(0)}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-secondary-900">${activity.user_name || 'System'}</div>
                                            <div class="text-xs text-secondary-500 font-mono">${activity.user_email || '-'}</div>
                                        </div>
                                    </div>
                                </td>
                                ` : ''}
                                <td>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border ${badgeColor}">
                                        ${activity.action}
                                    </span>
                                </td>
                                <td class="text-sm text-secondary-600">
                                    <div class="max-w-lg whitespace-normal break-words">${activity.description}</div>
                                </td>
                                <td class="text-sm text-secondary-500 whitespace-nowrap">
                                    ${new Date(activity.created_at).toLocaleString('id-ID')}
                                </td>
                                <td class="text-right">
                                    ${(activity.properties && Object.keys(activity.properties).length > 0) ? `
                                        <button class="view-log-btn p-2 text-secondary-400 hover:text-primary-600 transition-colors rounded-full hover:bg-primary-50"
                                                title="Lihat Detail Perubahan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    ` : ''}
                                </td>
                            </tr>
                        `;
                        desktopBody.insertAdjacentHTML('afterbegin', newRowHTML);
                        const newRow = desktopBody.firstElementChild;
                        const btn = newRow.querySelector('.view-log-btn');
                        if (btn) btn.onclick = () => this.viewActivityDetails(activity.id);
                    }

                    if (mobileContainer) {
                        const emptyCard = mobileContainer.querySelector('.text-center');
                        if (emptyCard) emptyCard.closest('.card').remove();

                        const newCardHTML = `
                            <div class="card p-4 flex flex-col gap-3 animate-highlight">
                                <div class="flex items-center justify-between gap-4">
                                    ${this.userRole !== 'operator' ? `
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="h-10 w-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 font-bold overflow-hidden">
                                            ${(activity.user_name || 'S').charAt(0)}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-secondary-900 truncate">${activity.user_name || 'System'}</div>
                                            <div class="text-xs text-secondary-500">Baru Saja</div>
                                        </div>
                                    </div>
                                    ` : `
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-secondary-900">Baru Saja</div>
                                    </div>
                                    `}
                                    <span class="badge ${badgeColor} text-[10px] uppercase font-bold tracking-wider flex-shrink-0 whitespace-nowrap">${activity.action}</span>
                                </div>
                                <div class="text-sm text-secondary-600 bg-secondary-50 p-3 rounded-lg border border-secondary-100 flex justify-between items-start gap-4">
                                    <span class="flex-1">${activity.description}</span>
                                    ${(activity.properties && Object.keys(activity.properties).length > 0) ? `
                                        <button class="view-log-btn px-3 py-1.5 text-sm font-semibold text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors shadow-sm">
                                            Detail
                                        </button>
                                    ` : ''}
                                </div>
                                <div class="text-xs text-secondary-400 text-right">
                                    Baru Saja
                                </div>
                            </div>
                        `;
                        mobileContainer.insertAdjacentHTML('afterbegin', newCardHTML);
                        const newCard = mobileContainer.firstElementChild;
                        const btn = newCard.querySelector('.view-log-btn');
                        if (btn) btn.onclick = () => this.viewActivityDetails(activity.id);
                    }
                },

                formatValue(val) {
                    if (val === null || val === undefined) return '-';
                    if (typeof val === 'boolean') return val ? 'Ya' : 'Tidak';
                    return val;
                },

                formatKey(key) {
                    const translations = {
                        'item_ids': 'ID Item',
                        'items': 'Daftar Item',
                        'names': 'Daftar Nama',
                        'counts': 'Jumlah Potongan',
                        'total_labels': 'Total Label',
                        'name': 'Nama',
                        'brand': 'Merek',
                        'category': 'Kategori',
                        'stock': 'Stok',
                        'price': 'Harga',
                        'description': 'Deskripsi',
                        'condition': 'Kondisi',
                        'location': 'Lokasi',
                        'color': 'Warna',
                        'status': 'Status',
                        'type': 'Tipe',
                        'role': 'Peran',
                        'email': 'Email',
                        'password': 'Kata Sandi',
                        'part_number': 'No. Identifikasi',
                        'remarks': 'Catatan',
                        'problem_chronology': 'Kronologi Masalah'
                    };
                    const lowerKey = key.toLowerCase();
                    return translations[lowerKey] || key.replace(/_/g, ' ');
                },

                viewActivityDetails(id) {
                    console.log('[Alpine] viewActivityDetails called for ID:', id);
                    if (!this.logs) {
                        console.error('[Alpine] logs object is undefined!');
                        return;
                    }
                    this.selectedActivity = this.logs[id];
                    if (this.selectedActivity) {
                        this.showActivityModal = true;
                    } else {
                        console.warn('[Alpine] Log not found for ID:', id);
                    }
                },

                hasVisibleProperties(properties) {
                    if (!properties) return false;
                    return Object.keys(properties).some(key => key !== 'ip' && key !== 'user_agent');
                }
            };
        }
    </script>