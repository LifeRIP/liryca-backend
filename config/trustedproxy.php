<?php

return [
    // Trust only proxies explicitly configured for this environment.
    'proxies' => array_values(array_filter(array_map(
        'trim', explode(',', env('TRUSTED_PROXIES', ''))
    ))),
];
