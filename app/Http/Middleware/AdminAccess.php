<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // セッションに "admin_authenticated" がなければリダイレクト
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.password.gate.show')
                             ->with('error', '共通パスワードを入力してください。');
        }

        return $next($request);
    }
}
