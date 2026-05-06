<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        session(['user_logged_in' => true]);
        return redirect()->route('dashboard');
    }

    public function logout()
    {
        session()->forget('user_logged_in');
        return redirect()->route('login');
    }
}
