-- Recharge App Database Schema
-- Version: 1.0.0

CREATE DATABASE IF NOT EXISTS recharge_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recharge_app;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mobile VARCHAR(15) NOT NULL UNIQUE,
    name VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_mobile (mobile)
) ENGINE=InnoDB;

-- Operators Table
CREATE TABLE operators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    logo VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Plans Table
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operator_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    validity VARCHAR(50),
    description TEXT,
    data VARCHAR(50),
    calls VARCHAR(50),
    sms VARCHAR(50),
    category ENUM('popular', 'unlimited', 'data', 'talktime') DEFAULT 'popular',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operator_id) REFERENCES operators(id),
    INDEX idx_operator (operator_id),
    INDEX idx_category (category),
    INDEX idx_amount (amount),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    operator_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'success', 'failed') DEFAULT 'pending',
    reference_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (plan_id) REFERENCES plans(id),
    FOREIGN KEY (operator_id) REFERENCES operators(id),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Payments Table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(50),
    transaction_id VARCHAR(100),
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    upi_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    INDEX idx_order (order_id),
    INDEX idx_transaction (transaction_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Transactions Table
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_id INT,
    type ENUM('debit', 'credit', 'refund') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (payment_id) REFERENCES payments(id),
    INDEX idx_order (order_id),
    INDEX idx_type (type)
) ENGINE=InnoDB;

-- Settings Table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB;

-- Logs Table
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level ENUM('info', 'warning', 'error', 'debug') NOT NULL,
    message TEXT NOT NULL,
    context JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_level (level),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Insert Default Operators
INSERT INTO operators (name, code, logo, is_active) VALUES
('Jio', 'jio', 'assets/icons/jio.svg', 1),
('Airtel', 'airtel', 'assets/icons/airtel.svg', 1),
('Vi (Vodafone Idea)', 'vi', 'assets/icons/vi.svg', 1),
('BSNL', 'bsnl', 'assets/icons/bsnl.svg', 1);

-- Insert Default Settings
INSERT INTO settings (setting_key, setting_value) VALUES
('app_name', 'Recharge App'),
('app_version', '1.0.0'),
('maintenance_mode', '0'),
('min_recharge', '10'),
('max_recharge', '10000');
