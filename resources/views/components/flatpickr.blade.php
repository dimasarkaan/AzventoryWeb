@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" crossorigin="anonymous">
    <style>
        /* --- Flatpickr Premium Theme (Figma Auto Layout) --- */
        .flatpickr-calendar { 
            background: #ffffff; 
            border: 1px solid #f1f5f9; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02); 
            border-radius: 28px; 
            z-index: 99999 !important;
            padding: 24px; 
            font-family: inherit;
            width: 380px !important; 
            max-width: 95vw !important; /* Mobile Fix */
            overflow: visible !important; 
            margin-top: 12px !important; /* Proper spacing from input */
            position: absolute; /* Auto fallback bila CDN lambat */
        }

        /* Mencegah kalender ngablak / bocor jika CDN JSDelivr diblokir atau telat me-load */
        .flatpickr-calendar:not(.open):not(.inline) {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }

        @media (max-width: 480px) {
            .flatpickr-calendar {
                padding: 16px 12px !important;
                width: 320px !important;
                border-radius: 20px;
            }
            .custom-month-selector {
                min-width: 65px !important;
                padding: 0 8px !important;
                font-size: 0.8rem !important;
                justify-content: center !important;
            }
            .numInputWrapper {
                width: 70px !important;
                grid-column: span 1 / span 1 !important;
            }
            .flatpickr-months {
                gap: 8px !important;
            }
        }
        
        /* FORCE ALL INTERNAL CONTAINERS TO ALLOW DROPDOWN OVERLAP & CENTERING */
        .flatpickr-innerContainer {
            display: flex !important;
            justify-content: center !important;
        }
        .flatpickr-rContainer {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            max-width: 100% !important;
            overflow: visible !important;
        }
        .flatpickr-days, 
        .flatpickr-weeks,
        .flatpickr-month, 
        .flatpickr-current-month { 
            overflow: visible !important; 
        }
        .dayContainer, .flatpickr-weekdaycontainer {
            margin: 0 auto !important;
            justify-content: center !important;
            display: flex !important;
            flex-wrap: wrap !important;
        }
        .flatpickr-weekdaycontainer { width: 100% !important; }

        /* Header: True Auto Layout & Stacking Context */
        .flatpickr-months { 
            display: flex !important;
            align-items: center !important;
            justify-content: center !important; /* CENTER EVERYTHING */
            padding: 4px 0 !important;
            margin-bottom: 20px;
            position: relative !important;
            z-index: 100 !important; /* Higher than days */
            gap: 16px !important; /* Slightly more gap for balance */
            overflow: visible !important;
            width: 100% !important;
        }
        .flatpickr-month { 
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 44px !important;
            width: 100% !important;
            margin: 0 !important;
        }
        .flatpickr-current-month { 
            position: static !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            padding: 0 !important;
            gap: 12px !important;
            font-size: 1rem;
            font-weight: 700;
        }
        
        /* Custom Dropdown Trigger (Replaces Static/Native) */
        .custom-month-selector {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 16px;
            height: 44px;
            min-width: 155px; 
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            z-index: 101 !important;
        }
        .custom-month-selector:hover { border-color: #3b82f6; background: #f8fafc; }
        .custom-month-selector.active { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .custom-month-selector .month-name { font-weight: 800; color: #1e293b; font-size: 0.9rem; flex: 1; text-align: center; }
        .custom-month-selector svg { color: #3b82f6; width: 14px; height: 14px; }
        
        /* Custom Month List Panel */
        .custom-month-list {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 100%;
            background: #ffffff !important;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            z-index: 9999999 !important; /* Ensure it stays above days */
            display: none;
            padding: 6px;
            max-height: 280px;
            overflow-y: auto;
        }
        .custom-month-list div {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s;
            font-weight: 600;
        }
        .custom-month-list div:hover { background: #eff6ff; color: #3b82f6; }
        .custom-month-list div.active { background: #3b82f6; color: #ffffff; font-weight: 700; }
        
        /* Year Input - Figma Precision */
        .numInputWrapper { 
            width: 85px !important;
            height: 44px !important;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 !important;
            transition: all 0.2s ease;
            display: flex !important;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: visible !important;
        }
        .numInputWrapper:hover, .numInputWrapper:focus-within { border-color: #3b82f6; }
        .numInputWrapper:focus-within { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .numInputWrapper input { 
            font-weight: 800 !important; 
            color: #1e293b !important; 
            font-size: 0.95rem !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
            text-align: center !important;
            background: transparent !important;
            border: none !important;
            outline: none !important;
        }
        .numInputWrapper span { display: none !important; }

        /* Navigation Buttons - Locked 44px */
        .flatpickr-prev-month, .flatpickr-next-month {
            position: static !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            height: 44px !important;
            width: 44px !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            border: 2px solid #f1f5f9 !important;
            transition: all 0.2s ease;
            z-index: 10;
        }
        .flatpickr-prev-month:hover, .flatpickr-next-month:hover { 
            background: #eff6ff !important; 
            border-color: #3b82f6 !important;
            transform: translateY(-1px);
        }
        .flatpickr-prev-month svg, .flatpickr-next-month svg { width: 14px !important; height: 14px !important; fill: #3b82f6 !important; }
        
        /* Weekdays & Days Polishing */
        .flatpickr-weekday { color: #94a3b8; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; padding: 16px 0; }
        
        /* Default: Current Month Days (Deep & Circular) */
        .flatpickr-day { 
            border-radius: 9999px !important; /* PERFECT CIRCLE */
            color: #0f172a !important; /* Deepest Slate */
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
            border: 2px solid transparent !important; 
            height: 40px !important; /* Adjusted slightly for perfect circle in 44px cell */
            line-height: 36px !important; 
            font-weight: 800 !important; 
            width: 40px !important;
            margin: 2px auto !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .flatpickr-day.today { 
            background: #eff6ff !important; 
            color: #3b82f6 !important; 
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        
        /* Non-current Month Days - Extreme Transition */
        .flatpickr-day.prevMonthDay, 
        .flatpickr-day.nextMonthDay,
        .flatpickr-day.prevMonthDay.inRange,
        .flatpickr-day.nextMonthDay.inRange {
            color: #94a3b8 !important; /* Neutral Gray */
            opacity: 0.45 !important; /* Slightly more visible */
            font-weight: 400 !important;
            background: transparent !important;
            border-color: transparent !important;
            pointer-events: all; /* Re-enable selection as requested */
        }
        .flatpickr-day.prevMonthDay:hover, .flatpickr-day.nextMonthDay:hover {
            background: #f1f5f9 !important;
            opacity: 0.8 !important;
            color: #94a3b8 !important;
        }
        
        /* Disabled Days (Past dates, etc) */
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            color: #cbd5e1 !important; 
            background: transparent !important;
            border-color: transparent !important;
            font-weight: 400 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            opacity: 0.5 !important;
        }

        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { 
            background: #3b82f6 !important; 
            color: #ffffff !important;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
            opacity: 1 !important;
            border-color: #3b82f6 !important;
        }
        .flatpickr-day.inRange { 
            background: #f1f7ff !important; 
            color: #3b82f6 !important; 
            border-radius: 0 !important; /* Keep range segments square-ish for continuity */
            opacity: 1 !important;
        }
        .flatpickr-day.startRange { border-radius: 9999px 0 0 9999px !important; }
        .flatpickr-day.endRange { border-radius: 0 9999px 9999px 0 !important; }
        .flatpickr-day.selected.startRange.endRange { border-radius: 9999px !important; }

        .flatpickr-day:not(.selected):not(.prevMonthDay):not(.nextMonthDay):hover { 
            background: #f1f5f9; 
            border-color: #e2e8f0;
            transform: scale(1.1); 
        }

        /* Drag-to-Select Highlight */
        .flatpickr-day.drag-hover {
            background: #dbeafe !important;
            color: #2563eb !important;
            border-color: #93c5fd !important;
            opacity: 1 !important;
            border-radius: 0 !important;
        }
        .flatpickr-day.drag-hover:first-of-type,
        .flatpickr-day.drag-hover:first-child { border-radius: 9999px 0 0 9999px !important; }
        .flatpickr-day.drag-hover:last-of-type,
        .flatpickr-day.drag-hover:last-child { border-radius: 0 9999px 9999px 0 !important; }

        /* Prevent text selection during drag */
        .dayContainer { user-select: none; -webkit-user-select: none; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reusable logic for custom Flatpickr Month UI
            const injectCustomMonthUI = (instance) => {
                if (!instance || !instance.calendarContainer) return;
                const container = instance.calendarContainer.querySelector('.flatpickr-current-month');
                if (!container) return;

                container.innerHTML = `
                    <div class="custom-month-selector" id="custom-month-btn-${instance.id}" role="button" tabindex="0" aria-label="{{ __('ui.select_month') }}" aria-haspopup="listbox">
                        <span class="month-name">
                            <span class="hidden sm:inline">${instance.l10n.months.longhand[instance.currentMonth]}</span>
                            <span class="sm:hidden font-extrabold text-lg">${instance.currentMonth + 1}</span>
                        </span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin='round' stroke-width='2.5' d='M19 9l-7 7-7-7'></path></svg>
                        <div class="custom-month-list" id="custom-month-panel-${instance.id}" role="listbox" aria-label="{{ __('ui.month_list') }}">
                            ${instance.l10n.months.longhand.map((m, i) => `
                                <div class="${i === instance.currentMonth ? 'active' : ''}" data-index="${i}" role="option" aria-selected="${i === instance.currentMonth ? 'true' : 'false'}">${m}</div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="numInputWrapper">
                        <input id="flatpickr_year_input_${instance.id}" name="flatpickr_year" class="numInput cur-year" type="text" inputmode="numeric" value="${instance.currentYear}" aria-label="{{ __('ui.input_year') }}">
                    </div>
                `;

                const trigger = container.querySelector('#custom-month-btn-' + instance.id);
                const panel = container.querySelector('#custom-month-panel-' + instance.id);
                const yearInput = container.querySelector('.numInput');

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isVisible = panel.style.display === 'block';
                    panel.style.display = isVisible ? 'none' : 'block';
                    trigger.classList.toggle('active', !isVisible);
                });

                panel.querySelectorAll('div[data-index]').forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const targetMonth = parseInt(item.getAttribute('data-index'));
                        const offset = targetMonth - instance.currentMonth;
                        if (offset !== 0) {
                            instance.changeMonth(offset, true);
                        }
                        panel.style.display = 'none';
                        trigger.classList.remove('active');
                    });
                });

                if (yearInput) {
                    yearInput.addEventListener('keydown', (e) => {
                        if (['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter'].includes(e.key)) return;
                        if (!/[0-9]/.test(e.key)) e.preventDefault();
                    });

                    yearInput.addEventListener('input', function() {
                        this.value = this.value.replace(/[^0-9]/g, ''); 
                        if (this.value.length > 4) this.value = this.value.slice(0, 4);
                        if (this.value.length === 4) instance.changeYear(parseInt(this.value));
                    });

                    yearInput.addEventListener('blur', function() {
                        const val = parseInt(this.value);
                        if (isNaN(val) || val < 1900) {
                            if (window.showToast) window.showToast('warning', '{{ __('ui.invalid_year') }}');
                            this.value = new Date().getFullYear();
                            instance.changeYear(parseInt(this.value));
                        }
                    });
                }
            };

            // Shared flatpickr options
            const getSharedOptions = (isMobile) => ({
                locale: {
                    rangeSeparator: " - ",
                    weekdays: {
                        shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                        longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                    },
                    months: {
                        shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                        longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                    }
                },
                position: "auto", 
                monthSelectorType: 'static', 
                altInput: true,
                altFormat: isMobile ? "d/m/y" : "j F Y",
                dateFormat: "Y-m-d",
                altInputClass: "w-full pl-12 pr-4 py-3 text-sm bg-secondary-50/50 border-secondary-200 rounded-2xl text-secondary-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all cursor-pointer font-semibold placeholder:text-secondary-400",
                allowInput: false,
                animate: true,
                onReady: function(selectedDates, dateStr, instance) {
                    if (instance.altInput && instance.element.id) {
                        instance.altInput.id = instance.element.id + '_display';
                    }
                    instance.id = Math.random().toString(36).substr(2, 9);
                    const inject = () => injectCustomMonthUI(instance);
                    inject();
                    instance.currentInject = inject;
                    
                    document.addEventListener('click', () => {
                        const panels = document.querySelectorAll('.custom-month-list');
                        panels.forEach(p => p.style.display = 'none');
                        document.querySelectorAll('.custom-month-selector').forEach(s => s.classList.remove('active'));
                    });
                },
                onMonthChange: function(selectedDates, dateStr, instance) {
                    if (instance.currentInject) instance.currentInject();
                },
                onYearChange: function(selectedDates, dateStr, instance) {
                    if (instance.currentInject) instance.currentInject();
                },
            });

            const isMobile = window.innerWidth < 480;

            // Initialize Single Date Pickers (e.g. Borrow Modal)
            document.querySelectorAll('input.flatpickr-single').forEach(input => {
                const options = getSharedOptions(isMobile);
                // Adjust class based on generic input
                options.altInputClass = input.getAttribute('data-alt-class') || "form-input block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm";
                options.minDate = input.getAttribute('min') || null;
                options.position = "auto";
                
                // Allow x-model integration
                options.onChange = function(selectedDates, dateStr, instance) {
                    input.value = dateStr;
                    input.dispatchEvent(new Event('input', { bubbles: true })); // trigger alpinejs
                };
                
                flatpickr(input, options);
            });

            // Initialize Range Pickers (e.g. Reports)
            document.querySelectorAll('.flatpickr-range-container').forEach(container => {
                const startInput = container.querySelector('.range-start');
                const endInput = container.querySelector('.range-end');
                const pickerInput = container.querySelector('.range-picker-input');
                
                if (!startInput || !endInput || !pickerInput) return;

                const options = getSharedOptions(isMobile);
                options.mode = "range";
                options.conjunction = " - ";
                options.position = "auto";
                options.altInputClass = pickerInput.getAttribute('data-alt-class') || options.altInputClass;
                
                let lastRange = [];
                let skipNextChange = false;
                let preventDragClick = false;

                if (startInput.value && endInput.value) {
                    options.defaultDate = [startInput.value, endInput.value];
                }

                // Add Drag-to-Select Logic to onReady
                const baseOnReady = options.onReady;
                options.onReady = function(selectedDates, dateStr, instance) {
                    baseOnReady(selectedDates, dateStr, instance);

                    if (instance.selectedDates.length === 2) {
                        lastRange = [new Date(instance.selectedDates[0]), new Date(instance.selectedDates[1])];
                    }

                    // Drag Logic
                    const cal = instance.calendarContainer;
                    let dragStartEl = null;
                    let dragStartDate = null;
                    let dragAnchor = null;
                    let hasDragged = false;

                    function clearDragHL() {
                        cal.querySelectorAll('.flatpickr-day.drag-hover').forEach(el => el.classList.remove('drag-hover'));
                    }

                    function highlightRange(d1, d2) {
                        clearDragHL();
                        const s = Math.min(d1.getTime(), d2.getTime());
                        const en = Math.max(d1.getTime(), d2.getTime());
                        cal.querySelectorAll('.flatpickr-day').forEach(el => {
                            if (el.dateObj && el.dateObj.getTime() >= s && el.dateObj.getTime() <= en) {
                                el.classList.add('drag-hover');
                            }
                        });
                    }

                    function applyDragRange(d1, d2) {
                        let s = new Date(Math.min(d1.getTime(), d2.getTime()));
                        let en = new Date(Math.max(d1.getTime(), d2.getTime()));
                        preventDragClick = true;
                        setTimeout(() => preventDragClick = false, 100);
                        skipNextChange = true;
                        instance.setDate([s, en], true);
                    }

                    function initDrag(dayEl) {
                        if (!dayEl || dayEl.classList.contains('flatpickr-disabled') || !dayEl.dateObj) return;
                        dragStartEl = dayEl;
                        dragStartDate = dayEl.dateObj;
                        hasDragged = false;
                        dragAnchor = null;

                        if (lastRange.length === 2) {
                            if (dayEl.classList.contains('startRange')) dragAnchor = new Date(lastRange[1]);
                            else if (dayEl.classList.contains('endRange')) dragAnchor = new Date(lastRange[0]);
                        }
                    }

                    function onDragMove(dayEl) {
                        if (!dragStartDate || !dayEl || !dayEl.dateObj || dayEl === dragStartEl) return;
                        hasDragged = true;
                        if (dragAnchor) highlightRange(dragAnchor, dayEl.dateObj);
                        else highlightRange(dragStartDate, dayEl.dateObj);
                    }

                    function finishDrag(dayEl) {
                        if (!dragStartDate) return;
                        clearDragHL();
                        if (hasDragged && dayEl && dayEl.dateObj && dayEl !== dragStartEl) {
                            if (dragAnchor) applyDragRange(dragAnchor, dayEl.dateObj);
                            else applyDragRange(dragStartDate, dayEl.dateObj);
                        }
                        dragStartEl = null; dragStartDate = null; dragAnchor = null; hasDragged = false;
                    }

                    cal.addEventListener('mousedown', e => initDrag(e.target.closest('.flatpickr-day')));
                    cal.addEventListener('mouseover', e => onDragMove(e.target.closest('.flatpickr-day')));
                    document.addEventListener('mouseup', e => finishDrag(e.target.closest('.flatpickr-day')));
                    
                    cal.addEventListener('click', e => {
                        if (preventDragClick) { e.stopImmediatePropagation(); e.preventDefault(); }
                    }, true);

                    cal.addEventListener('touchstart', e => initDrag(document.elementFromPoint(e.touches[0].clientX, e.touches[0].clientY)?.closest('.flatpickr-day')), { passive: true });
                    cal.addEventListener('touchmove', e => {
                        if (!dragStartDate) return;
                        onDragMove(document.elementFromPoint(e.touches[0].clientX, e.touches[0].clientY)?.closest('.flatpickr-day'));
                        e.preventDefault();
                    }, { passive: false });
                    cal.addEventListener('touchend', e => {
                        if (!dragStartDate) return;
                        finishDrag(document.elementFromPoint(e.changedTouches[0].clientX, e.changedTouches[0].clientY)?.closest('.flatpickr-day'));
                    });
                };

                options.onChange = function(selectedDates, dateStr, instance) {
                    const applyRange = (dates) => {
                        const diffDays = Math.round((dates[1] - dates[0]) / (1000 * 60 * 60 * 24));
                        if (diffDays > 365) {
                            instance.clear(); lastRange = []; startInput.value = ''; endInput.value = '';
                            if (window.showToast) window.showToast('warning', '{{ __('ui.max_range_365') }}');
                            return;
                        }
                        lastRange = [new Date(dates[0]), new Date(dates[1])];
                        startInput.value = instance.formatDate(dates[0], "Y-m-d");
                        endInput.value = instance.formatDate(dates[1], "Y-m-d");
                        startInput.dispatchEvent(new Event('input', { bubbles: true }));
                        endInput.dispatchEvent(new Event('input', { bubbles: true }));
                    };

                    if (skipNextChange) {
                        skipNextChange = false;
                        if (selectedDates.length === 2) applyRange(selectedDates);
                        return;
                    }

                    if (lastRange.length === 2 && selectedDates.length === 1) {
                        const clicked = selectedDates[0].getTime();
                        const distToStart = Math.abs(clicked - lastRange[0].getTime());
                        const distToEnd = Math.abs(clicked - lastRange[1].getTime());
                        let newStart, newEnd;
                        if (distToStart <= distToEnd) { newStart = selectedDates[0]; newEnd = new Date(lastRange[1]); } 
                        else { newStart = new Date(lastRange[0]); newEnd = selectedDates[0]; }
                        if (newStart > newEnd) [newStart, newEnd] = [newEnd, newStart];

                        skipNextChange = true;
                        instance.setDate([newStart, newEnd], true);
                        return;
                    }

                    if (selectedDates.length === 2) applyRange(selectedDates);
                };

                flatpickr(pickerInput, options);
            });
        });
    </script>
@endpush
