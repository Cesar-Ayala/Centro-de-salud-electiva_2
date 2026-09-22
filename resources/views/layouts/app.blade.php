<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a2540">
    <title>@yield('title', 'Centro de Salud') · {{ config('app.name') }}</title>

    {{-- Si se compilaron los assets con "npm run build" se usan esos;
         si no, se carga Tailwind desde CDN para que el proyecto funcione igual. --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    @include('partials.theme')
</head>
<body class="app-shell min-h-screen">

@php
    $user = auth()->user();
    $isStaff = $user->hasRole('admin', 'operator');
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $nav = $isStaff ? [
        'Principal' => [
            ['dashboard', 'Panel principal', 'dashboard', route('dashboard')],
        ],
        'Registros' => [
            ['stethoscope', 'Médicos', 'doctors.*', route('doctors.index')],
            ['briefcase', 'Empleados', 'employees.*', route('employees.index')],
            ['patient', 'Pacientes', 'patients.*', route('patients.index')],
        ],
        'Operación' => [
            ['calendar', 'Horarios', 'schedules.*', route('schedules.index')],
            ['repeat', 'Sustituciones', 'substitutions.*', route('substitutions.index')],
            ['palm', 'Vacaciones médicos', 'doctor-vacations.*', route('doctor-vacations.index')],
            ['sun', 'Vacaciones empleados', 'employee-vacations.*', route('employee-vacations.index')],
        ],
        'Análisis' => [
            ['chart', 'Reportes', 'reports.*', route('reports.index')],
            ['search', 'Búsqueda global', 'search', route('search')],
        ],
    ] : [
        'Principal' => [
            ['dashboard', 'Mi panel', 'dashboard', route('dashboard')],
        ],
    ];
@endphp

<div class="flex min-h-screen">

    {{-- Fondo oscuro del menú en móvil --}}
    <div id="navOverlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden md:hidden no-print"></div>

    {{-- Barra lateral de navegación --}}
    <aside id="sidebar"
           class="sidebar w-64 flex-col fixed inset-y-0 left-0 z-40 -translate-x-full transition-transform duration-200
                  md:translate-x-0 md:static md:flex no-print flex">

        <div class="px-5 py-5 flex items-center gap-3 border-b border-white/10">
            <span class="icon-chip" style="background: rgba(20,184,166,.18); color: #6ee7cd;">
                <x-icon name="activity" />
            </span>
            <div class="leading-tight">
                <p class="text-white font-semibold text-[.95rem]">Centro de Salud</p>
                <p class="text-[.68rem] text-slate-400 tracking-wide">GESTIÓN ADMINISTRATIVA</p>
            </div>
            <button type="button" id="navClose" class="ml-auto md:hidden text-slate-300 hover:text-white">
                <x-icon name="close" class="w-5 h-5" />
            </button>
        </div>

        <nav class="flex-1 px-3 py-3 overflow-y-auto">
            @foreach ($nav as $group => $items)
                <p class="nav-group">{{ $group }}</p>
                @foreach ($items as [$icon, $label, $pattern, $url])
                    <a href="{{ $url }}" class="nav-link {{ request()->routeIs($pattern) ? 'is-active' : '' }}">
                        <x-icon :name="$icon" />
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            @endforeach
        </nav>

        <div class="px-5 py-4 border-t border-white/10">
            <div class="flex items-center gap-2 text-[.7rem] text-slate-400">
                <span class="pulse-dot inline-block w-2 h-2 rounded-full" style="background:#34d399"></span>
                Sistema operativo
            </div>
            <p class="text-[.68rem] text-slate-500 mt-2">UAJS · Electiva Profesional II</p>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">

        {{-- Barra superior --}}
        <header class="topbar bg-white/90 backdrop-blur border-b border-slate-200 sticky top-0 z-20 no-print">
            <div class="px-4 md:px-6 py-3 flex items-center gap-3">

                <button type="button" id="navOpen" class="btn btn-ghost btn-icon md:hidden">
                    <x-icon name="menu" />
                </button>

                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-[.7rem] text-slate-400">
                        <a href="{{ route('dashboard') }}" class="hover:text-teal-700">Inicio</a>
                        @hasSection('breadcrumb')
                            <span>/</span><span class="text-slate-500">@yield('breadcrumb')</span>
                        @endif
                    </div>
                    <h1 class="text-lg md:text-xl font-semibold text-slate-900 truncate" style="letter-spacing:-.02em">
                        @yield('title', 'Panel principal')
                    </h1>
                    @hasSection('subtitle')
                        <p class="text-xs text-slate-500 truncate">@yield('subtitle')</p>
                    @endif
                </div>

                <div class="ml-auto flex items-center gap-2 md:gap-3">
                    @if ($isStaff)
                        <form method="GET" action="{{ route('search') }}" class="hidden lg:block">
                            <div class="input-icon">
                                <x-icon name="search" />
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar en el sistema…"
                                       class="input" style="width: 15rem;">
                            </div>
                        </form>
                        <a href="{{ route('search') }}" class="btn btn-ghost btn-icon lg:hidden" title="Buscar">
                            <x-icon name="search" />
                        </a>
                    @endif

                    <div class="hidden md:block text-right leading-tight">
                        <p class="text-sm font-medium text-slate-900">{{ $user->name }}</p>
                        <p class="text-[.7rem] text-slate-500">{{ $user->role_label }}</p>
                    </div>
                    <span class="avatar">{{ $initials }}</span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm" title="Cerrar sesión">
                            <x-icon name="logout" /><span class="hidden md:inline">Salir</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-4 md:p-6 flex-1">

            @hasSection('actions')
                <div class="flex flex-wrap items-center gap-2 mb-4 no-print">@yield('actions')</div>
            @endif

            @if (session('status'))
                <div class="alert alert-ok mb-4 fade-in js-alert no-print">
                    <x-icon name="check" />
                    <p class="flex-1">{{ session('status') }}</p>
                    <button type="button" class="js-alert-close text-current opacity-60 hover:opacity-100"><x-icon name="close" class="w-4 h-4" /></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-bad mb-4 fade-in no-print">
                    <x-icon name="warning" />
                    <div class="flex-1">
                        <p class="font-semibold mb-1">Revise los siguientes datos:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="px-6 py-4 text-center text-[.7rem] text-slate-400 no-print">
            Centro de Salud · Sistema de Información para la Gestión Administrativa ·
            {{ now()->format('d/m/Y') }}
        </footer>
    </div>
</div>

{{-- Ventana de confirmación para eliminaciones --}}
<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 no-print">
    <div class="card w-full max-w-sm fade-in" style="box-shadow: var(--shadow-lg)">
        <div class="card-body text-center">
            <span class="icon-chip chip-bad mx-auto mb-3"><x-icon name="warning" /></span>
            <h3 class="font-semibold text-slate-900 mb-1">Confirmar eliminación</h3>
            <p id="confirmText" class="text-sm text-slate-500">Esta acción no se puede deshacer.</p>
            <div class="flex gap-2 mt-5">
                <button type="button" id="confirmCancel" class="btn btn-ghost flex-1">Cancelar</button>
                <button type="button" id="confirmOk" class="btn flex-1" style="background:var(--bad-600);color:#fff">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Menú lateral en pantallas pequeñas
    (function () {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('navOverlay');
        const open = () => { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); };
        const close = () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); };
        document.getElementById('navOpen')?.addEventListener('click', open);
        document.getElementById('navClose')?.addEventListener('click', close);
        overlay?.addEventListener('click', close);
    })();

    // Confirmación de borrado con ventana propia
    (function () {
        const modal = document.getElementById('confirmModal');
        const text = document.getElementById('confirmText');
        let pending = null;

        document.querySelectorAll('form.js-confirm').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (form.dataset.confirmed === '1') return;
                event.preventDefault();
                pending = form;
                text.textContent = form.dataset.message || 'Esta acción no se puede deshacer.';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });

        function hide() { modal.classList.add('hidden'); modal.classList.remove('flex'); pending = null; }
        document.getElementById('confirmCancel')?.addEventListener('click', hide);
        modal?.addEventListener('click', e => { if (e.target === modal) hide(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') hide(); });
        document.getElementById('confirmOk')?.addEventListener('click', function () {
            if (!pending) return;
            pending.dataset.confirmed = '1';
            pending.submit();
        });
    })();

    // Mensajes de estado: cierre manual y automático
    (function () {
        document.querySelectorAll('.js-alert').forEach(function (alert) {
            alert.querySelector('.js-alert-close')?.addEventListener('click', () => alert.remove());
            setTimeout(() => { alert.style.transition = 'opacity .4s'; alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 400); }, 6000);
        });
    })();
</script>
@stack('scripts')
</body>
</html>
