-- Task 1: webapp_db schema
CREATE DATABASE IF NOT EXISTS webapp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webapp_db;

CREATE TABLE IF NOT EXISTS users (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100)  NOT NULL,
    email          VARCHAR(150)  NOT NULL UNIQUE,
    password_hash  VARCHAR(255)  NOT NULL,
    remember_token VARCHAR(64)   NULL,            -- hashed token for "Remember Me"
    created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)  NOT NULL,
    category    VARCHAR(80)   NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    stock       INT UNSIGNED  NOT NULL DEFAULT 0,
    image       VARCHAR(255)  NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_category (category)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED  NOT NULL,
    total       DECIMAL(10,2) NOT NULL,
    status      VARCHAR(20)   NOT NULL DEFAULT 'placed',
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Line items of each order (cart contents)
CREATE TABLE IF NOT EXISTS order_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id    INT UNSIGNED  NOT NULL,
    product_id  INT UNSIGNED  NOT NULL,
    quantity    INT UNSIGNED  NOT NULL,
    unit_price  DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_items_order   FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Sample data
INSERT INTO products (name, category, price, stock) VALUES
('Wireless Mouse', 'Electronics', 799.00, 50),
('Mechanical Keyboard', 'Electronics', 3499.00, 20),
('USB-C Cable', 'Accessories', 299.00, 100),
('Laptop Stand', 'Accessories', 1299.00, 35),
('Notebook A5', 'Stationery', 120.00, 200),
('Gel Pen Pack', 'Stationery', 90.00, 150),
('Bluetooth Speaker', 'Electronics', 2199.00, 25),
('Desk Lamp', 'Home', 999.00, 40),
('Water Bottle', 'Home', 450.00, 80),
('Backpack', 'Accessories', 1899.00, 30),
('Webcam HD', 'Electronics', 1999.00, 15),
('Sticky Notes', 'Stationery', 60.00, 300);
