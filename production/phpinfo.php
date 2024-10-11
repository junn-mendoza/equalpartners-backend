<?php
phpinfo();

echo 'Application cache, config, view, and route cache cleared! 1111';
// Include the Composer autoload
require __DIR__.'/vendor/autoload.php';

echo 'Application cache, config, view, and route cache cleared! 555';

// Boot the application
$app = require_once __DIR__.'/bootstrap/app.php';
echo 'Application cache, config, view, and route cache cleared! 333';
// Create an instance of the console kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo 'Application cache, config, view, and route cache cleared! 444';
// Run the Artisan commands
$kernel->bootstrap();
$kernel->call('cache:clear');
$kernel->call('config:clear');
$kernel->call('view:clear');
$kernel->call('route:clear');

echo 'Application cache, config, view, and route cache cleared! 333';

