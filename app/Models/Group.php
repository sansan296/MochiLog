<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'mode',
    ];

    /**
     * 作成者（ユーザー）とのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * グループに属するユーザーたち（中間テーブル）
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * グループに属するアイテム
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * グループに属するタグ
     */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * グループに属する食材
     */
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
