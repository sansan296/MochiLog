<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Item extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     */
    protected $fillable = [
        'item',
        'quantity',
        'expiration_date',
        'user_id',
    ];

    /**
     * 日付型として扱うカラム
     */
    protected $dates = [
        'expiration_date',
    ];

    /**
     * Cast設定（必要なら）
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * ユーザーとのリレーション
     * 1つの商品は1人のユーザーに属する
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * タグとのリレーション
     * 1つの商品は複数のタグを持つ
     */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * メモとのリレーション（存在する場合）
     * 1つの商品に複数のメモが紐付く
     */
    public function memos()
    {
        return $this->hasMany(Memo::class);
    }

    /**
     * 賞味期限をフォーマットして返すアクセサ
     * 例: 2025-10-16 → 2025/10/16
     */
    public function getFormattedExpirationDateAttribute()
    {
        return $this->expiration_date
            ? Carbon::parse($this->expiration_date)->format('Y/m/d')
            : 'なし';
    }

    /**
     * 賞味期限が切れているかどうかを判定するアクセサ
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiration_date) {
            return false;
        }

        return Carbon::parse($this->expiration_date)->isPast();
    }
}
