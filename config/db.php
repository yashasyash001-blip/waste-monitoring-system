<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {

    $client = new MongoDB\Client("mongodb://127.0.0.1:27017");

    $database = $client->selectDatabase("waste_monitoring_system");

} catch (Exception $e) {

    die("MongoDB connection failed: " . $e->getMessage());

}

?>