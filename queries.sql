INSERT INTO category (NAME, symbol_code) VALUES	('Доски и лыжи', 'boards');
INSERT INTO category (NAME, symbol_code) VALUES	('Крепления', 'attachment');
INSERT INTO category (NAME, symbol_code) VALUES	('Ботинки', 'boots');
INSERT INTO category (NAME, symbol_code) VALUES	('Одежда', 'clothing');
INSERT INTO category (NAME, symbol_code) VALUES	('Инструменты', 'tools');
INSERT INTO category (NAME, symbol_code) VALUES	('Разное', 'other');

INSERT INTO USER (email, NAME, PASSWORD, avatar, contact) VALUES ('test@test.com', 'Вася', '123456', 'img/avatar.jpg', '+375298008080');
INSERT INTO USER (email, NAME, PASSWORD, avatar, contact) VALUES ('vitya@test.com', 'Витя', '123456', 'img/avatar.jpg', '+375297008080');

SET @FIRST_USER_ID = (SELECT id FROM user WHERE email = 'test@test.com');
SET @SECOND_USER_ID = (SELECT id FROM user WHERE email = 'vitya@test.com');

INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (1, @FIRST_USER_ID, '2014 Rossignol District Snowboard', 'img/lot-1.jpg', 10999, '2019-09-25', 500);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (1, @SECOND_USER_ID, 'DC Ply Mens 2016/2017 Snowboard', 'img/lot-2.jpg', 159999, '2019-09-26', 1500);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (2, @SECOND_USER_ID, 'Крепления Union Contact Pro 2015 года размер L/XL', 'img/lot-3.jpg', 8000, '2019-09-27', 500);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (3, @SECOND_USER_ID, 'Ботинки для сноуборда DC Mutiny Charocal', 'img/lot-4.jpg', 10999, '2019-09-25', 500);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (4, @SECOND_USER_ID, 'Куртка для сноуборда DC Mutiny Charocal', 'img/lot-5.jpg', 7500, '2019-09-23', 200);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (6, @SECOND_USER_ID, 'Маска Oakley Canopy', 'img/lot-6.jpg', 5400, '2019-09-26', 100);

INSERT INTO bet (lot_id, user_id, rate) VALUES (1, @SECOND_USER_ID, 15000);
INSERT INTO bet (lot_id, user_id, rate) VALUES (1, @SECOND_USER_ID, 17000);
INSERT INTO bet (lot_id, user_id, rate) VALUES (2, @FIRST_USER_ID, 170000);

SELECT name FROM category;

SELECT lot.title, lot.initial_rate, lot.image, category.name AS category, MAX(bet.rate) AS rate, COUNT(bet.lot_id) AS bets FROM lot
JOIN category ON lot.category_id = category.id
JOIN bet ON lot.id = bet.lot_id
WHERE lot.date_close > NOW() AND lot.winner_id IS NULL
GROUP BY lot.id
ORDER BY lot.date_add DESC;

SELECT lot.id, lot.title, lot.initial_rate, lot.image, category.name AS category FROM lot
JOIN category ON lot.category_id = category.id
WHERE lot.id = 1;

UPDATE lot SET title = 'Новое название' WHERE id = 2;

SELECT bet.rate as rate, bet.date_add as date_add, user.name as user FROM lot
JOIN bet ON lot.id = bet.lot_id
JOIN user on user.id = bet.user_id
WHERE lot.id = 1
ORDER BY lot.date_add DESC;

