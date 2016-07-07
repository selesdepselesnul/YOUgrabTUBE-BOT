DROP DATABASE IF EXISTS yougrabtube;
CREATE DATABASE yougrabtube;

USE yougrabtube;

DROP TABLE IF EXISTS user_message;
CREATE TABLE user_message (
	id BIGINT,
	message LONGTEXT
);

DROP TABLE IF EXISTS bot_message;
CREATE TABLE bot_message (
	id BIGINT,
	message LONGTEXT
);

INSERT INTO user_message
VALUES(-999, 'hello world');

INSERT INTO bot_message
VALUES(-999, 'hello world');