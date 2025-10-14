@props([
  // 表示対象のアイテムID（詳細ページ用）
  'itemId' => null,
])

<div
  x-data="tagBar({ itemId: {{ $itemId ? (int)$itemId : 'null' }} })"
  x-init="init()"
  class="w-full flex items-center gap-2 flex-wrap"
>
  <!-- タグ一覧（チップ表示） -->
  <template x-for="t in tags" :key="t.id">
    <button
      class="px-3 py-1 rounded-full border text-sm bg-gray-100 hover:bg-gray-200 relative"
      x-text="t.name"
      @contextmenu.prevent="openTagContextMenu($event, t)"
    ></button>
  </template>

  <!-- 右端の＋ボタン -->
  <button
    class="px-3 py-1 rounded-full border text-sm bg-indigo-600 text-white hover:bg-indigo-700"
    @click="openCreateModal()"
  >＋</button>

  <!-- 右クリック用メニュー -->
  <div
    x-show="contextMenu.show"
    x-transition
    @click.outside="contextMenu.show=false"
    class="absolute z-50 bg-white border shadow rounded-md text-sm"
    :style="`top:${contextMenu.y}px;left:${contextMenu.x}px`"
  >
    <template x-if="contextMenu.target">
      <div class="py-1">
        <button
          class="block w-full text-left px-4 py-2 hover:bg-gray-100"
          @click="openEditPanel()"
        >タグを編集</button>
        <button
          class="block w-full text-left px-4 py-2 hover:bg-red-50 text-red-600"
          @click="confirmDeleteTag()"
        >削除</button>
      </div>
    </template>
  </div>

  <!-- 作成モーダル -->
  <div
    x-show="createModal"
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
  >
    <div class="bg-white rounded-xl p-6 w-80">
      <h3 class="font-semibold mb-3">新しいタグ</h3>
      <input type="text" x-model="newTagName"
        class="w-full border rounded px-3 py-2" placeholder="例）ネギ">
      <div class="mt-4 flex justify-end gap-2">
        <button class="px-3 py-2" @click="createModal=false">キャンセル</button>
        <button class="px-3 py-2 bg-indigo-600 text-white rounded"
          @click="createTag()">作成</button>
      </div>
      <p x-show="error" class="text-sm text-red-600 mt-2" x-text="error"></p>
    </div>
  </div>

  <!-- 右側スライドパネル（タグを編集：縦一覧＋チェックボックス） -->
  <div
    x-show="editPanel"
    x-transition
    class="fixed inset-y-0 right-0 w-80 bg-white border-l z-40 p-4 overflow-y-auto"
  >
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">タグを編集</h3>
      <button class="text-gray-500" @click="editPanel=false">✕</button>
    </div>

    <template x-if="itemId">
      <p class="text-xs text-gray-500 mb-2">アイテムID: <span x-text="itemId"></span></p>
    </template>

    <template x-for="row in editList" :key="row.id">
      <label class="flex items-center justify-between py-2 border-b">
        <span x-text="row.name"></span>
        <input type="checkbox" class="w-4 h-4"
          :checked="row.checked"
          @change="toggleItemTag(row.id, $event.target.checked)">
      </label>
    </template>

    <p x-show="!itemId" class="text-sm text-gray-500 mt-4">
      ※チェック操作はアイテム詳細画面（itemIdあり）で有効です。
    </p>
  </div>
</div>

@once
  @push('scripts')
  <script>
  function tagBar({ itemId = null }) {
    return {
      itemId,
      tags: [],
      createModal: false,
      newTagName: '',
      error: '',
      contextMenu: { show:false, x:0, y:0, target:null },
      editPanel: false,
      editList: [],
      async init() {
        await this.fetchTags();
        // 画面どこかクリックでメニュー閉じ
        window.addEventListener('click', () => { this.contextMenu.show = false; });
      },
      async fetchTags() {
        const res = await fetch(`{{ route('tags.index') }}`);
        this.tags = await res.json();
      },
      openCreateModal() {
        this.newTagName = '';
        this.error = '';
        this.createModal = true;
      },
      async createTag() {
        try {
          this.error = '';
          const res = await fetch(`{{ route('tags.store') }}`, {
            method: 'POST',
            headers: {
              'Content-Type':'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ name: this.newTagName.trim() })
          });
          if (!res.ok) {
            const e = await res.json();
            this.error = e.message ?? '作成に失敗しました';
            return;
          }
          this.createModal = false;
          await this.fetchTags();
          // 編集パネルを開いているなら更新
          if (this.editPanel && this.itemId) this.openEditPanel(true);
        } catch (e) {
          this.error = '通信エラー';
        }
      },
      openTagContextMenu(ev, tag) {
        this.contextMenu = { show:true, x:ev.pageX, y:ev.pageY, target:tag };
      },
      async confirmDeleteTag() {
        if (!this.contextMenu.target) return;
        const ok = confirm(`「${this.contextMenu.target.name}」を本当に削除しますか？`);
        if (!ok) return;
        await fetch(`{{ url('/tags') }}/${this.contextMenu.target.id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        this.contextMenu.show = false;
        await this.fetchTags();
        if (this.editPanel && this.itemId) this.openEditPanel(true);
      },
      async openEditPanel(keepOpen = false) {
        // itemIdがある画面なら、チェック状態付きで取得
        if (this.itemId) {
          const res = await fetch(`{{ url('/items') }}/${this.itemId}/tags`);
          this.editList = await res.json();
        } else {
          // 一覧画面など itemId が無い場合はチェック無しの一覧
          this.editList = this.tags.map(t => ({id:t.id,name:t.name,checked:false}));
        }
        this.editPanel = true;
        if (!keepOpen) this.contextMenu.show = false;
      },
      async toggleItemTag(tagId, checked) {
        if (!this.itemId) return; // safety
        await fetch(`{{ url('/items') }}/${this.itemId}/tags/toggle`, {
          method: 'POST',
          headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          body: JSON.stringify({ tag_id: tagId, checked: !!checked })
        });
        // チップ反映のため再取得
        await this.fetchTags(); // 全体タグは変わらないが、将来に備え再読込
      },
    }
  }
  </script>
  @endpush
@endonce
