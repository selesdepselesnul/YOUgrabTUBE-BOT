DROP DATABASE IF EXISTS yougrabtube;
CREATE DATABASE yougrabtube;

USE yougrabtube;

DROP TABLE IF EXISTS user_message;
CREATE TABLE user_message (
	no TINYINT PRIMARY KEY,
	id BIGINT,
	message LONGTEXT
);

DROP TABLE IF EXISTS bot_message;
CREATE TABLE bot_message (
	no TINYINT PRIMARY KEY,
	id BIGINT,
	message LONGTEXT
);

INSERT INTO user_message
VALUES(0, -999, 'hello world');

INSERT INTO bot_message
VALUES(0, -999, 'hello world');