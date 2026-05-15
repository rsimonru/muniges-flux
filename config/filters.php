<?php
// El primer array son los valores por defecto de los filtros.
// El segundo array de cada filtro es la definición que usa la clase para grabar el filtro y pintar las etiquetas
// El tercero son los valores por defecto.

return [
    'decos' => [
        [
            "sort" => "decos_sales_orders.id",
            "order" => "desc",
            "iPerPage" => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            "search" => "",
            "decos_ids" => "",
            "subscription_id" => "",
            "date" => [],
            "states_ids" => "",
            "carrier_reference" => "",
            "address" => "",
            "with_carrier_reference" => "",
        ],
        [
            "search" => ["text", null, "filtro rápido"],
            "decos_ids" => ["text", null, "id"],
            "subscription_id" => ["text", null, "id suscripción"],
            "date" => ["date", null, "fecha"],
            "states_ids" => ["array", "Autocomplete:states", "estados"],
            "carrier_reference" => ["text", null, "ref. transporte"],
            "address" => ["text", null, "destinatorio"],
            "with_carrier_reference" => ["boolean", null, "con ref. transporte"],
        ]
    ],
    'admin-users' => [
        [
            "sort" => "users.id",
            "order" => "desc",
            "iPerPage" => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            "search" => "",
            "group_id" => "",
            "level_id" => "",
            "active" => "",
        ],
        [
            "search" => ["text", null, "Filtro rápido"],
            "group_id" => ["text", null, "Groups"],
            "level_id" => ["text", null, "Levels"],
            "active" => ["text", null, "Usuarios activos"],
        ]
    ],
    'admin-groups' => [
        [
            "sort" => "roles.id",
            "order" => "desc",
            "iPerPage" => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            "search" => "",
        ],
        [
            "search" => ["text", null, "Filtro rápido"],
        ]
    ],
    'admin-schedules' => [
        [
            "sort" => "schedules.description",
            "order" => "desc",
            "iPerPage" => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            "search" => "",
        ],
        [
            "search" => ["text", null, "Filtro rápido"],
        ]
    ],
    'sports-events' => [
        [
            "sort" => "sports_events.from_date",
            "order" => "desc",
            "iPerPage" => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            "search" => "",
        ],
        [
            "search" => ["text", null, "Filtro rápido"],
        ]
    ],
    'sports-registrations' => [
        [
            "sort" => "sports_events_registrations.sequential",
            "order" => "desc",
            "iPerPage" => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            "search" => "",
            "events_id" => "",
        ],
        [
            "search" => ["text", null, "Filtro rápido"],
            "events_id" => ["text", null, "Eventos"],
        ]
    ],
    'sports-registrations-payments' => [
        [
            "sort" => "sports_events_registrations_payments.created_at",
            "order" => "desc",
            "iPerPage" => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            "search" => "",
            "events_id" => "",
        ],
        [
            "search" => ["text", null, "Filtro rápido"],
            "events_id" => ["text", null, "Eventos"],
            "activities_ids" => ["text", null, "Actividades"],
            "payments_id" => ["text", null, "Tipos de pago"],
            "states_id" => ["text", null, "Estados"],
        ]
    ],
];
