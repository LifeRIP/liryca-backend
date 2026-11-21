<?php

return [
    'limits' => [
        'traffic' => (int) env('RATE_LIMIT_TRAFFIC', 240),
        'api' => (int) env('RATE_LIMIT_API', 120),
        'login' => (int) env('RATE_LIMIT_LOGIN', 5),
        'registration' => (int) env('RATE_LIMIT_REGISTRATION', 3),
        'recovery' => (int) env('RATE_LIMIT_RECOVERY', 3),
        'two-factor' => (int) env('RATE_LIMIT_TWO_FACTOR', 5),
        'verification-email' => (int) env('RATE_LIMIT_VERIFICATION_EMAIL', 3),
        'oauth' => (int) env('RATE_LIMIT_OAUTH', 20),
        'search' => (int) env('RATE_LIMIT_SEARCH', 30),
    ],
];
