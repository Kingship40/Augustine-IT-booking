<?php

declare(strict_types=1);

return [
    'name' => 'IT Service Delivery Marketplace',
    'base_url' => getenv('APP_URL') ?: 'http://localhost',
    'session_name' => getenv('SESSION_NAME') ?: 'it_service_delivery_session',
    'default_route' => getenv('DEFAULT_ROUTE') ?: 'home',
];