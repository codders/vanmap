<?php
declare(strict_types=1);

namespace VanBaby;

class DataStore
{
  private object $conn;

  public function __construct(object $conn) {
    $this->conn = $conn;
  }

  public function saveLocation(GPSCoordinate $location): bool
  {
    $statement = $this->conn->prepare("INSERT INTO location (longitude, latitude, altitude) VALUES (?, ?, ?)");
    $longitude = $location->getLongitude();
    $latitude = $location->getLatitude();
    $altitude = $location->getAltitude();
    $statement->bind_param("ddd", $longitude, $latitude, $altitude);
    $result = $statement->execute();
    $statement->close();
    return $result;
  }

  private static function getConnection(): object
  {
    $conn = new \mysqli($_ENV["DATABASE_HOST"], $_ENV["DATABASE_USER"], $_ENV["DATABASE_PASSWORD"], $_ENV["DATABASE_NAME"]) or die("Connect failed %s\n". $conn -> error);
    return $conn;
  }

  public static function create(): DataStore
  {
    $conn = self::getConnection();
    return new DataStore($conn);
  }
}
