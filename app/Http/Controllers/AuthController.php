<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $celular = $request->input('celular');
    $nome = $request->input('nome');

    // Busca usuário pelo celular
    $user = User::where('celular', $celular)->first();

    if ($user) {
        // Login simples
        Auth::login($user);
        return redirect()->intended('/');
    }

    // Se não existe, precisa de nome
    if (!$nome) {
        return back()
            ->withInput()
            ->with('new_user', true)
            ->withErrors(['nome' => 'Informe seu nome para criar a conta']);
    }

    // Cria novo usuário
    $user = User::create([
        'name' => $nome,
        'celular' => $celular,
        'is_admin' => false
    ]);

    // Faz login
    Auth::login($user);
    return redirect()->intended('/');
}



    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
