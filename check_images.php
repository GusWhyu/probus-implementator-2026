<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ticket = \App\Models\Ticket::where('ticket_number', 'TCK-202610-0032')->first();
if ($ticket) {
    echo "Found Ticket ID: " . $ticket->id . "\n";
    $images = \App\Models\DataImage::where('ticket_id', $ticket->id)->get();
    echo "Total Images: " . $images->count() . "\n";
    foreach ($images as $img) {
        echo "- Image ID: " . $img->id . ", Filename: " . $img->image . "\n";
    }
} else {
    echo "Ticket not found.\n";
}
