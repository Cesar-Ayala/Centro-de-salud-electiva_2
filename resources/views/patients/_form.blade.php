@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="field-label">Nombre completo <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name', $patient->name) }}" required
               class="input">
    </div>
    <div>
        <label class="field-label">NIF / Cédula <span class="req">*</span></label>
        <input type="text" name="nif" value="{{ old('nif', $patient->nif) }}" required
               class="input">
    </div>
    <div>
        <label class="field-label">N° Seguridad Social <span class="req">*</span></label>
        <input type="text" name="social_security_number" value="{{ old('social_security_number', $patient->social_security_number) }}" required
               class="input">
    </div>
    <div>
        <label class="field-label">Médico asignado</label>
        <select name="doctor_id" class="select">
            <option value="">Sin asignar</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected((string) old('doctor_id', $patient->doctor_id) === (string) $doctor->id)>
                    {{ $doctor->name }} ({{ $doctor->doctor_type }})
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="field-label">Teléfono</label>
        <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Dirección</label>
        <input type="text" name="address" value="{{ old('address', $patient->address) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Código postal</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $patient->postal_code) }}"
               class="input">
    </div>
</div>

<div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-2">
    <button class="btn btn-primary"><x-icon name="check" />Guardar cambios</button>
    <a href="{{ route('patients.index') }}" class="btn btn-ghost">Cancelar</a>
</div>
