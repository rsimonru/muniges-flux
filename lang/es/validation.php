<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'              => 'El campo :attribute debe ser aceptado.',
	'active_url'            => 'El campo :attribute no es una URL válida.',
	'after'                 => 'El campo :attribute debe ser una fecha posterior de :date.',
	'after_or_equal'        => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
	'alpha'                 => 'El campo :attribute sólo puede contener letras.',
	'alpha_dash'            => 'El campo :attribute sólo puede contener letras, números y guiones.',
	'alpha_num'             => 'El campo :attribute sólo puede contener letras y números.',
	'array'                 => 'El campo :attribute debe ser un arreglo.',
	'before'                => 'El campo :attribute debe ser una fecha anterior de :date.',
	'before_or_equal'       => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
	'between'               => [
		'numeric' => 'El campo :attribute debe estar entre :min - :max.',
		'file'    => 'El campo :attribute debe estar entre :min - :max kilobytes.',
		'string'  => 'El campo :attribute debe estar entre :min - :max caracteres.',
		'array'   => 'El campo :attribute debe tener entre :min y :max elementos.',
	],
	'boolean'               => 'El campo :attribute debe ser verdadero o falso.',
	'confirmed'             => 'El campo :attribute no coincide.',
	'date'                  => 'El campo :attribute no es una fecha válida.',
	'date_format' 	        => 'El campo :attribute no corresponde con el formato :format.',
	'different'             => 'Los campos :attribute y :other deben ser diferentes.',
	'digits'                => 'El campo :attribute debe ser de :digits dígitos.',
	'digits_between'        => 'El campo :attribute debe tener entre :min y :max dígitos.',
	'dimensions'            => 'El campo :attribute no tiene una dimensión válida.',
	'distinct'              => 'El campo :attribute tiene un valor duplicado.',
	'email'                 => 'El formato del :attribute es inválido.',
	'exists'                => 'El campo :attribute seleccionado es inválido.',
	'file'                  => 'El campo :attribute debe ser un archivo.',
	'filled'                => 'El campo :attribute es requerido.',
	'gt'                    => [
		'numeric' => 'El campo :attribute debe ser mayor que :value.',
		'file'    => 'El campo :attribute debe ser mayor que :value kilobytes.',
		'string'  => 'El campo :attribute debe ser mayor que :value caracteres.',
		'array'   => 'El campo :attribute puede tener hasta :value elementos.',
	],
	'gte'                   => [
		'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
		'file'    => 'El campo :attribute debe ser mayor o igual que :value kilobytes.',
		'string'  => 'El campo :attribute debe ser mayor o igual que :value caracteres.',
		'array'   => 'El campo :attribute puede tener :value elementos o más.',
	],
	'image'                 => 'El campo :attribute debe ser una imagen.',
	'in'                    => 'El campo :attribute seleccionado es inválido.',
	'in_array'              => 'El campo :attribute no existe en :other.',
	'integer'               => 'El campo :attribute debe ser un entero.',
	'ip'                    => 'El campo :attribute debe ser una dirección IP válida.',
	'ipv4'                  => 'El campo :attribute debe ser una dirección IPv4 válida.',
	'ipv6'                  => 'El campo :attribute debe ser una dirección IPv6 válida.',
	'json'                  => 'El campo :attribute debe ser una cadena JSON válida.',
	'lt'                   => [
		'numeric' => 'El campo :attribute debe ser menor que :max.',
		'file'    => 'El campo :attribute debe ser menor que :max kilobytes.',
		'string'  => 'El campo :attribute debe ser menor que :max caracteres.',
		'array'   => 'El campo :attribute puede tener hasta :max elementos.',
	],
	'lte'                   => [
		'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
		'file'    => 'El campo :attribute debe ser menor o igual que :max kilobytes.',
		'string'  => 'El campo :attribute debe ser menor o igual que :max caracteres.',
		'array'   => 'El campo :attribute no puede tener más que :max elementos.',
	],
	'max'                   => [
		'numeric' => 'El campo :attribute debe ser menor que :max.',
		'file'    => 'El campo :attribute debe ser menor que :max kilobytes.',
		'string'  => 'El campo :attribute debe ser menor que :max caracteres.',
		'array'   => 'El campo :attribute puede tener hasta :max elementos.',
	],
	'mimes'                 => 'El campo :attribute debe ser un archivo de tipo: :values.',
	'mimetypes'             => 'El campo :attribute debe ser un archivo de tipo: :values.',
	'min'                   => [
		'numeric' => 'El campo :attribute debe tener al menos :min.',
		'file'    => 'El campo :attribute debe tener al menos :min kilobytes.',
		'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
		'array'   => 'El campo :attribute debe tener al menos :min elementos.',
	],
	'not_in'                => 'El campo :attribute seleccionado es invalido.',
	'not_regex'             => 'El formato del campo :attribute es inválido.',
	'numeric'               => 'El campo :attribute debe ser un número.',
	'present'               => 'El campo :attribute debe estar presente.',
	'regex'                 => 'El formato del campo :attribute es inválido.',
	'required'              => 'El campo :attribute es requerido.',
	'required_if'           => 'El campo :attribute es requerido cuando el campo :other es :value.',
	'required_unless'       => 'El campo :attribute es requerido a menos que :other esté presente en :values.',
	'required_with'         => 'El campo :attribute es requerido cuando :values está presente.',
	'required_with_all'     => 'El campo :attribute es requerido cuando :values está presente.',
	'required_without'      => 'El campo :attribute es requerido cuando :values no está presente.',
	'required_without_all'  => 'El campo :attribute es requerido cuando ningún :values está presente.',
	'same'                  => 'El campo :attribute y :other debe coincidir.',
	'size'                  => [
		'numeric' => 'El campo :attribute debe ser :size.',
		'file'    => 'El campo :attribute debe tener :size kilobytes.',
		'string'  => 'El campo :attribute debe tener :size caracteres.',
		'array'   => 'El campo :attribute debe contener :size elementos.',
	],
	'starts_with'           => 'El :attribute debe empezar con uno de los siguientes valores :values',
	'string'                => 'El campo :attribute debe ser una cadena.',
	'timezone'              => 'El campo :attribute debe ser una zona válida.',
	'unique'                => 'El campo :attribute ya ha sido utilizado.',
	'uploaded'              => 'El campo :attribute no ha podido ser cargado.',
	'url'                   => 'El formato de :attribute es inválido.',
	'uuid'                  => 'El :attribute debe ser un UUID valido.',
    'invalid_date'          => "Fecha no válida",
    'invalid_hour'          => "Hora no válida",
    'words'                 => 'El campo :attribute no tiene el formato correcto.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
		"name" => "Nombre",
		"current_password" => "contraseña actual",
		"password" => "contraseña",
		"new_password" => "contraseña",
		"new_password_confirmation" => "repetir contraseña",
        "payletter_templates_id" => "Plantilla carta pago",
        "payproof_templates_id" => "Plantilla justif. pago",

        "vat" => "NIF/CIF",
        "zip" => "Código postal",

        "group_roles_id" => "Tipo",

        "areas_id" => "Área",
        "form_file" => "Fichero",
        "file" => "Fichero",
        "file_type" => "Tipo",
        "description" => "Descripción",
        "text.*" => "Descripción",
        "text" => "Descripción",
        "title.*" => "Título",
        "title" => "Título",
        "schedules_id" => "Agenda",
        "accounts_id" => "Cuenta",
        "contact" => "Contacto",
        "surname" => "Apellidos",
        "phone" => "Teléfono",
        "mobile" => "Móvil",
        "address" => "Dirección",
        "town" => "Población",
        "province" => "Provincia",
        "observations" => "Observaciones",

        "legal_form" => "Razón social",
        "position" => "Puesto",
        "ss_number" => "Nº Seg. Social",
        "holidays" => "Días de vacaciones",
        "freedays" => "Asuntos propios",
        "antiquity" => "Antigüedad",
        "journey_hours" => "Jornada (horas)",
        "journey_start" => "Inicio jornada semanal",
        "journey_end" => "Fin jornada semanal",
        "contract_start" => "Inicio contrato",
        "contract_end" => "Fin contrato",
        "birthday" => "Fecha nacimiento",
        "employees_id" => "Empleado",
        "reasons_id" => "Motivo",
        "quota" => "Cupo",
        "from_date" => "Desde",
        "to_date" => "Hasta",
        "types_id" => "Tipo",
        "inscription_from" => "Inscripción desde",
        "inscription_to" => "Inscripción hasta",
        "inscr_templates_id" => "Plantilla inscripción",
        "pay_from_date" => "Desde",
        "pay_to_date" => "Hasta",
        "tutor_name" => "Nombre y apellidos padre, madre o tutor",
        "tutor_vat" => "DNI/NIE tutor",
        "iban" => "IBAN",
        "payment_date" => "Fecha pago",
        "payment_data" => "Datos de pago",
        "states_id" => "Estado",

        "photo" => "Fotografía",
        "categories_id" => "Categoría",

        "procedures_id" => "Procedimiento",
        "amount" => "Importe",

        "acq_vat" => "DNI / NIE / CIF",
        "acq_name" => "Nombre",
        "acq_address" => "Dirección",
        "acq_town" => "Población",
        "acq_province" => "Provincia",
        "acq_zip" => "C. postal",
        "tra_vat" => "DNI / NIE / CIF",
        "tra_name" => "Nombre",
        "tra_address" => "Dirección",
        "tra_town" => "Población",
        "tra_province" => "Provincia",
        "tra_zip" => "C. postal",
        "cadastral_reference" => "Referencia catastral",
        "parcel_address" => "Dirección del bien",
        "terrain_value" => "Valor de la superficie",
        "dtypes_id" => "Tipo de documento",
        "document_date" => "Fecha de documento",
        "transmission_date" => "Fecha de transmisión",
        "previous_date" => "Fecha anterior",
        "protocol" => "Protocolo",
        "notary" => "Notario",
        "bonus_percent" => "Bonificatión %",
        "surcharge_percent" => "Recargo %",
    ],

];
