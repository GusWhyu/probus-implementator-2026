<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\DB::statement('ALTER TABLE dataimage ADD COLUMN request_id BIGINT UNSIGNED NULL AFTER ticket_id;');
    \Illuminate\Support\Facades\DB::statement('ALTER TABLE dataimage ADD CONSTRAINT dataimage_request_id_foreign FOREIGN KEY (request_id) REFERENCES request(id) ON DELETE CASCADE;');
    echo "Successfully added request_id column and foreign key constraint.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
