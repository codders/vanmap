<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use VanBaby\DataStore;
use VanBaby\GPSCoordinate;

const TIME = "time";
const DATA = "data";
const SIGFOX_HEADER = "x-sigfox-key";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$headers = getallheaders();

if (
  !array_key_exists(SIGFOX_HEADER, $headers) ||
  $headers[SIGFOX_HEADER] != $_ENV["SIGFOX_CALLBACK_KEY"]
) {
  echo("request was not authenticated");
  exit(1);
}

if (!array_key_exists(TIME, $_GET) || !array_key_exists(DATA, $_GET)) {
  echo("Not enough data - supply time and data" . PHP_EOL);
  exit(1);
}

function getTime(): string
{
  $timeval = $_GET[TIME];
  if (!is_numeric($timeval)) {
    die("Time must be an int");
  }
  return gmdate("Ymd\TH:i:s\Z", (int)$timeval);
}

function getData(): ?GPSCoordinate
{
  $datastring = $_GET[DATA];
  if ("No Signal" === $datastring) {
    return null;
  }
  if (strlen($datastring) % 2 !== 0) {
    die("Invalid data string '" . $datastring . "' - must be even length");
  }
  return GPSCoordinate::parseHexFloatTriple($datastring);
}

$time = getTime();
$line = $time . "|" . $_GET[DATA];
file_put_contents("script.log", $line . "\n", FILE_APPEND);
$store = DataStore::create();
$data = getData();
if (!is_null($data)) {
  $store->saveLocation($data);
}
print($line . "\n");
?>
