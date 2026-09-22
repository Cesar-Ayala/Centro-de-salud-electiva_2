<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a2540">
    <title>Iniciar sesión · {{ config('app.name') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @include('partials.theme')
</head>
<body class="min-h-screen">

<div class="min-h-screen grid lg:grid-cols-2">

    {{-- Panel institucional --}}
    <div class="hidden lg:flex flex-col justify-between p-10 text-white relative overflow-hidden"
         style="background: linear-gradient(150deg, #0a2540 0%, #0f3f4d 55%, #0f766e 100%);">

        {{-- Cruz médica decorativa --}}
        <svg class="absolute -right-16 -bottom-20 opacity-[.07]" width="420" height="420" viewBox="0 0 100 100" fill="#fff">
            <path d="M40 5h20v35h35v20H60v35H40V60H5V40h35z"/>
        </svg>

        <div class="relative">
            <div class="flex items-center gap-3">
                <span class="icon-chip" style="background: rgba(255,255,255,.14); color:#6ee7cd">
                    <x-icon name="activity" />
                </span>
                <div>
                    <p class="font-semibold text-lg leading-tight">Centro de Salud</p>
                    <p class="text-[.7rem] tracking-[.16em] text-teal-200/80">GESTIÓN ADMINISTRATIVA</p>
                </div>
            </div>
        </div>

        <div class="relative max-w-md">
            <h2 class="text-3xl font-semibold leading-tight">
                Toda la operación del centro, en una sola pantalla.
            </h2>
            <p class="text-slate-300 mt-3 text-sm leading-relaxed">
                Médicos, personal, pacientes, horarios de consulta, sustituciones y vacaciones
                gestionados desde un sistema único, con reportes listos para presentar.
            </p>

            <ul class="mt-8 space-y-3 text-sm">
                @foreach ([
                    ['stethoscope', 'Registro clínico y administrativo del personal'],
                    ['calendar', 'Cuadro semanal de consulta por médico'],
                    ['repeat', 'Control de sustituciones y ausencias'],
                    ['chart', 'Reportes exportables a CSV'],
                ] as [$icono, $texto])
                    <li class="flex items-center gap-3">
                        <span class="icon-chip" style="width:32px;height:32px;border-radius:9px;background: rgba(255,255,255,.12); color:#a7f3e0">
                            <x-icon :name="$icono" class="w-4 h-4" />
                        </span>
                        <span class="text-slate-200">{{ $texto }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="relative flex items-center justify-between text-[.7rem] text-slate-400">
            <span>UAJS · Electiva Profesional II</span>
            <a href="{{ route('home') }}" class="text-teal-200 hover:text-white">Conocer el centro →</a>
        </div>
    </div>

    {{-- Formulario --}}
    <div class="flex items-center justify-center p-6 bg-slate-50">
        <div class="w-full max-w-sm fade-in">

            <div class="lg:hidden text-center mb-6">
                <span class="icon-chip chip-brand mx-auto mb-2"><x-icon name="activity" /></span>
                <h1 class="text-xl font-semibold text-slate-900">Centro de Salud</h1>
                <p class="text-xs text-slate-500">Sistema de gestión administrativa</p>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="text-lg font-semibold text-slate-900">Iniciar sesión</h2>
                    <p class="text-xs text-slate-500 mb-5">Ingrese con las credenciales asignadas por la administración.</p>

                    @if ($errors->any())
                        <div class="alert alert-bad mb-4">
                            <x-icon name="warning" />
                            <p class="flex-1">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="field-label">Correo electrónico</label>
                            <div class="input-icon">
                                <x-icon name="mail" />
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                       placeholder="usuario@hospital.com" class="input">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="field-label">Contraseña</label>
                            <div class="input-icon">
                                <x-icon name="shield" />
                                <input id="password" name="password" type="password" required
                                       placeholder="••••••••" class="input">
                            </div>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                            Mantener sesión iniciada
                        </label>

                        <button type="submit" class="btn btn-primary w-full">
                            <x-icon name="logout" />Ingresar al sistema
                        </button>
                    </form>
                </div>
            </div>

            {{-- Usuarios de prueba: un clic completa el formulario --}}
            <div class="card mt-4">
                <div class="card-body">
                    <p class="section-label mb-2">Usuarios de prueba · contraseña password123</p>
                    <div class="space-y-1">
                        @foreach ([
                            ['admin@hospital.com', 'Administrador', 'shield', 'chip-brand'],
                            ['operador@hospital.com', 'Operador', 'briefcase', 'chip-ink'],
                            ['juan.perez@hospital.com', 'Médico', 'stethoscope', 'chip-info'],
                            ['roberto.diaz@hospital.com', 'Paciente', 'patient', 'chip-warn'],
                        ] as [$correo, $rol, $icono, $tono])
                            <button type="button"
                                    class="js-demo w-full flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 text-left"
                                    data-email="{{ $correo }}">
                                <span class="icon-chip {{ $tono }}" style="width:30px;height:30px;border-radius:9px">
                                    <x-icon :name="$icono" class="w-4 h-4" />
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block text-xs font-medium text-slate-800 truncate">{{ $correo }}</span>
                                    <span class="block text-[.68rem] text-slate-400">{{ $rol }}</span>
                                </span>
                                <span class="text-[.68rem] text-teal-700">Usar</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <p class="text-center text-[.7rem] text-slate-400 mt-5">
                Centro de Salud · Sistema de Información para la Gestión Administrativa
            </p>
        </div>
    </div>
</div>

<script>
    // Un clic sobre un usuario de prueba completa correo y contraseña.
    document.querySelectorAll('.js-demo').forEach(function (boton) {
        boton.addEventListener('click', function () {
            document.getElementById('email').value = boton.dataset.email;
            document.getElementById('password').value = 'password123';
            document.getElementById('password').focus();
        });
    });
</script>
</body>
</html>
