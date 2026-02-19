<div x-show="scanModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div x-show="scanModalOpen" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
            @click="closeScanModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <!-- Modal Panel -->
        <div x-show="scanModalOpen" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full max-w-sm mx-auto">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start w-full">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Scan Part Number
                            </h3>
                            <div class="flex items-center gap-2">
                                <!-- Debug hidden -->
                                <label class="hidden items-center cursor-pointer">
                                    <input type="checkbox" x-model="debugMode" class="sr-only peer">
                                    <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                                    <span class="ms-2 text-xs font-medium text-gray-900">Debug</span>
                                </label>
                                <button type="button" @click="closeScanModal()" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Camera Container -->
                        <div class="mb-2 px-2 py-1 bg-amber-50 text-amber-700 text-xs rounded border border-amber-200">
                            <span class="font-bold">Info:</span> Pastikan foto label terlihat jelas dan terang.
                        </div>
                        <div class="grid gap-4" :class="debugMode ? 'grid-cols-2' : 'grid-cols-1'">
                            <!-- Live Camera -->
                            <div class="relative w-full bg-black rounded-lg overflow-hidden aspect-video">
                                <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                                
                                <div x-show="ocrLoading" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-20">
                                    <div class="text-center">
                                        <svg class="animate-spin h-10 w-10 text-white mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-white font-semibold text-sm">Memproses...</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Debug Preview -->
                            <div x-show="debugMode" class="relative w-full bg-gray-100 rounded-lg overflow-hidden aspect-video border border-gray-300">
                                <div class="absolute top-0 left-0 bg-black/50 text-white text-[10px] px-1">Processed</div>
                                <img :src="debugImage" class="w-full h-full object-contain" x-show="debugImage">
                                <div x-show="!debugImage" class="flex items-center justify-center h-full text-xs text-gray-400">Preview Processed</div>
                            </div>
                        </div>
                        <div class="mb-4"></div> <!-- Spacer -->

                        <!-- Camera Control Buttons -->
                        <div class="flex justify-between items-center mb-3 px-1" x-show="videoDevices.length > 1">
                            <span class="text-xs text-gray-500" x-text="'Kamera: ' + (currentDeviceLabel || 'Default')"></span>
                            <button type="button" @click="switchCamera()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-full transition-colors font-medium border border-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                Putar Kamera
                            </button>
                        </div>

                        <div x-show="scanErrorMsg" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative text-sm">
                            <span class="block sm:inline" x-text="scanErrorMsg"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="button" @click="captureAndScan" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm order-1 sm:order-none">
                                Ambil Gambar
                            </button>
                            
                            <label class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm cursor-pointer order-2 sm:order-none">
                                <span>Upload Foto</span>
                                <input type="file" class="hidden" accept="image/*" @change="handleFileUpload">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/inventory/partials/scan-modal.blade.php ENDPATH**/ ?>