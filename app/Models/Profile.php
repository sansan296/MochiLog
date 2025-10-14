<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'gender',
        'age',
        'occupation',
        'contact_email',
        'phone',
        'company_name',
        'position',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
