DROP DATABASE IF EXISTS yougrabtube;
CREATE DATABASE yougrabtube;

USE yougrabtube;

DROP TABLE IF EXISTS message;
CREATE TABLE last_user_message (
	id BIGINT
);

INSERT INTO last_user_message VALUES(-999);