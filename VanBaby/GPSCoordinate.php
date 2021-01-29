<?php
declare(strict_types=1);

namespace VanBaby;

class GPSCoordinate
{
  private float $latitude;
  private float $longitude;
  private float $altitude;

  public function toString(): string
  {
    return $this->getLatitude() . "," . $this->getLongitude();
  }

  private static function hex2float(string $hex): float
  {
    $dec = hexdec($hex);

    if ($dec === 0) {
        return 0;
    }

    $sup = 1 << 23;
    $x = ($dec & ($sup - 1)) + $sup * ($dec >> 31 | 1);
    $exp = ($dec >> 23 & 0xFF) - 127;
    $sign = ($dec & 0x80000000) ? -1 : 1;

    return $sign * $x * pow(2, $exp - 23);
  }

  public function setLatitude(float $latitude): self
  {
    $this->latitude = $latitude;
    return $this;
  }

  public function getLatitude(): float
  {
    return $this->latitude;
  }

  public function setLongitude(float $longitude): self
  {
    $this->longitude = $longitude;
    return $this;
  }

  public function getLongitude(): float
  {
    return $this->longitude;
  }

  public function setAltitude(float $altitude): self
  {
    $this->altitude = $altitude;
    return $this;
  }

  public function getAltitude(): float
  {
    return $this->altitude;
  }

  public static function parseHexFloatTriple(string $hexString): ?GPSCoordinate
  {
    if (strlen($hexString) !== 24) {
      trigger_error("GPS String should be 24 characters - got " . strlen($hexString), E_USER_NOTICE);
      return null;
    }
    $latitude = substr($hexString, 0, 8);
    $longitude = substr($hexString, 8, 8);
    $altitude = substr($hexString, 16, 8);
    return (new GPSCoordinate())
      ->setLongitude(self::hex2float($longitude))
      ->setLatitude(self::hex2float($latitude))
      ->setAltitude(self::hex2float($altitude));
  }
}
