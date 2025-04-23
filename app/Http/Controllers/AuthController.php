<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Removido o construtor com middleware que estava causando o erro
    
    public function showLoginForm()
    {
        // Se o usuário já estiver logado, redireciona para a home
        if (Auth::check()) {
            return redirect('/');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Regenera a sessão após login bem-sucedido (segurança)
            $request->session()->regenerate();
            
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        // Invalida a sessão do usuário
        $request->session()->invalidate();
        
        // Regenera o token da sessão
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}