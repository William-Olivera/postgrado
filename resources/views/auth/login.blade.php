@extends('layouts.auth')

@section('title', 'Iniciar Sesion')

@section('content')
<div class="login-container">
    <div class="login-box">
        <div class="login-logo">
            <div class="logo-icon-large">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4" />
                    <path d="M6 20c0-3.314 2.686-6 6-6s6 2.686 6 6" />
                </svg>
            </div>
            <h1>F.I.N.O.R Post-grado</h1>
            <p>Sistema de gestion contable</p>
        </div>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <button type="submit" class="btn-login">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Iniciar Sesion
            </button>
        </form>
    </div>
</div>
@endsection