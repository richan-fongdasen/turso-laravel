<?php

// config for RichanFongdasen/TursoLaravel
return [
    'sync_command' => [
        'node_path'       => env('NODE_PATH'), // Full path to the node executable. E.g: /usr/bin/node
        'script_filename' => 'turso-sync.mjs',
        'script_path'     => realpath(__DIR__ . '/..'),
        'timeout'         => 60,
    ],
];
