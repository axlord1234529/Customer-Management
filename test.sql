CREATE DATABASE `test`
CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `test`;

CREATE TABLE customers (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE billing_addresses (
  id INT NOT NULL AUTO_INCREMENT,
  customer_id INT NOT NULL,
  address VARCHAR(255) NOT NULL,
  tax_number VARCHAR(255),
  is_default BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (id),
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE TABLE shipping_addresses (
  id INT NOT NULL AUTO_INCREMENT,
  customer_id INT NOT NULL,
  address VARCHAR(255) NOT NULL,
  is_default BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (id),
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);
