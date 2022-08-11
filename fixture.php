<?php
$pdo = require 'db.php';

$pdo->exec('CREATE TABLE users (
    uuid VARCHAR(36) NOT NULL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL
)');

$pdo->exec('CREATE TABLE posts (
    uuid VARCHAR(36) NOT NULL PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    text TEXT NOT NULL,
    user_uuid VARCHAR(36) NOT NULL,
    FOREIGN KEY (user_uuid) REFERENCES users(uuid)
)');

$pdo->exec('CREATE TABLE comments (
    uuid VARCHAR(36) NOT NULL PRIMARY KEY,
    txt TEXT NOT NULL,
    user_uuid VARCHAR(36) NOT NULL,
    post_uuid VARCHAR(36) NOT NULL,
    FOREIGN KEY (user_uuid) REFERENCES users(uuid)
    FOREIGN KEY (post_uuid) REFERENCES posts(uuid)
)');