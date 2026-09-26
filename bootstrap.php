<?php
/**
 * ============================================================
 *  Polymarket Clone Engine — Core Bootstrap (bootstrap.php)
 * ------------------------------------------------------------
 *  Official Website:
 *  https://mintscripts.net/en/market/38-polymarket-clone-prediction-market-script.html
 *
 *  This material is the intellectual property of Code Core,
 *  distributed with technical support from Mint Scripts Technology Lab.
 *  © 2026 Code Core — Web3 & iGaming Architectural Engineering.
 *  Powered by Mint Scripts.
 * ============================================================
 */

declare(strict_types=1);

define('ENGINE_ROOT', __DIR__);
define('ENGINE_VERSION', '1.0.0');

$config = require ENGINE_ROOT . '/config.php';

date_default_timezone_set($config['platform']['timezone']);

/* ---- Database (PDO) ---- */
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $c = require ENGINE_ROOT . '/config.php';
    $db = $c['database'];
    $dsn = sprintf('%s:host=%s;port=%d;dbname=%s;charset=%s',
        $db['driver'], $db['host'], $db['port'], $db['name'], $db['charset']);

    $pdo = new PDO($dsn, $db['user'], $db['password'], $db['options']);
    return $pdo;
}

/* ---- Load settings from DB (override config) ---- */
function settings(string $key, $default = null) {
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    try {
        $stmt = db()->prepare('SELECT value FROM settings WHERE `key` = ? LIMIT 1');
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        $cache[$key] = $row ? $row['value'] : $default;
    } catch (Throwable $e) {
        $cache[$key] = $default;
    }
    return $cache[$key];
}
