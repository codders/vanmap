CREATE TABLE location (
  id INT NOT NULL AUTO_INCREMENT, 
  latitude FLOAT NOT NULL,
  longitude FLOAT NOT NULL,
  altitude FLOAT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(`id`),
  KEY `created_at` (`created_at`)
);
