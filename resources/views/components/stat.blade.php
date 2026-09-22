@props([
    'label' => '',
    'value' => 0,
    'hint' => null,
    'icon' => 'circle',
    'tone' => 'brand',
    'href' => null,
    'progress' => null,
])
@php $tag = $href ? 'a' : 'div'; @endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @endif class="card card-hover p-4 block fade-in">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="section-label truncate">{{ $label }}</p>
            <p class="stat-value mt-1">{{ $value }}</p>
        </div>
        <span class="icon-chip chip-{{ $tone }}"><x-icon :name="$icon" /></span>
    </div>
    @if (! is_null($progress))
        <div class="progress mt-3"><span style="width: {{ max(0, min(100, $progress)) }}%"></span></div>
    @endif
    @if ($hint)
        <p class="text-xs text-slate-500 mt-2">{{ $hint }}</p>
    @endif
</{{ $tag }}>
