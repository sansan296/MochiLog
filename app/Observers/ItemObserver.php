<?php

// app/Observers/ItemObserver.php
namespace App\Observers;

use App\Models\Item;
use App\Models\AuditLog;

class ItemObserver
{
    public function created(Item $item): void
    {
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'created',
            'target_type' => Item::class,
            'target_id'   => $item->id,
            'changes'     => ['after' => $item->getAttributes()],
            'ip'          => request()?->ip(),
        ]);
    }

    public function updated(Item $item): void
    {
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'updated',
            'target_type' => Item::class,
            'target_id'   => $item->id,
            'changes'     => [
                'before' => $item->getOriginal(),
                'after'  => $item->getAttributes(),
            ],
            'ip'          => request()?->ip(),
        ]);
    }

    public function deleted(Item $item): void
    {
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'deleted',
            'target_type' => Item::class,
            'target_id'   => $item->id,
            'changes'     => ['before' => $item->getOriginal()],
            'ip'          => request()?->ip(),
        ]);
    }
}
