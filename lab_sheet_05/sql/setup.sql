-- Optional: create everything by hand (Q4 does the same from Python)
CREATE DATABASE IF NOT EXISTS python_web_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE python_web_db;

CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  age           INT NULL,
  city          VARCHAR(100) NULL,
  password_hash VARCHAR(255) NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Used by Q18 (session handling)
CREATE TABLE IF NOT EXISTS sessions (
  token      CHAR(64) PRIMARY KEY,
  user_id    INT NOT NULL,
  expires_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
