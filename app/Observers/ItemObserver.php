<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class ItemObserver
{
    /** 作成時 */
    public function created(Item $item): void
    {
        self::log($item, 'created', [], $item->only(self::logKeys($item)));
    }

    /** 更新時（差分を記録） */
    public function updating(Item $item): void
    {
        // 変更されたキーだけを拾う（updated_at等は除外）
        $dirty = Arr::only($item->getDirty(), self::logKeys($item));
        if (empty($dirty)) return;

        $before = [];
        $after  = [];
        foreach ($dirty as $key => $newValue) {
            $before[$key] = $item->getOriginal($key);
            $after[$key]  = $newValue;
        }

        // 一時的にモデルに保存して、updated後のcreated()でなくここで記録
        $item->setAttribute('_audit_before', $before);
        $item->setAttribute('_audit_after',  $after);
    }

    public function updated(Item $item): void
    {
        $before = $item->getAttribute('_audit_before') ?? [];
        $after  = $item->getAttribute('_audit_after')  ?? [];

        if (!empty($before) || !empty($after)) {
            self::log($item, 'updated', $before, $after);
        }
    }

    /** 削除時（削除前の値を保存） */
    public function deleting(Item $item): void
    {
        $before = $item->only(self::logKeys($item));
        self::log($item, 'deleted', $before, []);
    }

    /** ログ対象のキー（モデルの fillable を優先） */
    private static function logKeys(Item $item): array
    {
        return $item->getFillable() ?: ['item', 'quantity', 'expiration_date', 'user_id'];
    }

    /** 実際の書き込み */
    private static function log(Item $item, string $action, array $before, array $after): void
    {
        $request = request(); // CLI等ではnullのこともある
        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'target_type' => get_class($item),
            'target_id'   => $item->getKey(),
            'changes'     => ['before' => $before, 'after' => $after],
            'ip'          => $request?->ip(),
            'user_agent'  => $request?->userAgent(),
        ]);
    }
}
