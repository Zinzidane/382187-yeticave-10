INSERT INTO category (NAME) VALUES	('Доски и лыжи');
INSERT INTO category (NAME) VALUES	('Крепления');
INSERT INTO category (NAME) VALUES	('Ботинки');
INSERT INTO category (NAME) VALUES	('Одежда');
INSERT INTO category (NAME) VALUES	('Инструменты');
INSERT INTO category (NAME) VALUES	('Разное');

INSERT INTO USER (email, NAME, PASSWORD, avatar, contact) VALUES ('test@test.com', 'Вася', '123456', 'img/avatar.jpg', '+375298008080');
INSERT INTO USER (email, NAME, PASSWORD, avatar, contact) VALUES ('vitya@test.com', 'Витя', '123456', 'img/avatar.jpg', '+375297008080');

INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (1, 1, '2014 Rossignol District Snowboard', 'img/lot-1.jpg', 10999, '2019-08-19', 500);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (1, 2, 'DC Ply Mens 2016/2017 Snowboard', 'img/lot-2.jpg', 159999, '2019-08-22', 1500);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (2, 2, 'Крепления Union Contact Pro 2015 года размер L/XL', 'img/lot-3.jpg', 8000, '2019-08-25', 500);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (3, 2, 'Ботинки для сноуборда DC Mutiny Charocal', 'img/lot-4.jpg', 10999, '2019-08-22', 500);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (4, 2, 'Куртка для сноуборда DC Mutiny Charocal', 'img/lot-5.jpg', 7500, '2019-08-23', 200);
INSERT INTO lot (category_id, user_id, title, image, initial_rate, date_close, rate_step) VALUES (6, 2, 'Маска Oakley Canopy', 'img/lot-6.jpg', 5400, '2019-08-19', 100);

INSERT INTO bet (lot_id, user_id, rate) VALUES (1, 2, 15000);
INSERT INTO bet (lot_id, user_id, rate) VALUES (1, 2, 17000);
INSERT INTO bet (lot_id, user_id, rate) VALUES (2, 1, 170000);

SELECT name FROM category;

SELECT lot.title, lot.initial_rate, lot.image FROM lot
WHERE lot.date_close > NOW() AND lot.winner_id IS NULL;

SELECT lot.id, lot.title, lot.initial_rate, lot.image, category.name AS category FROM lot
JOIN category ON lot.category_id = category.id
WHERE lot.id = 1;

UPDATE lot SET title = 'Новое название' WHERE id = 2;

SELECT bet.rate as rate FROM lot
JOIN bet ON lot.id = bet.lot_id
WHERE lot.id = 1
ORDER BY lot.date_add DESC;

