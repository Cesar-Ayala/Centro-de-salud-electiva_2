@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="field-label">Nombre completo <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name', $doctor->name) }}" required
               class="input">
    </div>

    <div>
        <label class="field-label">NIF / Cédula <span class="req">*</span></label>
        <input type="text" name="nif" value="{{ old('nif', $doctor->nif) }}" required
               class="input">
    </div>

    <div>
        <label class="field-label">N° Seguridad Social <span class="req">*</span></label>
        <input type="text" name="social_security_number" value="{{ old('social_security_number', $doctor->social_security_number) }}" required
               class="input">
    </div>

    <div>
        <label class="field-label">N° de colegiado <span class="req">*</span></label>
        <input type="text" name="collegiate_number" value="{{ old('collegiate_number', $doctor->collegiate_number) }}" required
               class="input">
    </div>

    <div>
        <label class="field-label">Tipo de médico <span class="req">*</span></label>
        <select name="doctor_type" required class="select">
            @foreach (['Titular', 'Interino', 'Sustituto'] as $type)
                <option value="{{ $type }}" @selected(old('doctor_type', $doctor->doctor_type) === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="field-label">Teléfono</label>
        <input type="text" name="phone" value="{{ old('phone', $doctor->phone) }}"
               class="input">
    </div>

    <div>
        <label class="field-label">Dirección</label>
        <input type="text" name="address" value="{{ old('address', $doctor->address) }}"
               class="input">
    </div>

    <div>
        <label class="field-label">Población</label>
        <input type="text" name="town" value="{{ old('town', $doctor->town) }}"
               class="input">
    </div>

    <div>
        <label class="field-label">Provincia / Departamento</label>
        <input type="text" name="province" value="{{ old('province', $doctor->province) }}"
               class="input">
    </div>

    <div>
        <label class="field-label">Código postal</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $doctor->postal_code) }}"
               class="input">
    </div>

    <div>
        <label class="field-label">Fecha de alta</label>
        <input type="date" name="hiring_date" value="{{ old('hiring_date', $doctor->hiring_date?->format('Y-m-d')) }}"
               class="input">
    </div>

    <div>
        <label class="field-label">Fecha de baja</label>
        <input type="date" name="discharge_date" value="{{ old('discharge_date', $doctor->discharge_date?->format('Y-m-d')) }}"
               class="input">
        <p class="text-xs text-slate-400 mt-1">Déjela vacía si el médico continúa activo.</p>
    </div>
</div>

<div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-2">
    <button class="btn btn-primary"><x-icon name="check" />Guardar cambios</button>
    <a href="{{ route('doctors.index') }}" class="btn btn-ghost">Cancelar</a>
</div>
