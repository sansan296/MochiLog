<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $q = AuditLog::query()->with('user')
            ->latest()
            ->when($request->filled('action'), fn($qq) => $qq->where('action', $request->action))
            ->when($request->filled('target_type'), fn($qq) => $qq->where('target_type', $request->target_type))
            ->when($request->filled('target_id'), fn($qq) => $qq->where('target_id', $request->target_id));

        $logs = $q->paginate(20);

        return view('audit_logs.index', compact('logs'));
    }
}
