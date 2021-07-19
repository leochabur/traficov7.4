CREATE SCHEMA ontuts_doctrine CHARSET UTF8 COLLATE utf8_general_ci;
use ontuts_doctrine;

CREATE TABLE users(
	id INT(11) PRIMARY KEY auto_increment,
	name VARCHAR(30),
	email VARCHAR(60)
)ENGINE=INNODB;

CREATE TABLE users_comments(
	id INT(11) PRIMARY KEY auto_increment,
	id_user INT(11) NOT NULL,
	text VARCHAR(255),
	CONSTRAINT fk_users_comments_users FOREIGN KEY (id_user) REFERENCES users(id)
)ENGINE=INNODB;

