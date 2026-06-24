-- ============================================================
-- Gaming Store — Cashier Module Database Schema
-- CIT6224 Web Application Development | Member 3 (Cashier)
-- ============================================================

CREATE DATABASE IF NOT EXISTS gaming_retail_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gaming_retail_store;

-- ============================================================
-- Products Table
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price       DECIMAL(10, 2) NOT NULL,
    stock       INT UNSIGNED NOT NULL DEFAULT 0,
    category    ENUM('mouse', 'keyboard', 'audio', 'collectibles', 'merchandise') NOT NULL,
    image_path  VARCHAR(512) DEFAULT '/assets/product-images/product-1.webp',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Orders Table
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number    VARCHAR(20)  NOT NULL UNIQUE,
    transaction_id  VARCHAR(30)  NOT NULL UNIQUE,
    session_id      VARCHAR(128) NOT NULL,
    -- Customer contact
    email           VARCHAR(255) NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    -- Delivery address
    first_name      VARCHAR(100) NOT NULL,
    last_name       VARCHAR(100) NOT NULL,
    address         VARCHAR(512) NOT NULL,
    address2        VARCHAR(512) DEFAULT NULL,
    postcode        VARCHAR(10)  NOT NULL,
    city            VARCHAR(100) NOT NULL,
    state           VARCHAR(100) NOT NULL,
    country         VARCHAR(100) NOT NULL DEFAULT 'Malaysia',
    -- Shipping
    shipping_method ENUM('ninjavan', 'spx_express', 'jnt_express') NOT NULL,
    tracking_id     VARCHAR(30)  NOT NULL,
    -- Payment
    payment_method  ENUM('credit_card', 'spaylater', 'atome') NOT NULL,
    -- Financials (server-calculated, never trusted from client)
    subtotal        DECIMAL(10, 2) NOT NULL,
    shipping_fee    DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total           DECIMAL(10, 2) NOT NULL,
    -- Status
    status          ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled')
                    NOT NULL DEFAULT 'confirmed',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Order Items Table
-- ============================================================
CREATE TABLE IF NOT EXISTS order_items (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        INT UNSIGNED NOT NULL,
    product_id      INT UNSIGNED NOT NULL,
    -- Snapshot of product at time of purchase (price may change)
    product_name    VARCHAR(255) NOT NULL,
    product_price   DECIMAL(10, 2) NOT NULL,
    quantity        INT UNSIGNED NOT NULL,
    line_total      DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_order  FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    CONSTRAINT fk_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- Sample Products (Gaming Peripherals & Merchandise)
-- ============================================================
INSERT IGNORE INTO products (name, description, price, stock, category, image_path) VALUES
('Logitech G Pro X2 Superstrike','PRO X2 SUPERSTRIKE is a breakthrough in ultra-low click-latency technology. The revolutionary Haptic Inductive Trigger System (HITS) accelerates click speed with tunable actuation and rapid trigger reset points for both main mouse keys.',                                 349.00, 30, 'mouse',        '/assets/product-images/product-1.webp'),
('Razer DeathAdder V4 Pro',   'Become the deadliest version of yourself with the Razer DeathAdder V4 Pro—an ultra-lightweight wireless ergonomic mouse that’s the perfect esports specimen.',                    299.00, 50, 'mouse',        '/assets/product-images/product-2.webp'),
('Razer BlackWidow V4 Pro',   'Mechanical gaming keyboard with Razer Green switches, RGB & macro keys.',         599.00, 25, 'keyboard',     '/assets/product-images/product-3.webp'),
('Keychron Q1 Pro QMK',       'Wireless custom mechanical keyboard, gasket mount, hot-swap.',                    549.00, 20, 'keyboard',     '/assets/product-images/product-4.webp'),
('HyperX Cloud Alpha S',      'Gaming headset with dual chamber drivers & 7.1 surround sound.',                  399.00, 40, 'audio',        '/assets/product-images/product-5.webp'),
('Razer BlackShark V2 Pro',   'Wireless esports headset with THX Spatial Audio.',                                449.00, 35, 'audio',        '/assets/product-images/product-6.webp'),
('Valorant Neon Agent Figure','Limited edition Neon resin figurine, 20cm tall.',                                  89.00, 15, 'collectibles', '/assets/product-images/product-7.webp'),
('CS2 Karambit Knife Replica','Decorative Karambit knife replica — collector piece, not sharpened.',             149.00, 10, 'collectibles', '/assets/product-images/product-8.webp'),
('Gamer Hoodie — Black L',    'Premium heavyweight cotton gaming hoodie with store emblem.',                      99.00, 60, 'merchandise',  '/assets/product-images/product-9.webp'),
('RGB Mouse Pad XL',          'Extended RGB mouse pad, 900×400mm, non-slip base.',                                79.00, 80, 'merchandise',  '/assets/product-images/product-10.webp');
