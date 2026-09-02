<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {

    $client = new MongoDB\Client("mongodb://127.0.0.1:27017");

    $database = $client->selectDatabase("waste_monitoring_system");

    echo "<h1 style='color:green;'>MongoDB Connected Successfully!</h1>";

    echo "<p>Database: waste_monitoring_system</p>";

} catch (Exception $e) {

    echo "<h1 style='color:red;'>MongoDB Connection Failed</h1>";

    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";

}

?>