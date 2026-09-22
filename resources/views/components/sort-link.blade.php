@props(['field', 'label'])
@php
    /* Encabezado de tabla ordenable: alterna asc/desc conservando los filtros actuales. */
    $current   = request('sort');
    $direction = request('direction', 'asc') === 'asc' ? 'desc' : 'asc';
    $isActive  = $current === $field;
    $url = request()->fullUrlWithQuery(['sort' => $field, 'direction' => $isActive ? $direction : 'asc', 'page' => 1]);
@endphp
<a href="{{ $url }}" class="th-sort {{ $isActive ? 'is-active' : '' }}">
    {{ $label }}
    @if ($isActive)
        @if (request('direction', 'asc') === 'asc')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 14 6-6 6 6"/></svg>
        @else
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 10 6 6 6-6"/></svg>
        @endif
    @else
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 10 4-4 4 4M8 14l4 4 4-4"/></svg>
    @endif
</a>
