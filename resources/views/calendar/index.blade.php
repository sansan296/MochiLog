<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-semibold text-center text-gray-800">
      📅 入出庫スケジュール管理
    </h2>
  </x-slot>

  <div class="flex flex-col md:flex-row gap-6 max-w-7xl mx-auto px-4 py-6"
       x-data="calendarApp()">

    {{-- 🗓️ カレンダー --}}
    <div class="flex-1 bg-white rounded-xl shadow p-4">
      <div id="calendar"></div>
    </div>

    {{-- 📋 今日の予定 --}}
    <div class="w-full md:w-80 bg-gray-50 rounded-xl shadow p-4">
      <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">📋 今日の予定</h3>

      @if($todayEvents->isEmpty())
        <p class="text-gray-500 text-sm">本日の予定はありません。</p>
      @else
        <ul class="space-y-2">
          @foreach($todayEvents as $event)
            <li class="bg-white rounded-lg shadow p-3 border-l-4 
                       {{ $event->type === '入庫' ? 'border-green-500' : 'border-blue-500' }}">
              <div class="flex justify-between items-center">
                <span class="font-semibold text-gray-800 text-sm">
                  {{ $event->type }}：
                  {{ $event->item->item ?? $event->item_name ?? '商品未指定' }}
                </span>
                <span class="text-xs text-gray-500">{{ $event->quantity }} 個</span>
              </div>
              <p class="text-xs text-gray-500 mt-1">{{ $event->notes ?? '（メモなし）' }}</p>

              @if($event->status !== '完了')
                <form method="POST" action="{{ route('calendar.complete', $event) }}" class="mt-2">
                  @csrf
                  <button
                    type="submit"
                    class="text-xs px-3 py-1 rounded bg-green-500 text-white hover:bg-green-600 transition">
                    ✅ 完了
                  </button>
                </form>
              @else
                <p class="text-xs text-green-600 font-semibold mt-1">完了済み</p>
              @endif
            </li>
          @endforeach
        </ul>
      @endif
    </div>

    {{-- 💬 モーダル（出庫時に同名在庫が複数ある場合） --}}
    <div x-show="showModal" 
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
         x-transition>
      <div class="bg-white rounded-2xl shadow-xl p-6 w-96">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">出庫する在庫を選択</h3>
        <template x-for="item in duplicateItems" :key="item.id">
          <button 
            class="w-full text-left px-4 py-2 mb-2 rounded hover:bg-blue-100 border"
            @click="confirmItemSelection(item.id)">
            <span x-text="item.item"></span>（在庫数：<span x-text="item.quantity"></span>）
          </button>
        </template>
        <button class="mt-4 text-sm text-gray-600 hover:text-gray-800" @click="showModal=false">キャンセル</button>
      </div>
    </div>

  </div>

  {{-- FullCalendar --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>

  <script>
    function calendarApp() {
      return {
        showModal: false,
        duplicateItems: [],
        pendingEvent: null,

        async init() {
          const calendarEl = document.getElementById('calendar');
          const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ja',
            height: 'auto',
            selectable: true,
            editable: true,
            eventSources: [{ url: '{{ route('calendar.fetch') }}', method: 'GET' }],

            select: async (info) => {
              const type = prompt('「入庫」または「出庫」を入力してください:');
              if (!type || !['入庫', '出庫'].includes(type.trim())) return;
              const quantity = parseInt(prompt('数量を入力:'), 10);
              if (isNaN(quantity) || quantity <= 0) return;

              let itemName = '';
              if (type === '入庫') {
                itemName = prompt('商品名を入力してください（例：トマト缶）:');
              } else {
                itemName = prompt('出庫する商品名を入力してください（在庫名と一致させてください）:');
              }

              const date = info.startStr;
              this.pendingEvent = { type, date, quantity, item_name: itemName };

              const res = await fetch('{{ route('calendar.store') }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.pendingEvent)
              });

              const data = await res.json();

              if (data.success) {
                alert('予定を追加しました！');
                calendar.refetchEvents();
              } else if (data.error && data.options) {
                // 同名在庫が複数ある → モーダル表示
                this.duplicateItems = Object.keys(data.options).map((name, i) => ({
                  id: data.options[name],
                  item: name,
                  quantity: data.options[name].quantity
                }));
                this.showModal = true;
              } else {
                alert(data.error || '登録エラーが発生しました。');
              }
            },

            eventClick: async (info) => {
              if (confirm('この予定を削除しますか？')) {
                await fetch(`{{ url('/calendar/events') }}/${info.event.id}`, {
                  method: 'DELETE',
                  headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                calendar.refetchEvents();
              }
            }
          });
          calendar.render();
        },

        async confirmItemSelection(selectedId) {
          this.pendingEvent.item_id = selectedId;
          this.showModal = false;

          const res = await fetch('{{ route('calendar.store') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(this.pendingEvent)
          });

          const data = await res.json();
          if (data.success) {
            alert('出庫予定を登録しました！');
            location.reload();
          } else {
            alert('登録に失敗しました。');
          }
        }
      };
    }
  </script>
</x-app-layout>
