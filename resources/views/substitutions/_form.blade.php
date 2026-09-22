@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="field-label">Médico titular (ausente) <span class="req">*</span></label>
        <select name="titular_doctor_id" required class="select">
            <option value="">Seleccione…</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected((string) old('titular_doctor_id', $substitution->titular_doctor_id) === (string) $doctor->id)>{{ $doctor->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="field-label">Médico sustituto <span class="req">*</span></label>
        <select name="substitute_doctor_id" required class="select">
            <option value="">Seleccione…</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected((string) old('substitute_doctor_id', $substitution->substitute_doctor_id) === (string) $doctor->id)>{{ $doctor->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="field-label">Fecha de inicio <span class="req">*</span></label>
        <input type="date" name="start_date" required
               value="{{ old('start_date', $substitution->start_date?->format('Y-m-d')) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Fecha de fin <span class="req">*</span></label>
        <input type="date" name="end_date" required
               value="{{ old('end_date', $substitution->end_date?->format('Y-m-d')) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Estado <span class="req">*</span></label>
        <select name="status" required class="select">
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $substitution->status) === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="field-label">Motivo</label>
        <input type="text" name="reason" value="{{ old('reason', $substitution->reason) }}"
               class="input">
    </div>
</div>

<div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-2">
    <button class="btn btn-primary"><x-icon name="check" />Guardar cambios</button>
    <a href="{{ route('substitutions.index') }}" class="btn btn-ghost">Cancelar</a>
</div>
