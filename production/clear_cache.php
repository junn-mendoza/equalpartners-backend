<?php
// Include the Composer autoload
require __DIR__.'/vendor/autoload.php';

// Boot the application
$app = require_once __DIR__.'/bootstrap/app.php';

// Create an instance of the console kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run the Artisan commands
$kernel->bootstrap();
$kernel->call('cache:clear');
$kernel->call('config:clear');
$kernel->call('view:clear');
$kernel->call('route:clear');

echo 'Application cache, config, view, and route cache cleared!';
