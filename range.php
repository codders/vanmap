<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use VanBaby\DataStore;
use VanBaby\GPSCoordinate;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$store = DataStore::create();

header('Content-Type: application/json');
echo json_encode($store->getDateRange());
