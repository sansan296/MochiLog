<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * タグ一覧を取得（JSON）
     * /tags にアクセスしたとき、全タグを返す
     */
    public function index()
    {
        $tags = Tag::orderBy('id', 'asc')->get();
        return response()->json($tags);
    }

    /**
     * 新しいタグを作成（＋ボタン）
     * 同名タグが既に存在する場合はエラーを返す
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        $tag = Tag::create(['name' => $validated['name']]);

        return response()->json($tag, 201);
    }

    /**
     * タグを削除（右クリックなどで使う想定）
     * 関連する item_tag も自動的に削除される（DB制約により）
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'message' => "タグ「{$tag->name}」を削除しました。"
        ]);
    }
}
