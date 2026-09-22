<?php

/**
 * Mensajes de validacion en espanol para los formularios del sistema.
 * Laravel busca aqui las traducciones cuando APP_LOCALE=es.
 */
return [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe ser un correo válido.',
    'unique' => 'El valor de :attribute ya está registrado.',
    'exists' => 'El valor seleccionado en :attribute no es válido.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_format' => 'El campo :attribute no coincide con el formato :format.',
    'different' => 'Los campos :attribute y :other deben ser diferentes.',
    'in' => 'El valor seleccionado en :attribute no es válido.',
    'after' => 'El campo :attribute debe ser posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser igual o posterior a :date.',
    'string' => 'El campo :attribute debe ser texto.',
    'max' => [
        'string' => 'El campo :attribute no debe superar :max caracteres.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
    ],
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'confirmed' => 'La confirmación de :attribute no coincide.',

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'nif' => 'NIF',
        'social_security_number' => 'número de seguridad social',
        'collegiate_number' => 'número de colegiado',
        'doctor_type' => 'tipo de médico',
        'employee_type' => 'cargo',
        'hiring_date' => 'fecha de alta',
        'discharge_date' => 'fecha de baja',
        'doctor_id' => 'médico',
        'employee_id' => 'empleado',
        'patient_id' => 'paciente',
        'titular_doctor_id' => 'médico titular',
        'substitute_doctor_id' => 'médico sustituto',
        'day_of_week' => 'día de la semana',
        'start_time' => 'hora de inicio',
        'end_time' => 'hora de fin',
        'start_date' => 'fecha de inicio',
        'end_date' => 'fecha de fin',
        'status' => 'estado',
        'reason' => 'motivo',
        'observations' => 'observaciones',
        'phone' => 'teléfono',
        'address' => 'dirección',
        'town' => 'población',
        'province' => 'provincia',
        'postal_code' => 'código postal',
    ],
];
