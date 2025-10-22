<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.password.gate.show')
                ->with('error', '共通パスワードを入力してください。');
        }

        return $next($request);
    }
}
