<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-semibold text-center text-gray-800">
      📅 入出庫スケジュール管理
    </h2>
  </x-slot>

  <div x-data="calendarApp()" x-init="init()" class="flex flex-col md:flex-row gap-6 max-w-7xl mx-auto px-4 py-6">

    {{-- 🗓️ カレンダー --}}
    <div class="flex-1 bg-white rounded-xl shadow p-4">
      <div id="calendar"></div>
    </div>

    {{-- 📋 選択中の日の予定 --}}
    <div class="w-full md:w-80 bg-gray-50 rounded-xl shadow p-4">
      <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
        📋 <span x-text="selectedLabel"></span>
      </h3>

      <template x-if="events.length === 0">
        <p class="text-gray-500 text-sm">この日の予定はありません。</p>
      </template>

      <ul class="space-y-2" x-show="events.length > 0">
        <template x-for="event in events" :key="event.id">
          <li class="bg-white rounded-lg shadow p-3 border-l-4"
              :class="event.type === '入庫' ? 'border-green-500' : 'border-blue-500'">
            <div class="flex justify-between items-center">
              <span class="font-semibold text-gray-800 text-sm" x-text="`${event.type}：${event.name}`"></span>
              <span class="text-xs text-gray-500" x-text="`${event.quantity} 個`"></span>
            </div>
            <p class="text-xs text-gray-500 mt-1" x-text="event.notes || '（メモなし）'"></p>
            <p class="text-xs mt-1 font-semibold" :class="event.status === '完了' ? 'text-green-600' : 'text-gray-500'" x-text="event.status"></p>
          </li>
        </template>
      </ul>
    </div>
  </div>

  {{-- FullCalendar --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>

  <script>
    function calendarApp() {
      return {
        selectedDate: new Date().toISOString().slice(0,10),
        selectedLabel: '今日の予定',
        events: [],

        async fetchEvents(date) {
          const res = await fetch(`{{ route('calendar.byDate') }}?date=${date}`);
          this.events = res.ok ? await res.json() : [];
        },

        async init() {
          // 今日の予定を初期表示
          await this.fetchEvents(this.selectedDate);

          const calendarEl = document.getElementById('calendar');
          const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ja',
            height: 'auto',
            selectable: true,
            eventSources: [
              { url: '{{ route('calendar.fetch') }}', method: 'GET' }
            ],
            dateClick: async (info) => {
              this.selectedDate = info.dateStr;
              const today = new Date().toISOString().slice(0,10);
              this.selectedLabel = (this.selectedDate === today) ? '今日の予定' : `${this.selectedDate} の予定`;
              await this.fetchEvents(this.selectedDate);
            }
          });
          calendar.render();
        }
      };
    }
  </script>
</x-app-layout>
