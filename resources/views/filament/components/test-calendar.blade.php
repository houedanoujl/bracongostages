{{-- Calendrier dynamique pour la convocation au test --}}
{{-- Chargé dynamiquement quand une date est sélectionnée --}}
@props(['dateTest' => null, 'heureTest' => null, 'lieuTest' => null])

<div
    x-data="{
        selectedDate: @js($dateTest),
        heureTest: @js($heureTest ?? '09:00'),
        lieuTest: @js($lieuTest ?? ''),
        currentMonth: null,
        currentYear: null,
        days: [],
        weekDays: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
        months: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],

        init() {
            if (this.selectedDate) {
                const d = new Date(this.selectedDate);
                this.currentMonth = d.getMonth();
                this.currentYear = d.getFullYear();
            } else {
                const now = new Date();
                this.currentMonth = now.getMonth();
                this.currentYear = now.getFullYear();
            }
            this.buildCalendar();

            // Écouter les changements de date depuis Filament
            this.$watch('selectedDate', () => {
                if (this.selectedDate) {
                    const d = new Date(this.selectedDate);
                    this.currentMonth = d.getMonth();
                    this.currentYear = d.getFullYear();
                    this.buildCalendar();
                }
            });
        },

        buildCalendar() {
            this.days = [];
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);

            // Jour de la semaine du 1er (0=dim, on veut 0=lun)
            let startDay = firstDay.getDay() - 1;
            if (startDay < 0) startDay = 6;

            // Jours vides avant le 1er
            for (let i = 0; i < startDay; i++) {
                this.days.push({ day: '', date: null, isToday: false, isSelected: false, isPast: false });
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let d = 1; d <= lastDay.getDate(); d++) {
                const date = new Date(this.currentYear, this.currentMonth, d);
                const dateStr = date.toISOString().split('T')[0];
                this.days.push({
                    day: d,
                    date: dateStr,
                    isToday: date.getTime() === today.getTime(),
                    isSelected: dateStr === this.selectedDate,
                    isPast: date < today,
                });
            }
        },

        prevMonth() {
            this.currentMonth--;
            if (this.currentMonth < 0) { this.currentMonth = 11; this.currentYear--; }
            this.buildCalendar();
        },

        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 11) { this.currentMonth = 0; this.currentYear++; }
            this.buildCalendar();
        },

        formatSelectedDate() {
            if (!this.selectedDate) return null;
            const d = new Date(this.selectedDate);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return d.toLocaleDateString('fr-FR', options);
        },

        getDaysUntil() {
            if (!this.selectedDate) return null;
            const d = new Date(this.selectedDate);
            const now = new Date();
            now.setHours(0,0,0,0);
            d.setHours(0,0,0,0);
            const diff = Math.ceil((d - now) / (1000 * 60 * 60 * 24));
            if (diff === 0) return `Aujourd'hui`;
            if (diff === 1) return 'Demain';
            if (diff < 0) return `Date passée (${Math.abs(diff)} jours)`;
            return `Dans ${diff} jours`;
        }
    }"
    class="mt-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden"
>
    {{-- Header avec date sélectionnée --}}
    <div class="px-4 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs font-medium opacity-80">📅 Date de convocation</div>
                <template x-if="selectedDate">
                    <div>
                        <div class="text-lg font-bold capitalize" x-text="formatSelectedDate()"></div>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-sm opacity-90" x-show="heureTest">
                                🕐 <span x-text="heureTest"></span>
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                  :class="getDaysUntil() === 'Date passée' ? 'bg-red-100 text-red-800' : 'bg-white/20 text-white'"
                                  x-text="getDaysUntil()">
                            </span>
                        </div>
                    </div>
                </template>
                <template x-if="!selectedDate">
                    <div class="text-sm opacity-80 italic">Aucune date sélectionnée</div>
                </template>
            </div>
            <div class="text-3xl">📋</div>
        </div>
    </div>

    {{-- Lieu --}}
    <template x-if="lieuTest">
        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-start gap-2">
                <span class="text-gray-400 mt-0.5">📍</span>
                <div class="text-xs text-gray-600 dark:text-gray-300" x-text="lieuTest"></div>
            </div>
        </div>
    </template>

    {{-- Navigation mois --}}
    <div class="flex items-center justify-between px-4 py-2 border-b border-gray-100 dark:border-gray-700">
        <button @click="prevMonth()" type="button"
                class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 capitalize"
              x-text="months[currentMonth] + ' ' + currentYear"></span>
        <button @click="nextMonth()" type="button"
                class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    {{-- Grille du calendrier --}}
    <div class="p-3">
        {{-- Jours de la semaine --}}
        <div class="grid grid-cols-7 gap-1 mb-1">
            <template x-for="wd in weekDays" :key="wd">
                <div class="text-center text-[10px] font-semibold text-gray-400 uppercase py-1" x-text="wd"></div>
            </template>
        </div>

        {{-- Jours du mois --}}
        <div class="grid grid-cols-7 gap-1">
            <template x-for="(d, idx) in days" :key="idx">
                <div class="relative aspect-square flex items-center justify-center rounded-lg text-xs transition-all duration-200"
                     :class="{
                         'bg-primary-500 text-white font-bold shadow-md ring-2 ring-primary-300 scale-110': d.isSelected,
                         'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold ring-1 ring-blue-300': d.isToday && !d.isSelected,
                         'text-gray-300 dark:text-gray-600': d.isPast && !d.isSelected,
                         'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': !d.isSelected && !d.isToday && !d.isPast && d.day,
                         '': !d.day
                     }">
                    <span x-text="d.day"></span>
                    {{-- Indicateur jour sélectionné --}}
                    <template x-if="d.isSelected">
                        <span class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-white"></span>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
