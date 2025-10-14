<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'action'      => 'nullable|in:created,updated,deleted',
            'target_type' => 'nullable|string|max:255',
            'target_id'   => 'nullable|integer',
            'from'        => 'nullable|date',
            'to'          => 'nullable|date|after_or_equal:from',
        ]);

        $q = AuditLog::with('user')->latest()
            ->when($validated['action'] ?? null, fn($qq, $v) => $qq->where('action', $v))
            ->when($validated['target_type'] ?? null, fn($qq, $v) => $qq->where('target_type', $v))
            ->when($validated['target_id'] ?? null, fn($qq, $v) => $qq->where('target_id', $v))
            ->when(($validated['from'] ?? null) && ($validated['to'] ?? null),
                fn($qq) => $qq->whereBetween('created_at', [$validated['from'], $validated['to']])
            );

        $logs = $q->paginate(20)->withQueryString();

        return view('audit_logs.index', compact('logs'));
    }
}
