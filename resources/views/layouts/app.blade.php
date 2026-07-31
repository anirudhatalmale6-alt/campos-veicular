<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Campos Veicular') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800">
<div x-data="{ open: false }" class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside :class="open ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-slate-200 transform transition-transform duration-200 md:translate-x-0 md:static md:inset-auto flex flex-col">
        <div class="h-16 flex items-center gap-2 px-5 border-b border-slate-700/60">
            <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center font-bold text-white">C</div>
            <div class="leading-tight">
                <div class="font-semibold text-white text-sm">Campos Veicular</div>
                <div class="text-[11px] text-slate-400">Sistema Integrado</div>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
            @php
                $nav = fn($active) => request()->routeIs($active)
                    ? 'flex items-center gap-3 px-3 py-2 rounded-lg bg-emerald-600 text-white'
                    : 'flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-300';
            @endphp
            <a href="{{ route('dashboard') }}" class="{{ $nav('dashboard') }}">📊 <span>Painel</span></a>

            @can('analyses.view')
                <a href="{{ route('analyses.index') }}" class="{{ $nav('analyses.*') }}">🔎 <span>Análises &amp; Aquisição</span></a>
            @endcan
            @can('clients.view')
                <a href="{{ route('clients.index') }}" class="{{ $nav('clients.*') }}">👤 <span>Clientes</span></a>
            @endcan
            @can('banks.view')
                <a href="{{ route('banks.index') }}" class="{{ $nav('banks.*') }}">🏦 <span>Bancos &amp; Financeiras</span></a>
            @endcan

            <div class="pt-3 mt-3 border-t border-slate-700/60 text-[11px] uppercase tracking-wider text-slate-500 px-3">Administração</div>
            @can('users.manage')
                <a href="{{ route('users.index') }}" class="{{ $nav('users.*') }}">🛡️ <span>Usuários &amp; Acessos</span></a>
            @endcan
            @can('audit.view')
                <a href="{{ route('audit.index') }}" class="{{ $nav('audit.*') }}">📝 <span>Log de Auditoria</span></a>
            @endcan
        </nav>
        <div class="p-3 border-t border-slate-700/60 text-xs text-slate-400">
            <div class="px-2 py-1">{{ auth()->user()->name }}</div>
            <div class="px-2 pb-2 text-emerald-400">{{ auth()->user()->roleLabel() }}</div>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div x-show="open" @click="open=false" class="fixed inset-0 bg-black/40 z-20 md:hidden" x-cloak></div>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6 sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <button @click="open=!open" class="md:hidden text-slate-600 text-xl">☰</button>
                <h1 class="font-semibold text-slate-700">@yield('title', 'Painel')</h1>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('profile.edit') }}" class="text-sm text-slate-500 hover:text-slate-800">Meu perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-red-600 hover:text-red-800">Sair</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-6">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
