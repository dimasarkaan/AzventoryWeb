<!-- PWA Install Prompt Component -->
<div x-data="pwaInstallPrompt()"
     x-show="showPrompt"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-8 scale-95"
     class="fixed bottom-4 left-4 right-4 md:left-auto md:right-8 md:bottom-8 md:w-96 z-[9999] bg-white rounded-2xl shadow-2xl border border-secondary-100 overflow-hidden"
     style="display: none;"
     x-cloak>
    
    <div class="relative p-5">
        <!-- Close Button -->
        <button @click="dismissPrompt" class="absolute top-3 right-3 text-secondary-400 hover:text-secondary-600 bg-secondary-50 hover:bg-secondary-100 rounded-full p-1.5 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="flex gap-4 items-start">
            <!-- App Icon -->
            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl shadow-lg flex items-center justify-center p-2 mt-1">
                <img src="{{ asset('logo.svg') }}" alt="Azventory Icon" class="w-full h-full object-contain filter brightness-0 invert">
            </div>

            <div class="flex-1">
                <h3 class="text-base font-bold text-secondary-900 mb-1">{{ __('ui.pwa_install_title') }}</h3>
                <p class="text-sm text-secondary-600 leading-relaxed mb-4">{{ __('ui.pwa_install_desc') }}</p>
                
                <!-- Android/Chrome Install Button -->
                <button x-show="canInstall" @click="installApp" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm shadow-primary-500/30 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    {{ __('ui.pwa_install_btn') }}
                </button>

                <!-- iOS Instructions -->
                <div x-show="isIOS && !isStandalone && !canInstall" class="bg-secondary-50 border border-secondary-200 rounded-xl p-3 text-xs text-secondary-700">
                    <div class="flex items-center gap-2 mb-2 font-semibold">
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ __('ui.pwa_ios_instruction_1') }}
                    </div>
                    <ol class="list-decimal list-inside space-y-1.5 pl-1">
                        <li class="flex items-center gap-1">{{ __('ui.pwa_ios_instruction_2') }} <svg class="w-4 h-4 inline pb-0.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> <b>Share</b> {{ __('ui.pwa_ios_instruction_2_suffix') }}</li>
                        <li>{{ __('ui.pwa_ios_instruction_3') }} <b>Add to Home Screen</b> <span class="bg-white border border-secondary-200 rounded-[4px] px-1 shadow-sm ml-1">+</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function pwaInstallPrompt() {
        return {
            showPrompt: false,
            deferredPrompt: null,
            canInstall: false,
            isIOS: false,
            isStandalone: false,
            
            init() {
                // Periksa status PWA
                this.isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone || document.referrer.includes('android-app://');
                
                // Deteksi iOS
                const userAgent = window.navigator.userAgent.toLowerCase();
                this.isIOS = /iphone|ipad|ipod/.test(userAgent);

                // Cek apakah prompt pernah di-dismiss
                const dismissed = localStorage.getItem('pwa_prompt_dismissed');
                const lastDismissedTime = localStorage.getItem('pwa_prompt_dismissed_time');
                
                // Tampilkan kembali setelah 3 hari jika di-dismiss
                let shouldShow = true;
                if (dismissed === 'true' && lastDismissedTime) {
                    const daysPassed = (new Date().getTime() - parseInt(lastDismissedTime)) / (1000 * 3600 * 24);
                    if (daysPassed < 3) shouldShow = false;
                }

                if (this.isStandalone) {
                    shouldShow = false; // Jangan tampilkan jika sudah diinstal
                }

                // Listen untuk event beforeinstallprompt (Android/Chrome)
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    this.canInstall = true;
                    if (shouldShow) {
                        setTimeout(() => this.showPrompt = true, 2000); // Delay 2 detik agar tidak terlalu intrusif
                    }
                });

                // Tampilkan instruksi iOS jika belum diinstal
                if (this.isIOS && !this.isStandalone && shouldShow) {
                    setTimeout(() => this.showPrompt = true, 2000);
                }
            },
            
            async installApp() {
                if (!this.deferredPrompt) return;
                
                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;
                
                if (outcome === 'accepted') {
                    this.showPrompt = false;
                }
                this.deferredPrompt = null;
                this.canInstall = false;
            },
            
            dismissPrompt() {
                this.showPrompt = false;
                localStorage.setItem('pwa_prompt_dismissed', 'true');
                localStorage.setItem('pwa_prompt_dismissed_time', new Date().getTime().toString());
            }
        }
    }
</script>
