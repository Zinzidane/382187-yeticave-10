CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE category (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);

CREATE TABLE user (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  date_add DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(128) NOT NULL,
  name VARCHAR(255) NOT NULL,
  password VARCHAR(64) NOT NULL,
  avatar VARCHAR(255),
  contact TEXT
);

CREATE TABLE lot (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  winner_id INT UNSIGNED,
  date_add DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_close DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  title VARCHAR(255) NOT NULL,
  description VARCHAR(255),
  image VARCHAR(255),
  initial_rate INT UNSIGNED NOT NULL,
  rate_step INT UNSIGNED NOT NULL,
  CONSTRAINT fk_lot_category_id FOREIGN KEY (category_id) REFERENCES category (id),
  CONSTRAINT fk_lot_user_id FOREIGN KEY (user_id) REFERENCES USER (id),
  CONSTRAINT fk_lot_winner_id FOREIGN KEY (winner_id) REFERENCES USER (id)
);

CREATE TABLE bet (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  lot_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  date_add DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  rate INT UNSIGNED NOT NULL,
  CONSTRAINT fk_bet_lot_id FOREIGN KEY (lot_id) REFERENCES lot (id),
  CONSTRAINT fk_bet_user_id FOREIGN KEY (user_id) REFERENCES USER (id)
);

CREATE INDEX title ON lot(title);
CREATE INDEX description ON lot(description);
CREATE UNIQUE INDEX email ON user(email);
