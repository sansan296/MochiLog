<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
      {{ __('在庫一覧') }}
    </h2>
  </x-slot>

  {{-- Alpine.js 読み込み（必ず最初に！） --}}
  <script src="https://unpkg.com/alpinejs" defer></script>

  <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8"
       x-data="tagFilter()"
       x-init="init()">

    {{-- 🔍 検索フォーム --}}
    <form method="GET" action="{{ route('items.index') }}" class="mb-6 flex justify-between items-center">
      <div>
        <input type="text" name="keyword" value="{{ request('keyword') }}"
              placeholder="商品名"
              class="border rounded-lg px-3 py-2 w-64">
        <button type="submit"
                class="ml-2 px-4 py-2 bg-[#4973B5] text-white rounded-lg hover:bg-[#2C5BA5]">
         検索
        </button>
      </div>

      {{-- 🍳 在庫で作れる料理を表示ボタン --}}
      <a href="{{ route('recipes.index') }}" 
         class="px-6 py-2 bg-[#FF9A3C] text-white font-semibold rounded-lg hover:bg-[#4973B5] transition">
         在庫で作れる料理を表示
      </a>
    </form>

    {{-- 🏷 タグ追加・絞り込み --}}
    <div class="mb-8 bg-white shadow-sm rounded-lg p-4">
      <div class="flex items-center flex-wrap gap-2 mb-3">
        {{-- タグ一覧 --}}
        <template x-for="tag in tags" :key="tag.id">
          <button
            type="button"
            class="px-3 py-1 rounded-full border text-sm transition"
            :class="selectedTags.includes(tag.id)
              ? 'bg-indigo-600 text-white border-indigo-600'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            x-text="tag.name"
            @click="toggleTagFilter(tag.id)">
          </button>
        </template>

        {{-- ＋ボタン（新規タグ追加） --}}
        <button type="button"
                class="px-3 py-1 rounded-full border text-sm bg-indigo-600 text-white hover:bg-indigo-700"
                @click="openCreateModal()">＋</button>
      </div>
      <p class="text-sm text-gray-500">タグをクリックして在庫を絞り込みできます（複数選択可）</p>
    </div>

    {{-- 📦 在庫一覧 --}}
    <div class="bg-[#9cbcf0ff] overflow-hidden shadow-sm sm:rounded-lg p-6">
      <template x-if="filteredItems.length === 0">
        <p class="text-center text-gray-600">該当する在庫がありません。</p>
      </template>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="item in filteredItems" :key="item.id">
          <div class="p-4 bg-white rounded-lg shadow">
            <p class="text-lg font-semibold mb-2" x-text="item.item"></p>

            {{-- 🏷 タグ表示 --}}
            <div class="flex flex-wrap gap-1 mb-2">
              <template x-for="t in item.tags" :key="t.id">
                <span class="px-2 py-1 text-xs bg-gray-100 border rounded-full" x-text="t.name"></span>
              </template>
            </div>

            <p class="text-gray-800 text-base mt-2">
              賞味期限：
              <template x-if="item.expiration_date">
                <span
                  x-text="formatExpiration(item.expiration_date)"
                  :class="isExpired(item.expiration_date) ? 'text-[#EE2E48] font-bold' : ''">
                </span>
              </template>
              <template x-if="!item.expiration_date">
                <span>なし</span>
              </template>
            </p>

            <p class="text-gray-800 text-base">個数：<span x-text="item.quantity"></span></p>
            <p class="text-gray-600 text-sm mb-2">登録者：<span x-text="item.user.name"></span></p>

            <a :href="`/items/${item.id}`" 
               class="block text-right text-[#4973B5] hover:text-[#2C5BA5] font-medium mt-2">
              詳細 →
            </a>
          </div>
        </template>
      </div>
    </div>

    {{-- 🧾 タグ作成モーダル --}}
    <div x-show="createModal"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white rounded-xl p-6 w-80">
        <h3 class="font-semibold mb-3">新しいタグを追加</h3>
        <input type="text" x-model="newTagName"
          class="w-full border rounded px-3 py-2" placeholder="例）冷凍">
        <div class="mt-4 flex justify-end gap-2">
          <button type="button" class="px-3 py-2" @click="createModal=false">キャンセル</button>
          <button type="button" class="px-3 py-2 bg-indigo-600 text-white rounded"
            @click="createTag()">作成</button>
        </div>
        <p x-show="error" class="text-sm text-red-600 mt-2" x-text="error"></p>
      </div>
    </div>

  </div>

  {{-- ✅ Alpine.jsロジック --}}
  @push('scripts')
  <script>
  function tagFilter() {
    return {
      tags: [],
      items: [],
      filteredItems: [],
      selectedTags: [],
      createModal: false,
      newTagName: '',
      error: '',

      async init() {
        console.log('🔄 Alpine init start');
        await this.fetchTags();
        await this.fetchItems();
        console.log('✅ Alpine init complete');
      },

      // タグ一覧取得
      async fetchTags() {
        try {
          const res = await fetch(`{{ route('tags.index') }}`);
          if (!res.ok) throw new Error('タグ取得に失敗');
          this.tags = await res.json();
        } catch (e) {
          console.error('タグ取得エラー:', e);
        }
      },

      // 在庫一覧取得
      async fetchItems() {
        try {
          const res = await fetch(`{{ route('items.index') }}?json=1`, {
            headers: { 'Accept': 'application/json' }
          });
          if (!res.ok) throw new Error('在庫取得に失敗');
          this.items = await res.json();
          this.filteredItems = this.items;
        } catch (e) {
          console.error('在庫取得エラー:', e);
        }
      },

      // タグ絞り込み切替
      toggleTagFilter(tagId) {
        if (this.selectedTags.includes(tagId)) {
          this.selectedTags = this.selectedTags.filter(id => id !== tagId);
        } else {
          this.selectedTags.push(tagId);
        }
        this.applyFilter();
      },

      // 絞り込み適用
      applyFilter() {
        if (this.selectedTags.length === 0) {
          this.filteredItems = this.items;
          return;
        }
        this.filteredItems = this.items.filter(item =>
          item.tags.some(tag => this.selectedTags.includes(tag.id))
        );
      },

      // タグ作成モーダル
      openCreateModal() {
        this.newTagName = '';
        this.error = '';
        this.createModal = true;
      },

      // タグ作成
      async createTag() {
        try {
          const res = await fetch(`{{ route('tags.store') }}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json',
            },
            body: JSON.stringify({ name: this.newTagName.trim() })
          });
          if (!res.ok) {
            const e = await res.json().catch(() => ({}));
            this.error = e.message ?? '作成に失敗しました';
            return;
          }
          this.createModal = false;
          await this.fetchTags();
        } catch (e) {
          console.error('タグ作成エラー:', e);
          this.error = '通信エラー';
        }
      },

      formatExpiration(dateStr) {
        if (!dateStr) return 'なし';
        const date = new Date(dateStr);
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        const now = new Date();
        const diff = Math.ceil((date - now) / (1000 * 60 * 60 * 24));
        if (diff < 0) {
          return `${y}/${m}/${d}（期限切れ）`;
        } else {
          return `${y}/${m}/${d}（あと ${diff} 日）`;
        }
      },

      isExpired(dateStr) {
        if (!dateStr) return false;
        const date = new Date(dateStr);
        return date < new Date();
      },

     }
  }
  </script>
  @endpush
</x-app-layout>

{{-- ✅ ここに置くことでスクリプトが確実に読み込まれる --}}
@stack('scripts')
