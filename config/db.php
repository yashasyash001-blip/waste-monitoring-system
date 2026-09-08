<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {

    $uri = getenv('MONGODB_URI');

    if (!$uri) {
        die("MONGODB_URI environment variable is not set.");
    }

    $client = new MongoDB\Client($uri);

    $database = $client->selectDatabase("waste_monitoring_system");

} catch (Exception $e) {
    die("MongoDB connection failed: " . $e->getMessage());
}

?>