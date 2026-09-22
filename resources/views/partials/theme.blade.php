{{-- Sistema de diseño del centro de salud: variables, componentes y utilidades propias. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --ink-900: #0a2540;
        --ink-800: #0f3050;
        --ink-700: #16405f;
        --ink-500: #47607a;
        --ink-400: #6b819a;

        --brand-50:  #ecfdf9;
        --brand-100: #d1faef;
        --brand-200: #a7f3e0;
        --brand-300: #6ee7cd;
        --brand-500: #14b8a6;
        --brand-600: #0d9488;
        --brand-700: #0f766e;
        --brand-800: #115e59;

        --accent-50: #eff6ff;
        --accent-100:#dbeafe;
        --accent-600:#2563eb;
        --accent-700:#1d4ed8;

        --surface: #ffffff;
        --canvas:  #f1f5f9;
        --line:    #e2e8f0;
        --line-soft:#eef2f7;

        --ok-50:#ecfdf5;   --ok-600:#059669;  --ok-700:#047857;
        --warn-50:#fffbeb; --warn-600:#d97706; --warn-700:#b45309;
        --bad-50:#fef2f2;  --bad-600:#dc2626;  --bad-700:#b91c1c;
        --info-50:#eff6ff; --info-600:#2563eb; --info-700:#1d4ed8;

        --radius: 14px;
        --shadow-sm: 0 1px 2px rgba(10,37,64,.06), 0 1px 3px rgba(10,37,64,.04);
        --shadow-md: 0 4px 16px rgba(10,37,64,.07), 0 1px 3px rgba(10,37,64,.05);
        --shadow-lg: 0 18px 40px -18px rgba(10,37,64,.35);
    }

    html { -webkit-text-size-adjust: 100%; }
    body {
        font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
        background: var(--canvas);
        color: var(--ink-800);
        -webkit-font-smoothing: antialiased;
    }
    body.app-shell {
        background:
            radial-gradient(900px 420px at 88% -12%, rgba(20,184,166,.10), transparent 60%),
            radial-gradient(700px 380px at -8% 0%, rgba(37,99,235,.08), transparent 55%),
            var(--canvas);
    }

    /* ---------- Superficies ---------- */
    .card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
    }
    .card-hover { transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease; }
    .card-hover:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); border-color: #cfe0e6; }
    .card-head {
        display: flex; align-items: center; justify-content: space-between; gap: .75rem;
        padding: 1rem 1.25rem; border-bottom: 1px solid var(--line-soft);
    }
    .card-title { font-weight: 600; color: var(--ink-900); font-size: .95rem; letter-spacing: -.01em; }
    .card-body { padding: 1.25rem; }
    .section-label {
        font-size: .68rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--ink-400);
    }

    /* ---------- Botones ---------- */
    .btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
        padding: .55rem .95rem; border-radius: 10px; font-size: .85rem; font-weight: 500;
        border: 1px solid transparent; cursor: pointer; white-space: nowrap;
        transition: background-color .15s ease, color .15s ease, border-color .15s ease, box-shadow .15s ease, transform .1s ease;
    }
    .btn:active { transform: translateY(1px); }
    .btn svg { width: 16px; height: 16px; flex: none; }
    .btn-primary { background: var(--brand-700); color: #fff; box-shadow: 0 1px 2px rgba(15,118,110,.35); }
    .btn-primary:hover { background: var(--brand-800); }
    .btn-dark { background: var(--ink-900); color: #fff; }
    .btn-dark:hover { background: var(--ink-700); }
    .btn-ghost { background: #fff; color: var(--ink-700); border-color: var(--line); }
    .btn-ghost:hover { background: #f8fafc; border-color: #cbd5e1; }
    .btn-soft { background: var(--brand-50); color: var(--brand-800); border-color: var(--brand-100); }
    .btn-soft:hover { background: var(--brand-100); }
    .btn-danger { background: #fff; color: var(--bad-600); border-color: #fecaca; }
    .btn-danger:hover { background: var(--bad-50); }
    .btn-sm { padding: .35rem .7rem; font-size: .78rem; border-radius: 8px; }
    .btn-icon { padding: .45rem; border-radius: 9px; }

    /* ---------- Formularios ---------- */
    .field-label { display:block; font-size:.78rem; font-weight:600; color: var(--ink-700); margin-bottom:.35rem; }
    .input, .select, textarea.input {
        width: 100%; background: #fff; border: 1px solid var(--line); border-radius: 10px;
        padding: .55rem .75rem; font-size: .85rem; color: var(--ink-900);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .input::placeholder { color: #94a3b8; }
    .input:focus, .select:focus {
        outline: none; border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(20,184,166,.16);
    }
    .select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%2364748b'%3E%3Cpath d='M5.2 7.5 10 12l4.8-4.5'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right .6rem center; background-size: 16px;
        padding-right: 2rem;
    }
    .select-inline { width: auto; padding: .3rem 1.8rem .3rem .5rem; font-size: .75rem; border-radius: 8px; }
    .input-icon { position: relative; }
    .input-icon svg { position: absolute; left: .65rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8; }
    .input-icon .input { padding-left: 2.1rem; }
    .form-section { border-top: 1px dashed var(--line); padding-top: 1.25rem; margin-top: 1.25rem; }
    .form-section:first-of-type { border-top: 0; padding-top: 0; margin-top: 0; }
    .req { color: var(--bad-600); }

    /* ---------- Tablas ---------- */
    .table { width: 100%; border-collapse: collapse; font-size: .85rem; }
    .table thead th {
        text-align: left; font-size: .7rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
        color: var(--ink-400); background: #f8fafc; padding: .7rem 1rem; border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }
    .table tbody td { padding: .8rem 1rem; border-bottom: 1px solid var(--line-soft); vertical-align: middle; }
    .table tbody tr { transition: background-color .12s ease; }
    .table tbody tr:hover { background: #f8fbfc; }
    .table tbody tr:last-child td { border-bottom: 0; }
    .table .num { font-variant-numeric: tabular-nums; }
    .th-sort { color: inherit; display: inline-flex; align-items: center; gap: .3rem; }
    .th-sort:hover { color: var(--brand-700); }
    .th-sort svg { width: 12px; height: 12px; opacity: .45; }
    .th-sort.is-active { color: var(--brand-700); }
    .th-sort.is-active svg { opacity: 1; }

    /* ---------- Badges ---------- */
    .badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .2rem .55rem; border-radius: 999px; font-size: .72rem; font-weight: 600;
        border: 1px solid transparent; white-space: nowrap;
    }
    .badge .dot { width: 6px; height: 6px; border-radius: 999px; background: currentColor; }
    .badge-ok   { background: var(--ok-50);   color: var(--ok-700);   border-color: #a7f3d0; }
    .badge-warn { background: var(--warn-50); color: var(--warn-700); border-color: #fde68a; }
    .badge-bad  { background: var(--bad-50);  color: var(--bad-700);  border-color: #fecaca; }
    .badge-info { background: var(--info-50); color: var(--info-700); border-color: #bfdbfe; }
    .badge-mute { background: #f1f5f9;        color: #475569;         border-color: #e2e8f0; }
    .badge-brand{ background: var(--brand-50);color: var(--brand-800);border-color: var(--brand-100); }

    /* ---------- Avatares / chips ---------- */
    .avatar {
        display: inline-flex; align-items: center; justify-content: center;
        width: 36px; height: 36px; border-radius: 999px; font-size: .75rem; font-weight: 700;
        color: #fff; background: linear-gradient(135deg, var(--brand-600), var(--ink-700)); flex: none;
        letter-spacing: .02em;
    }
    .avatar-sm { width: 28px; height: 28px; font-size: .65rem; }
    .avatar-lg { width: 60px; height: 60px; font-size: 1.15rem; }
    .icon-chip {
        display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; border-radius: 12px; flex: none;
    }
    .icon-chip svg { width: 20px; height: 20px; }
    .chip-brand { background: var(--brand-50); color: var(--brand-700); }
    .chip-info  { background: var(--info-50);  color: var(--info-600); }
    .chip-warn  { background: var(--warn-50);  color: var(--warn-600); }
    .chip-bad   { background: var(--bad-50);   color: var(--bad-600); }
    .chip-ink   { background: #eef2f7;         color: var(--ink-700); }

    /* ---------- Estadísticas ---------- */
    .stat-value { font-size: 1.85rem; font-weight: 700; color: var(--ink-900); line-height: 1.1; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .progress { height: 6px; border-radius: 999px; background: #eef2f7; overflow: hidden; }
    .progress > span { display:block; height:100%; border-radius:999px; background: linear-gradient(90deg, var(--brand-500), var(--brand-700)); }

    /* ---------- Barra lateral ---------- */
    .sidebar {
        background: linear-gradient(180deg, #0a2540 0%, #0c3243 60%, #0e3b45 100%);
        color: #cbd5e1;
    }
    .nav-link {
        display: flex; align-items: center; gap: .7rem;
        padding: .55rem .7rem; border-radius: 10px; font-size: .85rem; color: #b6c6d6;
        transition: background-color .15s ease, color .15s ease;
    }
    .nav-link svg { width: 18px; height: 18px; flex: none; opacity: .8; }
    .nav-link:hover { background: rgba(255,255,255,.07); color: #fff; }
    .nav-link.is-active {
        background: rgba(20,184,166,.16); color: #fff; font-weight: 500;
        box-shadow: inset 2px 0 0 var(--brand-500);
    }
    .nav-link.is-active svg { color: var(--brand-300); opacity: 1; }
    .nav-group { font-size: .64rem; letter-spacing: .14em; text-transform: uppercase; color: #64809a; padding: 1rem .7rem .35rem; }
    .nav-count { margin-left: auto; font-size: .68rem; color: #7f9ab0; background: rgba(255,255,255,.07); border-radius: 999px; padding: .05rem .4rem; }

    /* ---------- Alertas ---------- */
    .alert { display:flex; gap:.7rem; padding: .85rem 1rem; border-radius: 12px; font-size: .85rem; border: 1px solid; }
    .alert svg { width: 18px; height: 18px; flex: none; margin-top: 1px; }
    .alert-ok  { background: var(--ok-50);  border-color: #a7f3d0; color: var(--ok-700); }
    .alert-bad { background: var(--bad-50); border-color: #fecaca; color: var(--bad-700); }
    .alert-warn{ background: var(--warn-50);border-color: #fde68a; color: var(--warn-700); }

    /* ---------- Horario semanal ---------- */
    .slot {
        border-radius: 10px; padding: .45rem .55rem; font-size: .72rem; line-height: 1.25;
        background: linear-gradient(135deg, var(--brand-50), #fff);
        border: 1px solid var(--brand-100); color: var(--brand-800);
    }
    .slot strong { display:block; color: var(--ink-900); font-weight: 600; }

    /* ---------- Varios ---------- */
    .empty-state { text-align:center; padding: 2.5rem 1rem; color: var(--ink-400); font-size: .85rem; }
    .empty-state svg { width: 34px; height: 34px; margin: 0 auto .6rem; color: #cbd5e1; }
    .link { color: var(--accent-600); font-weight: 500; }
    .link:hover { text-decoration: underline; }
    .divider { height:1px; background: var(--line-soft); }
    .fade-in { animation: fadeIn .28s ease both; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(6px);} to { opacity:1; transform:none;} }
    .pulse-dot { position: relative; }
    .pulse-dot::after {
        content:''; position:absolute; inset:-4px; border-radius:999px;
        border: 2px solid rgba(20,184,166,.5); animation: pulseRing 1.8s ease-out infinite;
    }
    @keyframes pulseRing { 0% { transform: scale(.7); opacity: .9; } 100% { transform: scale(1.4); opacity: 0; } }

    /* Paginación de Laravel */
    .pagination-wrap svg { width: 16px; height: 16px; }

    /* ---------- Impresión ---------- */
    @media print {
        .no-print, .sidebar, header.topbar { display: none !important; }
        body, body.app-shell { background: #fff !important; }
        .card { box-shadow: none !important; border-color: #d7dee6 !important; break-inside: avoid; }
        main { padding: 0 !important; }
    }
</style>
