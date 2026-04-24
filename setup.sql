CREATE DATABASE IF NOT EXISTS getip DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

USE getip;

CREATE TABLE IF NOT EXISTS access_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45) NOT NULL,
    country VARCHAR(2) DEFAULT NULL,
    mode VARCHAR(10) DEFAULT 'simple',
    user_agent TEXT,
    request_uri VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_ip (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
