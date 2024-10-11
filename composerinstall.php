<?php

// Run the composer install command
$output = [];
$returnVar = 0;

exec('composer install 2>&1', $output, $returnVar);

// Check if the command was successful
if ($returnVar === 0) {
    echo "Composer install completed successfully." . PHP_EOL;
    // Output the command result
    echo implode(PHP_EOL, $output);
} else {
    echo "Error running composer install." . PHP_EOL;
    // Output the error message
    echo implode(PHP_EOL, $output);
}
?>