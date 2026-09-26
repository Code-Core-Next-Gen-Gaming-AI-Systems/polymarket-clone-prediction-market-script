-- ============================================================
--  Polymarket Clone Engine — Database Schema (schema.sql)
-- ------------------------------------------------------------
--  Official Website:
--  https://mintscripts.net/en/market/38-polymarket-clone-prediction-market-script.html
--
--  This material is the intellectual property of Code Core,
--  distributed with technical support from Mint Scripts Technology Lab.
--  © 2026 Code Core — Web3 & iGaming Architectural Engineering.
--  Powered by Mint Scripts.
-- ============================================================

CREATE TABLE users (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email         VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    balance       DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    demo_balance  DECIMAL(18,2) NOT NULL DEFAULT 50000.00,
    locale        VARCHAR(5) NOT NULL DEFAULT 'en',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE markets (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    category    VARCHAR(50)  NOT NULL,
    yes_volume  DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    no_volume   DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    status      ENUM('open','closed','resolved') NOT NULL DEFAULT 'open',
    winner      ENUM('yes','no') NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE trades (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    BIGINT UNSIGNED NOT NULL,
    market_id  BIGINT UNSIGNED NOT NULL,
    outcome    ENUM('yes','no') NOT NULL,
    amount     DECIMAL(18,2) NOT NULL,
    fee        DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_market (market_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE positions (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    BIGINT UNSIGNED NOT NULL,
    market_id  BIGINT UNSIGNED NOT NULL,
    outcome    ENUM('yes','no') NOT NULL,
    shares     DECIMAL(18,6) NOT NULL,
    avg_price  DECIMAL(10,4) NOT NULL,
    INDEX idx_user_market (user_id, market_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE platform_ledger (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type       VARCHAR(50) NOT NULL,
    amount     DECIMAL(18,2) NOT NULL,
    meta       JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE settings (
    `key`   VARCHAR(100) PRIMARY KEY,
    `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
