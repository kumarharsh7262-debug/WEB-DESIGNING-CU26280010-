-- Run as an admin user (root):  mysql -u root -p < database/schema.sql
CREATE DATABASE IF NOT EXISTS myproject_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myproject_db;

CREATE TABLE IF NOT EXISTS users (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username          VARCHAR(50)  NOT NULL,
    email             VARCHAR(100) NOT NULL UNIQUE,
    password_hash     VARCHAR(255) NOT NULL,
    failed_attempts   TINYINT UNSIGNED NOT NULL DEFAULT 0,      -- Q12
    locked_until      DATETIME NULL,                            -- Q12
    reset_token_hash  CHAR(64) NULL,                            -- Q13
    reset_expires     DATETIME NULL,                            -- Q13
    api_token_hash    CHAR(64) NULL,                            -- Q8
    api_token_expires DATETIME NULL,                            -- Q8
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reset (reset_token_hash),
    INDEX idx_api   (api_token_hash)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100)  NOT NULL,
    category     VARCHAR(50)   NOT NULL,
    price        DECIMAL(10,2) NOT NULL,
    stock        INT UNSIGNED  NOT NULL DEFAULT 0,
    supplier_url VARCHAR(255)  NULL,
    description  TEXT NULL,
    image        VARCHAR(40)   NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_category (category)
) ENGINE=InnoDB;
