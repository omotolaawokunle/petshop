<?php

return [
    'key' => [
        'public' => file_get_contents(storage_path(env('JWT_PUBLIC_KEY'))),
        'private' => file_get_contents(storage_path(env('JWT_PRIVATE_KEY'))),
    ]
];
