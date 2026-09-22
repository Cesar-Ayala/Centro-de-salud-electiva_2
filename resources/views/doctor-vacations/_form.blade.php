@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="field-label">Médico <span class="req">*</span></label>
        <select name="doctor_id" required class="select">
            <option value="">Seleccione…</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected((string) old('doctor_id', $vacation->doctor_id) === (string) $doctor->id)>{{ $doctor->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="field-label">Fecha de inicio <span class="req">*</span></label>
        <input type="date" name="start_date" required value="{{ old('start_date', $vacation->start_date?->format('Y-m-d')) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Fecha de fin <span class="req">*</span></label>
        <input type="date" name="end_date" required value="{{ old('end_date', $vacation->end_date?->format('Y-m-d')) }}"
               class="input">
    </div>
    <div>
        <label class="field-label">Estado <span class="req">*</span></label>
        <select name="status" required class="select">
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $vacation->status) === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="field-label">Motivo</label>
        <input type="text" name="reason" value="{{ old('reason', $vacation->reason) }}"
               class="input">
    </div>
    <div class="md:col-span-2">
        <label class="field-label">Observaciones</label>
        <textarea name="observations" rows="3" class="input">{{ old('observations', $vacation->observations) }}</textarea>
    </div>
</div>

<div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-2">
    <button class="btn btn-primary"><x-icon name="check" />Guardar cambios</button>
    <a href="{{ route('doctor-vacations.index') }}" class="btn btn-ghost">Cancelar</a>
</div>
