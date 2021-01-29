<?php
declare(strict_types=1);

include("VanBaby/GPSCoordinate.php");
use VanBaby\GPSCoordinate;

const TIME = "time";
const DATA = "data";

if (!array_key_exists(TIME, $_GET) || !array_key_exists(DATA, $_GET)) {
  echo("Not enough data - supply time and data" . PHP_EOL);
  exit(1);
}

function getTime(): string
{
  $timeval = $_GET[TIME];
  if (!is_numeric($timeval)) {
    echo("Time must be an int" . PHP_EOL);
    exit(1);
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
    echo("Invalid data string '" . $datastring . "' - must be even length");
    exit(1);
  }
  return GPSCoordinate::parseHexFloatTriple($datastring);
}

$time = getTime();
$data = getData();
$line = $time . "|" . (is_null($data) ? "No Signal" : $data->toString());
$res = file_put_contents("script.log", $line . "\n", FILE_APPEND);
print($line . "\n");
?>
