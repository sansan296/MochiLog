@props(['itemId' => null, 'mode' => 'view']) {{-- view|create|edit --}}

<div
  x-data="tagBar({ itemId: {{ $itemId ? (int)$itemId : 'null' }}, mode: '{{ $mode }}' })"
  x-init="init()"
  class="flex flex-wrap items-center gap-2"
>
  <!-- タグ一覧 -->
  <template x-for="t in tags" :key="t.id">
    <button
      class="px-3 py-1 rounded-full border text-sm"
      :class="{
        'bg-gray-100 text-gray-700 cursor-default': mode === 'view',
        'bg-indigo-50 hover:bg-indigo-100': mode !== 'view'
      }"
      x-text="t.name"
      {{-- 👇 閲覧モードでは右クリック無効 --}}
      @contextmenu.prevent="if (mode === 'edit') openTagContextMenu($event, t)"
    ></button>
  </template>

  {{-- 👇 createモードのときだけ＋ボタン表示 --}}
  <button
    x-show="mode === 'create'"
    class="px-3 py-1 rounded-full border text-sm bg-indigo-600 text-white hover:bg-indigo-700"
    @click="openCreateModal()"
  >＋</button>

  <!-- 以下、右クリックメニュー・モーダル・編集パネルは mode に応じて制御 -->
  <div
    x-show="contextMenu.show && mode === 'edit'"
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

  <!-- 作成モーダル（createモード専用） -->
  <div
    x-show="createModal && mode === 'create'"
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
</div>

@once
  @push('scripts')
  <script>
  function tagBar({ itemId = null, mode = 'view' }) {
    return {
      itemId,
      mode,
      tags: [],
      createModal: false,
      newTagName: '',
      error: '',
      contextMenu: { show:false, x:0, y:0, target:null },
      async init() {
        await this.fetchTags();
      },
      async fetchTags() {
        const url = this.itemId
          ? `{{ url('/items') }}/${this.itemId}/tags`
          : `{{ route('tags.index') }}`;
        const res = await fetch(url);
        this.tags = await res.json();
      },
      openCreateModal() {
        if (this.mode !== 'create') return;
        this.newTagName = '';
        this.error = '';
        this.createModal = true;
      },
      async createTag() {
        if (this.mode !== 'create') return;
        try {
          const res = await fetch(`{{ route('tags.store') }}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
              name: this.newTagName.trim(),
              item_id: this.itemId,
            })
          });
          if (!res.ok) {
            const e = await res.json();
            this.error = e.message ?? '作成に失敗しました';
            return;
          }
          this.createModal = false;
          await this.fetchTags();
        } catch (e) {
          this.error = '通信エラー';
        }
      },
      openTagContextMenu(ev, tag) {
        if (this.mode !== 'edit') return;
        this.contextMenu = { show:true, x:ev.pageX, y:ev.pageY, target:tag };
      },
      async confirmDeleteTag() {
        if (this.mode !== 'edit' || !this.contextMenu.target) return;
        const ok = confirm(`「${this.contextMenu.target.name}」を削除しますか？`);
        if (!ok) return;
        await fetch(`{{ url('/tags') }}/${this.contextMenu.target.id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        this.contextMenu.show = false;
        await this.fetchTags();
      },

      async openEditPanel() {
        if (this.mode !== 'edit' || !this.contextMenu.target) return;

        const newName = prompt("新しいタグ名を入力してください", this.contextMenu.target.name);
        if (!newName || newName.trim() === this.contextMenu.target.name) {
          this.contextMenu.show = false;
          return;
        }

        try {
          const response = await fetch(`{{ url('/tags') }}/${this.contextMenu.target.id}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: newName.trim() })
          });

          if (!response.ok) throw new Error("更新に失敗しました");

          this.contextMenu.show = false;
          await this.fetchTags();
        } catch (e) {
          alert(e.message || '通信エラーが発生しました');
        }
      },

      async toggleItemTag() { /* 省略 */ },
    }
  }
  </script>
  @endpush
@endonce
