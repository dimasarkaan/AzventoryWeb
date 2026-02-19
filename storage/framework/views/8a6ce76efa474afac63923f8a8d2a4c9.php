<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'options' => [], 'selected' => '', 'placeholder' => 'Pilih Opsi', 'submitOnChange' => false, 'width' => 'w-full md:w-auto']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['name', 'options' => [], 'selected' => '', 'placeholder' => 'Pilih Opsi', 'submitOnChange' => false, 'width' => 'w-full md:w-auto']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="{
    open: false,
    selected: '<?php echo e((string)$selected); ?>',
    selectedLabel: '',
    placeholder: '<?php echo e($placeholder); ?>',
    options: <?php echo e(json_encode($options)); ?>,
    init() {
        // Cari label untuk nilai awal
        if (this.selected && this.options.hasOwnProperty(this.selected)) {
            this.selectedLabel = this.options[this.selected];
        } else {
             this.selectedLabel = this.placeholder;
        }
        
        $watch('selected', value => {
             if (<?php echo e($submitOnChange ? 'true' : 'false'); ?>) {
                 // Gunakan timeout kecil agar nilai input terupdate sebelum submit
                 setTimeout(() => {
                    const form = $el.closest('form');
                    if (form) {
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.submit();
                        }
                    }
                 }, 50);
             }
        });
    },
    select(value, label) {
        this.selected = value;
        this.selectedLabel = label;
        this.open = false;
    }
}" 
@reset-filters.window="selected = ''; selectedLabel = placeholder; open = false"
class="relative <?php echo e($width); ?>">
    <input type="hidden" name="<?php echo e($name); ?>" :value="selected">
    
    <button type="button" @click="open = !open" @click.away="open = false"
            class="input-field w-full text-left flex justify-between items-center rounded-xl py-2.5 px-4 text-sm cursor-pointer hover:border-primary-400 focus:ring-2 ring-primary-500 bg-white transition-all shadow-sm">
        <span x-text="selectedLabel" :class="{'text-secondary-900': selected, 'text-secondary-500': !selected}" class="truncate mr-2"></span>
        <svg class="w-4 h-4 text-secondary-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-1 w-full min-w-[100%] bg-white rounded-xl shadow-xl border border-secondary-100 overflow-hidden" 
         style="display: none;">
        <div class="max-h-60 overflow-y-auto p-1 space-y-0.5">
             <!-- Opsi Reset -->
             <div @click="select('', placeholder)"
                  class="px-3 py-2 rounded-lg cursor-pointer hover:bg-primary-50 hover:text-primary-700 transition-colors text-sm"
                  :class="{'bg-primary-50 text-primary-700 font-medium': !selected}">
                 <span x-text="placeholder"></span>
             </div>
             
             <!-- Opsi -->
             <!-- Catatan: iterasi objek di Alpine x-for menggunakan sintaks (value, key) untuk array, tapi (val, key) untuk objek. -->
             <!-- Kita memperlakukan opsi sebagai objek/array asosiatif dari PHP json_encode -->
             <template x-for="(label, value) in options" :key="value">
                <div @click="select(value, label)"
                     class="px-3 py-2 rounded-lg cursor-pointer text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors text-sm"
                     :class="{'bg-primary-50 text-primary-700 font-medium': selected == value}">
                    <span x-text="label"></span>
                </div>
             </template>
        </div>
    </div>
</div>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/components/select.blade.php ENDPATH**/ ?>