CREATE DATABASE IF NOT EXISTS doingsdone_NoSpooksAllowed;

USE doingsdone_NoSpooksAllowed;

CREATE TABLE IF NOT EXISTS users
(
  id                INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name              VARCHAR(70)  NOT NULL,
  email             VARCHAR(255) NOT NULL,
  password          VARCHAR(60)  NOT NULL,
  registration_time DATETIME     NOT NULL
);

CREATE TABLE IF NOT EXISTS projects
(
  id      INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name    VARCHAR(70)  NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS tasks
(
  id            INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name          VARCHAR(255) NOT NULL,
  creation_time DATETIME     NOT NULL,
  status        BOOLEAN      NOT NULL DEFAULT False,
  file          VARCHAR(255),
  end_time      DATE                  DEFAULT NULL,
  user_id       INT UNSIGNED NOT NULL,
  project_id    INT UNSIGNED NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id),
  FOREIGN KEY (project_id) REFERENCES projects (id),
  INDEX status (status)
);
