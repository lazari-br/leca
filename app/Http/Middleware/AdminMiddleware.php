<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar se está autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Verificar se é administrador (não é vendedor)
        $user = auth()->user();
        if (!$user->type || $user->type->name !== 'admin') {
            return redirect()->route('admin.index')->with('error', 'Acesso negado. Área restrita para administradores.');
        }

        return $next($request);
    }
}
