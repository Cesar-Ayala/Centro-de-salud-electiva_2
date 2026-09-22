<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a2540">
    <title>Centro de Salud · {{ config('app.name') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @include('partials.theme')
</head>
<body class="min-h-screen bg-white">

{{-- Barra superior --}}
<header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-3 flex items-center gap-3">
        <span class="icon-chip chip-brand"><x-icon name="activity" /></span>
        <div class="leading-tight">
            <p class="font-semibold text-slate-900">Centro de Salud</p>
            <p class="text-[.65rem] tracking-[.14em] text-slate-400">GESTIÓN ADMINISTRATIVA</p>
        </div>
        <nav class="ml-auto flex items-center gap-2">
            <a href="#servicios" class="btn btn-ghost btn-sm hidden sm:inline-flex">Servicios</a>
            <a href="#horarios" class="btn btn-ghost btn-sm hidden sm:inline-flex">Horarios</a>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">Ir al panel</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm"><x-icon name="logout" />Ingresar</a>
            @endauth
        </nav>
    </div>
</header>

{{-- Portada --}}
<section class="relative overflow-hidden text-white" style="background: linear-gradient(140deg, #0a2540 0%, #0f3f4d 55%, #0f766e 100%);">
    <svg class="absolute -right-20 -top-16 opacity-[.07]" width="460" height="460" viewBox="0 0 100 100" fill="#fff">
        <path d="M40 5h20v35h35v20H60v35H40V60H5V40h35z"/>
    </svg>
    <div class="max-w-6xl mx-auto px-5 py-16 md:py-24 relative">
        <span class="badge" style="background: rgba(255,255,255,.14); color:#a7f3e0; border-color: rgba(255,255,255,.2)">
            <span class="dot"></span>Atención primaria y consulta especializada
        </span>
        <h1 class="text-3xl md:text-5xl font-semibold mt-4 max-w-2xl leading-tight">
            Cuidamos de las personas, organizamos el centro.
        </h1>
        <p class="text-slate-300 mt-4 max-w-xl text-sm md:text-base leading-relaxed">
            Un sistema de información que reúne médicos, personal, pacientes, horarios de consulta,
            sustituciones y vacaciones para que la atención nunca se interrumpa.
        </p>
        <div class="flex flex-wrap gap-2 mt-8">
            <a href="{{ route('login') }}" class="btn btn-soft"><x-icon name="logout" />Acceder al sistema</a>
            <a href="#servicios" class="btn btn-ghost">Ver servicios</a>
        </div>
    </div>
</section>

{{-- Cifras --}}
<section class="max-w-6xl mx-auto px-5 -mt-8 relative z-10">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach ([
            ['stethoscope', 'Cuadro médico', 'Titulares, interinos y sustitutos'],
            ['calendar', 'Consulta programada', 'Franjas horarias por día'],
            ['repeat', 'Cobertura continua', 'Sustituciones sin interrupción'],
            ['shield', 'Datos protegidos', 'Acceso por roles de usuario'],
        ] as [$icono, $titulo, $texto])
            <div class="card card-hover p-4">
                <span class="icon-chip chip-brand mb-3"><x-icon :name="$icono" /></span>
                <p class="font-semibold text-slate-900 text-sm">{{ $titulo }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $texto }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- Servicios --}}
<section id="servicios" class="max-w-6xl mx-auto px-5 py-16">
    <p class="section-label">Servicios</p>
    <h2 class="text-2xl font-semibold text-slate-900 mt-1 mb-6">Lo que gestiona el centro</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ([
            ['patient', 'Pacientes', 'Cada paciente queda asociado a un médico de cabecera, con sus datos de contacto y su historial administrativo.'],
            ['stethoscope', 'Personal médico', 'Alta y baja de médicos, tipo de vinculación, número de colegiado y pacientes a su cargo.'],
            ['briefcase', 'Personal de apoyo', 'ATS, ATS de zona, auxiliares, celadores y administrativos con su planificación de vacaciones.'],
            ['calendar', 'Agenda de consulta', 'Cuadro semanal por médico con validación de solapamientos entre franjas.'],
            ['repeat', 'Sustituciones', 'Reemplazos temporales con periodo, motivo y estado, visibles también para el paciente.'],
            ['chart', 'Reportes', 'Consultas de apoyo a la gestión, exportables a CSV y listas para imprimir.'],
        ] as [$icono, $titulo, $texto])
            <div class="card card-hover p-5">
                <span class="icon-chip chip-info mb-3"><x-icon :name="$icono" /></span>
                <h3 class="font-semibold text-slate-900">{{ $titulo }}</h3>
                <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">{{ $texto }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- Horarios --}}
<section id="horarios" class="bg-slate-50 border-y border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-16 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <div>
            <p class="section-label">Atención</p>
            <h2 class="text-2xl font-semibold text-slate-900 mt-1">Horario de atención al público</h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                La consulta se organiza por franjas horarias asignadas a cada médico.
                El personal administrativo gestiona las citas dentro del horario general del centro.
            </p>
            <div class="flex flex-wrap gap-2 mt-5">
                <span class="badge badge-brand"><x-icon name="clock" class="w-3.5 h-3.5" />Urgencias 24 h</span>
                <span class="badge badge-info">Consulta con cita previa</span>
            </div>
        </div>
        <div class="card">
            <div class="card-head"><h3 class="card-title">Horario general</h3></div>
            <div class="card-body space-y-2 text-sm">
                @foreach ([
                    ['Lunes a viernes', '07:00 – 19:00'],
                    ['Sábados', '08:00 – 13:00'],
                    ['Domingos y festivos', 'Solo urgencias'],
                ] as [$dia, $hora])
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
                        <span class="text-slate-600">{{ $dia }}</span>
                        <span class="num font-medium text-slate-900">{{ $hora }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Llamado final --}}
<section class="max-w-6xl mx-auto px-5 py-16 text-center">
    <h2 class="text-2xl font-semibold text-slate-900">¿Forma parte del equipo?</h2>
    <p class="text-sm text-slate-500 mt-2">Acceda con su usuario para gestionar la operación diaria del centro.</p>
    <a href="{{ route('login') }}" class="btn btn-primary mt-5"><x-icon name="logout" />Ingresar al sistema</a>
</section>

<footer class="border-t border-slate-200 py-6">
    <div class="max-w-6xl mx-auto px-5 flex flex-col sm:flex-row gap-2 items-center justify-between text-xs text-slate-400">
        <span>Centro de Salud · Sistema de Información para la Gestión Administrativa</span>
        <span>UAJS · Electiva Profesional II</span>
    </div>
</footer>

</body>
</html>
