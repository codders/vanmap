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

  public function getLocationHistory(?string $minDate = null, ?string $maxDate = null): array
  {
    $queryString = "SELECT longitude, latitude, altitude, created_at FROM location";
    if (is_string($minDate) === true || is_string($maxDate) === true) {
      $queryString .= " WHERE";
    }
    if (is_string($minDate) === true) {
      $queryString .= " created_at >= ?";
    }
    if (is_string($minDate) === true && is_string($maxDate) === true) {
      $queryString .= " AND";
    }
    if (is_string($maxDate) === true) {
      $queryString .= " created_at <= ?";
    }
    $queryString .= " ORDER BY created_at DESC";
    $statement = $this->conn->prepare($queryString);
    if (is_string($minDate) === true && is_string($maxDate) === true) {
      $statement->bind_param("ss", $minDate, $maxDate);
    } else if (is_string($minDate) === true) {
      $statement->bind_param("s", $minDate);
    } else if (is_string($maxDate) === true) {
      $statement->bind_param("s", $maxDate);
    }
    $statement->execute();
    $longitude = null;
    $latitude = null;
    $altitude = null;
    $createdAt = null;
    $statement->bind_result($longitude, $latitude, $altitude, $createdAt);
    $locations = [];
    while ($obj = $statement->fetch()) {
      $locations []= GPSCoordinate::createFromHash([
        "longitude" => $longitude,
        "latitude" => $latitude,
        "altitude" => $altitude
      ]);
    }
    $statement->close();
    return $locations;
  }

  public function getDateRange(): array
  {
    $range = $this->conn->query("SELECT MIN(created_at) as lower, MAX(created_at) as upper FROM location");
    if (($obj = $range->fetch_assoc()) !== false) {
      return $obj;
    }
    return [];
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
