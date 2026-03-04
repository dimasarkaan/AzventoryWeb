<script>
    console.log('Alpine Script Loaded');
    window.testGlobalClick = function() {
        alert('Global JS is working!');
    }

    document.addEventListener('alpine:init', () => {
        console.log('Alpine Init Event Fired');
        Alpine.data('inventoryDetail', () => ({
            stockModalOpen: false,
            borrowModalOpen: false,
            returnModalOpen: false,
            evidenceModalOpen: false,

            
            selectedBorrowing: null,
            activeEvidence: {},
            errors: {},
            successMessage: '',
            
            // Real-time stock state
            liveStock: {{ $sparepart->stock ?? 0 }},
            liveUpdateMessage: '',
            liveUpdateShow: false,
            
            maxReturnQty: 0,
            returnQty: '',
            returnCondition: '',
            isSubmitting: false,
            
            // Dropdown Logic
            dropdownOpen: false,
            conditionLabel: 'Pilih Kondisi',
            conditionOptions: [
                { value: 'good', label: 'Baik (Layak Pakai)' },
                { value: 'bad', label: 'Rusak (Perlu Perbaikan/Ganti)' },
                { value: 'lost', label: 'Hilang' }
            ],

            init() {
                console.log('Alpine Component Initialized via Script');

                // Real-time listener for Inventory Updates
                if (window.Echo) {
                    window.Echo.channel('inventory-updates')
                        .listen('.InventoryUpdated', (e) => {
                            // Cek jika update berasal dari item yang sedang dibuka
                            if (e.id === {{ $sparepart->id ?? 0 }}) {
                                console.log('Live Stock Update received:', e);
                                this.handleStockUpdate(e);
                            }
                        });
                }
            },

            handleStockUpdate(data) {
                // Update live stock
                this.liveStock = data.stock;
                
                // Show floating warning in modal
                if (this.stockModalOpen || this.borrowModalOpen) {
                    this.liveUpdateMessage = `⚠️ Stok baru saja diperbarui menjadi ${this.liveStock} Pcs.`;
                    this.liveUpdateShow = true;
                    
                    // Auto hide warning message after 8 seconds
                    setTimeout(() => {
                        this.liveUpdateShow = false;
                    }, 8000);
                }
            },

            selectCondition(option) {
                this.conditionLabel = option.label;
                this.returnCondition = option.value;
                this.dropdownOpen = false;
            },

            get isValid() {
                return this.returnQty !== '' && this.returnQty > 0 && 
                       this.returnQty <= this.maxReturnQty && 
                       this.returnCondition !== ''; 
            },

            initReturn(detail) {
                console.log('Init Return Open:', detail);
                this.maxReturnQty = detail.maxQty;
                this.returnQty = '';
                this.selectedBorrowing = detail.borrowingId;
                this.returnCondition = '';
                this.conditionLabel = 'Pilih Kondisi';
                this.returnModalOpen = true;
            },

            getItemColor(condition) {
                if (condition === 'good') return 'bg-success-500';
                if (condition === 'bad') return 'bg-warning-500';
                if (condition === 'lost') return 'bg-danger-500';
                return 'bg-secondary-400';
            },

            getBadgeColor(condition) {
                if (condition === 'good') return 'bg-success-100 text-success-800';
                if (condition === 'bad') return 'bg-warning-100 text-warning-800';
                if (condition === 'lost') return 'bg-danger-100 text-danger-800';
                return 'bg-secondary-100 text-secondary-800';
            },

            async submitReturn(e) {
                if (!this.isValid) return;
                this.isSubmitting = true;
                this.errors = {};
                this.successMessage = '';

                const formData = new FormData(e.target);

                try {
                    const response = await fetch(e.target.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.successMessage = data.message || 'Berhasil dikembalikan!';
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        if (response.status === 422) {
                            this.errors = data.errors;
                            this.$nextTick(() => {
                                if (this.$refs.errorContainer) {
                                    this.$refs.errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            });
                        } else {
                            alert(data.message || 'Terjadi kesalahan sistem.');
                        }
                    }
                } catch (error) {
                    console.error('Submission error:', error);
                    alert('Gagal menghubungi server.');
                } finally {
                    this.isSubmitting = false;
                }
            }
        }));
    });
</script>
