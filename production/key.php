<?php

// Clear the application cache
$output = shell_exec('php ../artisan key:generate');
echo "<pre>$output</pre>";


?>
