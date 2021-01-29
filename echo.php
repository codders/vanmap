<?php
$time = gmdate("Ymd\TH:i:s\Z", $_GET["time"]);
$hex_data = hex2bin($_GET["data"]);
$data = $_GET["data"];
if (strpos("No Signal", $hex_data) === 0) {
	$data = "No Signal";
}
$line = $time . "|" . $data;
$res = file_put_contents("script.log", $line . "\n", FILE_APPEND);
print($line . "\n");
?>
