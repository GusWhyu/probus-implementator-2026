<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$types = \App\Models\User::select('usertype')->distinct()->pluck('usertype')->toArray();
echo "USERTYPES:\n";
print_r($types);
