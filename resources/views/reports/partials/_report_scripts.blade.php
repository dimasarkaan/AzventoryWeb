    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportManager', () => ({
                reportType: 'inventory_list', 
                period: 'this_month',
                startDate: '',
                endDate: '',
                loading: false,
                
                get isDateInvalid() {
                    if (this.period === 'custom' && this.startDate && this.endDate) {
                        return new Date(this.startDate) > new Date(this.endDate);
                    }
                    return false;
                },

                async downloadReport(e) {
                    if (this.isDateInvalid) {
                        e.preventDefault();
                        return;
                    }

                    const format = document.querySelector('input[name=export_format]:checked').value;
                    
                    if (format === 'excel') {
                        this.loading = true;
                        setTimeout(() => this.loading = false, 3000);
                        return;
                    }

                    e.preventDefault();
                    this.loading = true;

                    if (window.showToast) {
                        window.showToast('info', '{{ __('ui.report_processing') }}');
                    }

                    try {
                        const formData = new FormData(e.target);
                        const params = new URLSearchParams(formData);
                        
                        const response = await fetch(`${e.target.action}?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json, application/pdf',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const contentType = response.headers.get('content-type');

                        if (response.ok && contentType && contentType.includes('application/pdf')) {
                            const blob = await response.blob();
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            
                            const disposition = response.headers.get('content-disposition');
                            let filename = 'laporan.pdf';
                            if (disposition && disposition.indexOf('attachment') !== -1) {
                                const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                                const matches = filenameRegex.exec(disposition);
                                if (matches != null && matches[1]) { 
                                    filename = matches[1].replace(/['"]/g, '');
                                }
                            }
                            
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            
                            if (window.showToast) {
                                window.showToast('success', '{{ __('ui.report_success') }}');
                            }
                        } else if (response.ok && contentType && contentType.includes('application/json')) {
                            const data = await response.json();
                            if (data.success) {
                                if (window.showToast && data.message) {
                                    window.showToast('info', data.message);
                                }
                            } else {
                                window.showToast('error', data.message || '{{ __('ui.report_failed') }}');
                            }
                        } else {
                            throw new Error('{{ __('ui.report_error') }}');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        if (window.showToast) {
                            window.showToast('error', '{{ __('ui.report_system_error') }}');
                        }
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>
