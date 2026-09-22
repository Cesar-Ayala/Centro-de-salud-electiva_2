@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="field-label">Médico <span class="req">*</span></label>
        <select name="doctor_id" required class="select">
            <option value="">Seleccione…</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected((string) old('doctor_id', $schedule->doctor_id) === (string) $doctor->id)>
                    {{ $doctor->name }} ({{ $doctor->doctor_type }})
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="field-label">Día de la semana <span class="req">*</span></label>
        <select name="day_of_week" required class="select">
            @foreach ($days as $day)
                <option value="{{ $day }}" @selected(old('day_of_week', $schedule->day_of_week) === $day)>{{ $day }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="field-label">Hora inicio <span class="req">*</span></label>
            <input type="time" name="start_time" required
                   value="{{ old('start_time', $schedule->start_time ? substr($schedule->start_time, 0, 5) : '') }}"
                   class="input">
        </div>
        <div>
            <label class="field-label">Hora fin <span class="req">*</span></label>
            <input type="time" name="end_time" required
                   value="{{ old('end_time', $schedule->end_time ? substr($schedule->end_time, 0, 5) : '') }}"
                   class="input">
        </div>
    </div>
</div>

<div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-2">
    <button class="btn btn-primary"><x-icon name="check" />Guardar cambios</button>
    <a href="{{ route('schedules.index') }}" class="btn btn-ghost">Cancelar</a>
</div>
