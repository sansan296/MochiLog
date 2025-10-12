<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    protected $fillable = ['item', 'expiration_date', 'user_id', 'quantity',];

     protected $casts = [
        'expiration_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memos()
    {
        return $this->hasMany(Memo::class)->orderBy('created_at', 'desc');
    }
}
