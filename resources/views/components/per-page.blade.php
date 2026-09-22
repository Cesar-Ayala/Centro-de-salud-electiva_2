@props(['options' => [10, 25, 50, 100]])
<form method="GET" class="flex items-center gap-2 text-xs text-slate-500">
    @foreach (request()->except(['per_page', 'page']) as $key => $value)
        @if (! is_array($value))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    <label for="per_page">Mostrar</label>
    <select id="per_page" name="per_page" class="select select-inline" onchange="this.form.submit()">
        @foreach ($options as $option)
            <option value="{{ $option }}" @selected((int) request('per_page', 10) === $option)>{{ $option }}</option>
        @endforeach
    </select>
    <span>registros</span>
</form>
