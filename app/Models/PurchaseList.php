<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseList extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseListFactory> */
    use HasFactory;

    protected $fillable = ['item', 'quantity', 'purchase_date'];

}
