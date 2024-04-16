<?php

// config for RichanFongdasen/TursoLaravel
return [
    'sync_command' => [
        'script_filename' => 'turso-sync.mjs',
        'script_path'     => realpath(__DIR__ . '/..'),
        'timeout'         => 60,
    ],
];
