@props(['name' => 'circle', 'size' => null])
@php
    /* Set de iconos SVG (trazo) usado en toda la aplicación. */
    $paths = [
        'dashboard'   => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'stethoscope' => '<path d="M6 3v5a4 4 0 0 0 8 0V3"/><path d="M6 3H4.5M14 3h1.5"/><path d="M10 12v2a5 5 0 0 0 5 5 4 4 0 0 0 4-4v-1"/><circle cx="19" cy="12" r="2"/>',
        'users'       => '<path d="M16 19v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 17.5V19"/><circle cx="10" cy="8" r="3.2"/><path d="M20 19v-1.5a3.5 3.5 0 0 0-2.6-3.4"/><path d="M15.5 5.2a3.2 3.2 0 0 1 0 5.6"/>',
        'patient'     => '<path d="M12 21s-6.5-4.2-6.5-9A3.9 3.9 0 0 1 12 9.4 3.9 3.9 0 0 1 18.5 12c0 4.8-6.5 9-6.5 9Z"/><path d="M8.6 13.5h1.7l1 1.9 1.4-3.4 1 1.5h1.7"/>',
        'calendar'    => '<rect x="3" y="4.5" width="18" height="16" rx="2.5"/><path d="M3 9.5h18M8 3v3M16 3v3"/>',
        'repeat'      => '<path d="M4 9V8a3 3 0 0 1 3-3h10l-2.5-2.5M20 15v1a3 3 0 0 1-3 3H7l2.5 2.5"/>',
        'palm'        => '<path d="M12 21V11"/><path d="M12 11c0-2.5 2-4.5 4.5-4.5 1.6 0 3 .8 3.8 2"/><path d="M12 11c0-2.5-2-4.5-4.5-4.5-1.6 0-3 .8-3.8 2"/><path d="M12 11a4 4 0 0 1 4-4"/><path d="M12 11a4 4 0 0 0-4-4"/><path d="M9 21h6"/>',
        'briefcase'   => '<rect x="3" y="7.5" width="18" height="13" rx="2.5"/><path d="M8.5 7.5V6a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v1.5M3 12.5h18"/>',
        'chart'       => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'search'      => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-3.6-3.6"/>',
        'plus'        => '<path d="M12 5v14M5 12h14"/>',
        'pencil'      => '<path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3Z"/><path d="m14.5 6.5 3 3"/>',
        'trash'       => '<path d="M4 7h16M9.5 7V5.5A1.5 1.5 0 0 1 11 4h2a1.5 1.5 0 0 1 1.5 1.5V7"/><path d="M6.5 7 7.5 20h9L17.5 7"/><path d="M10.5 11v5M13.5 11v5"/>',
        'eye'         => '<path d="M2.5 12S6 5.8 12 5.8 21.5 12 21.5 12 18 18.2 12 18.2 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>',
        'download'    => '<path d="M12 4v11M7.5 10.5 12 15l4.5-4.5"/><path d="M4.5 19.5h15"/>',
        'printer'     => '<path d="M7 9V4h10v5"/><rect x="3.5" y="9" width="17" height="7" rx="2"/><path d="M7 14h10v6H7z"/>',
        'logout'      => '<path d="M14 4h3.5A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5H14"/><path d="M10 8.5 6.5 12 10 15.5M6.5 12H15"/>',
        'menu'        => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close'       => '<path d="M6 6l12 12M18 6 6 18"/>',
        'check'       => '<circle cx="12" cy="12" r="8.5"/><path d="m8.5 12.2 2.4 2.4 4.6-4.9"/>',
        'warning'     => '<path d="M10.3 4.3 2.9 17.1A2 2 0 0 0 4.6 20h14.8a2 2 0 0 0 1.7-2.9L13.7 4.3a2 2 0 0 0-3.4 0Z"/><path d="M12 9.5v4M12 17h.01"/>',
        'info'        => '<circle cx="12" cy="12" r="8.5"/><path d="M12 11v5M12 8h.01"/>',
        'clock'       => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 1.8"/>',
        'phone'       => '<path d="M6.5 3.5h3l1.5 4-2 1.4a12 12 0 0 0 5.1 5.1l1.4-2 4 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.5 5.7a2 2 0 0 1 2-2.2Z"/>',
        'pin'         => '<path d="M12 21s6.5-6 6.5-10.5A6.5 6.5 0 0 0 5.5 10.5C5.5 15 12 21 12 21Z"/><circle cx="12" cy="10.5" r="2.4"/>',
        'id'          => '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><circle cx="8.5" cy="11" r="2.2"/><path d="M5 16.2c.7-1.4 2-2.2 3.5-2.2s2.8.8 3.5 2.2M15 10h4M15 13.5h4"/>',
        'filter'      => '<path d="M4 6h16l-6.2 7.3V19l-3.6-1.8v-3.9Z"/>',
        'back'        => '<path d="M15 5.5 8.5 12l6.5 6.5"/>',
        'chevron-down'=> '<path d="m6.5 9.5 5.5 5 5.5-5"/>',
        'activity'    => '<path d="M3 12h4l2.5-6 5 12 2.5-6h4"/>',
        'building'    => '<path d="M4 20V6.5A2.5 2.5 0 0 1 6.5 4h7A2.5 2.5 0 0 1 16 6.5V20"/><path d="M16 10h2.5A2.5 2.5 0 0 1 21 12.5V20M3 20h18"/><path d="M7.5 8h1.5M11 8h1.5M7.5 11.5h1.5M11 11.5h1.5M7.5 15h1.5M11 15h1.5"/>',
        'mail'        => '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="m3.5 7 8.5 6 8.5-6"/>',
        'shield'      => '<path d="M12 3.5 5 6v5.5c0 4.3 3 7.4 7 9 4-1.6 7-4.7 7-9V6l-7-2.5Z"/><path d="m9 12 2 2 4-4"/>',
        'sparkles'    => '<path d="m12 3.5 1.6 4.4 4.4 1.6-4.4 1.6L12 15.5l-1.6-4.4L6 9.5l4.4-1.6L12 3.5Z"/><path d="M18.5 15.5 19.3 18l2.2.8-2.2.8-.8 2.4-.8-2.4-2.2-.8 2.2-.8.8-2.5Z"/>',
        'inbox'       => '<path d="M3.5 13.5 5.8 6a2 2 0 0 1 1.9-1.4h8.6A2 2 0 0 1 18.2 6l2.3 7.5"/><path d="M3.5 13.5h4l1.2 2.5h6.6l1.2-2.5h4v4a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2v-4Z"/>',
        'sun'         => '<circle cx="12" cy="12" r="4"/><path d="M12 2.5V5M12 19v2.5M4.2 4.2 6 6M18 18l1.8 1.8M2.5 12H5M19 12h2.5M4.2 19.8 6 18M18 6l1.8-1.8"/>',
        'circle'      => '<circle cx="12" cy="12" r="8.5"/>',
    ];
    $d = $paths[$name] ?? $paths['circle'];
@endphp
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
     @if ($size) width="{{ $size }}" height="{{ $size }}" @endif
     {{ $attributes }}>{!! $d !!}</svg>
