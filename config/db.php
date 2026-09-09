<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {

    $uri = getenv('MONGODB_URI');

    if (!$uri) {
        die("MONGODB_URI environment variable is not set.");
    }

    $client = new MongoDB\Client($uri);

    // Force MongoDB connection/authentication now
    $client->selectDatabase("waste_monitoring_system")
           ->command(["ping" => 1]);

    $database = $client->selectDatabase("waste_monitoring_system");

} catch (\Throwable $e) {

    error_log("MONGODB CONNECTION ERROR: " . $e->getMessage());

    die(
        "MongoDB connection failed. Please check the MongoDB credentials and connection string."
    );
}

?>