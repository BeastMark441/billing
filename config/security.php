<?php

return [
    'super_admin_user_ids' => array_values(array_filter(array_map('trim', explode(',', (string) env('SUPER_ADMIN_USER_IDS', ''))))),
    'super_admin_emails' => array_values(array_filter(array_map('trim', explode(',', (string) env('SUPER_ADMIN_EMAILS', ''))))),
];
