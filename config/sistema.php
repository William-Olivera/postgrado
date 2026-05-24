<?php
return [
    'backup_path' => env('BACKUP_PATH', storage_path('app/backups')),
    'max_file_size_mb' => 5,
    'allowed_extensions' => ['pdf', 'jpg', 'png'],
    'horario_atencion' => ['07:00', '21:00'],
];
