<?php

// Clear the application cache
$output = shell_exec('php ../artisan cache:clear');
echo "<pre>$output</pre>";

// Clear the config cache
$output = shell_exec('php ../artisan config:clear');
echo "<pre>$output</pre>";

// Clear the route cache
$output = shell_exec('php ../artisan route:clear');
echo "<pre>$output</pre>";

// Clear the view cache
$output = shell_exec('php ../artisan view:clear');
echo "<pre>$output</pre>";

echo "Cache cleared!";
?>
