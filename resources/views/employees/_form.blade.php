@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="field-label">Nombre completo <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
               class="input">
    </div>
    <div>
        <label class="field-label">NIF / Cédula <span class="req">*</span></label>
        <input type="text" name="nif" value="{{ old('nif', $employee->nif) }}" required
               class="input">
    </div>
    <div>
        <label class="field-label">N° Seguridad Social <span class="req">*</span></label>
        <input type="text" name="social_security_number" value="{{ old('social_security_number', $employee->social_security_number) }}" required
               class="input">
    </div>
    <div>
        <label class="field-label">Cargo <span class="req">*</span></label>
        <select name="employee_type" required class="select">
            @foreach (['ATS', 'ATS_Zona', 'Auxiliar', 'Celador', 'Administrativo'] as $type)
                <option value="{{ $type }}" @selected(old('employee_type', $employee->employee_type) === $type)>{{ str_replace('_', ' de ', $type) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="field-label">Teléfono</label>
        <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Dirección</label>
        <input type="text" name="address" value="{{ old('address', $employee->address) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Población</label>
        <input type="text" name="town" value="{{ old('town', $employee->town) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Provincia / Departamento</label>
        <input type="text" name="province" value="{{ old('province', $employee->province) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Código postal</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $employee->postal_code) }}"
               class="input">
    </div>
</div>

<div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-2">
    <button class="btn btn-primary"><x-icon name="check" />Guardar cambios</button>
    <a href="{{ route('employees.index') }}" class="btn btn-ghost">Cancelar</a>
</div>
