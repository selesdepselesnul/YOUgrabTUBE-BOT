DROP DATABASE IF EXISTS yougrabtube;
CREATE DATABASE yougrabtube;

USE yougrabtube;

DROP TABLE IF EXISTS last_message_id;
CREATE TABLE last_message_id (
	user BIGINT,
	bot BIGINT
);

INSERT INTO last_message_id
VALUES(-999, -999);