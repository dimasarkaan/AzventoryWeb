@push('scripts')
<script>
    // Fungsi global untuk membersihkan localStorage saat logout
    function clearDashboardPeriod() {
        try {
            ['superadmin', 'admin', 'operator'].forEach(function(role) {
                localStorage.removeItem(role + '_dashboard_period');
                localStorage.removeItem(role + '_dashboard_start');
                localStorage.removeItem(role + '_dashboard_end');
            });
        } catch(e) {}
    }

    function notificationComponent() {
        return {
            notificationOpen: false, 
            unreadCount: {{ auth()->check() ? auth()->user()->unreadNotifications()->count() : 0 }},
            notifications: [],
            isLoading: true,
            init() { 
                this.fetchNotifications(); 
                
                // Listener Real-time
                if (window.Echo) {
                    window.Echo.private('App.Models.User.{{ auth()->id() }}')
                        .notification((notification) => {
                            // Perbarui Jumlah
                            this.unreadCount++;

                            // Tambahkan ke Daftar
                            this.notifications.unshift({
                                id: notification.id,
                                type: notification.type,
                                read_at: null,
                                created_at: new Date().toISOString(),
                                data: {
                                    title: notification.title,
                                    message: notification.message,
                                    url: notification.url
                                }
                            });

                            // Tampilkan Toast
                            if (typeof window.showToast === 'function') {
                                window.showToast(notification.type || 'success', notification.message || notification.title);
                            }
                        });
                }
            },
            timeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);

                let interval = seconds / 31536000;
                if (interval > 1) return Math.floor(interval) + " tahun yang lalu";
                
                interval = seconds / 2592000;
                if (interval > 1) return Math.floor(interval) + " bulan yang lalu";
                
                interval = seconds / 86400;
                if (interval > 1) return Math.floor(interval) + " hari yang lalu";
                
                interval = seconds / 3600;
                if (interval > 1) return Math.floor(interval) + " jam yang lalu";
                
                interval = seconds / 60;
                if (interval > 1) return Math.floor(interval) + " menit yang lalu";
                
                return "Baru saja";
            },
            fetchNotifications() {
                this.isLoading = true;
                axios.get('/notifications?_=' + new Date().getTime())
                    .then(response => {
                        this.notifications = response.data;
                    })
                    .catch(error => console.error(error))
                    .finally(() => this.isLoading = false);
            },
            markAsRead(id, url, type) {
                 axios.patch('/notifications/' + id + '/read')
                    .then(response => {
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        this.notifications = this.notifications.map(n => 
                            n.id === id ? { ...n, read_at: new Date().toISOString() } : n
                        );
                        
                        const targetUrl = response.data.url || url;
                        if (targetUrl) {
                            if (type === 'App\\Notifications\\ReportReadyNotification') {
                                window.open(targetUrl, '_blank');
                            } else {
                                window.location.href = targetUrl;
                            }
                        }
                    })
                    .catch(error => console.error(error));
            },
            getIcon(type) {
                if (type.includes('LowStock') || type.includes('ApproachingStock')) {
                    return '<div class="p-1.5 bg-warning-50 text-warning-600 rounded-lg"><svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>';
                }
                if (type.includes('StockRequest')) {
                    return '<div class="p-1.5 bg-primary-50 text-primary-600 rounded-lg"><svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg></div>';
                }
                if (type.includes('OverdueBorrowing')) {
                    return '<div class="p-1.5 bg-danger-50 text-danger-600 rounded-lg"><svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>';
                }
                if (type.includes('ReportReady')) {
                    return '<div class="p-1.5 bg-success-50 text-success-600 rounded-lg"><svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>';
                }
                return '<div class="p-1.5 bg-secondary-50 text-secondary-600 rounded-lg"><svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg></div>';
            },
        }
    }
</script>
@endpush
