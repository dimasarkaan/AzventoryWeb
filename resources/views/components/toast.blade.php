<div x-data="{
        notifications: [],
        add(e) {
            const id = Date.now();
            this.notifications.push({
                id: id,
                message: e.detail.message,
                type: e.detail.type || 'info',
            });
            setTimeout(() => { this.remove(id) }, Number(e.detail.timeout) || 4000);
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }"
    @notify.window="add($event)"
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 w-full max-w-sm pointer-events-none"
>
    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="true"
             x-transition:enter="ease-out duration-300 transition-all pointer-events-auto"
             x-transition:enter-start="opacity-0 translate-y-[-10px] sm:translate-y-0 sm:translate-x-4"
             x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
             x-transition:leave="ease-in duration-200 transition-all pointer-events-auto"
             x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
             x-transition:leave-end="opacity-0 translate-y-[-10px] sm:translate-y-0 sm:translate-x-4"
             class="pointer-events-auto w-full bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-xl flex items-start p-4 gap-3 relative overflow-hidden group">
             
             <!-- Icon mapping -->
             <div class="flex-shrink-0 mt-0.5" 
                  :class="{
                     'text-success-500': ['success', 'created', 'returned'].includes(notification.type),
                     'text-danger-500': ['error', 'danger'].includes(notification.type),
                     'text-warning-500': ['warning', 'deleted'].includes(notification.type),
                     'text-primary-500': ['info', 'borrowing', 'updated'].includes(notification.type)
                  }">
                 
                 <!-- Success / Created / Returned -->
                 <template x-if="['success', 'created', 'returned'].includes(notification.type)">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                 </template>
                 
                 <!-- Error / Danger -->
                 <template x-if="['error', 'danger'].includes(notification.type)">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75h.008v.008H12v-.008z" /></svg>
                 </template>

                 <!-- Warning / Deleted -->
                 <template x-if="['warning', 'deleted'].includes(notification.type)">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                 </template>

                 <!-- Info / Default / Borrowing / Updated -->
                 <template x-if="['info', 'borrowing', 'updated'].includes(notification.type) || !['success', 'created', 'returned', 'error', 'danger', 'warning', 'deleted'].includes(notification.type)">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                 </template>
             </div>
             
             <!-- Message -->
             <div class="flex-1 w-0">
                 <p class="text-sm font-semibold text-secondary-900" x-html="notification.message"></p>
             </div>
             
             <!-- Close Button -->
             <div class="ml-4 flex-shrink-0 flex items-start">
                 <button @click="remove(notification.id)" class="bg-white rounded-md inline-flex text-secondary-400 hover:text-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                     <span class="sr-only">Tutup</span>
                     <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                 </button>
             </div>
        </div>
    </template>
</div>
