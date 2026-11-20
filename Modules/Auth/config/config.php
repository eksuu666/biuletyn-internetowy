<?php

return [
    'name' => 'Auth',
    'registration_enabled' => env('REGISTRATION_ENABLED', true),
    'email_verification_required' => env('EMAIL_VERIFICATION_REQUIRED', false),
];
