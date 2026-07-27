<?php
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    unlink($envFile);
    echo ".env file successfully deleted.\n";
} else {
    echo "No .env file found to delete.\n";
}
