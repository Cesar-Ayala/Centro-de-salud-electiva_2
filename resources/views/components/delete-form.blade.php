@props(['action', 'message' => '¿Desea eliminar este registro de forma definitiva?', 'label' => 'Eliminar'])
<form method="POST" action="{{ $action }}" class="inline js-confirm" data-message="{{ $message }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger" title="{{ $label }}" aria-label="{{ $label }}">
        <x-icon name="trash" />
    </button>
</form>
