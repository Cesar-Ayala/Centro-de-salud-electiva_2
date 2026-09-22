@props(['icon' => 'inbox', 'title' => 'Sin registros', 'hint' => null])
<div class="empty-state">
    <x-icon :name="$icon" />
    <p class="font-medium text-slate-600">{{ $title }}</p>
    @if ($hint)<p class="mt-1">{{ $hint }}</p>@endif
    {{ $slot }}
</div>
