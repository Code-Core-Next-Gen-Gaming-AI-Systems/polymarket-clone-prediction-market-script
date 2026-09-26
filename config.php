<?php
/**
 * ============================================================
 *  Polymarket Clone Script — Prediction Market Platform Engine
 *  Configuration File (config.php)
 * ------------------------------------------------------------
 *  Production-Ready WhiteLabel Engine
 *  100% Open Source · PHP 7.4+ · MySQL / MariaDB
 *
 *  Official Website & Full Documentation & Purchase:
 *  https://mintscripts.net/en/market/38-polymarket-clone-prediction-market-script.html
 *
 *  This material is the intellectual property of Code Core,
 *  distributed with technical support from Mint Scripts Technology Lab.
 *  © 2026 Code Core — Web3 & iGaming Architectural Engineering.
 *  Powered by Mint Scripts.
 * ============================================================
 */

declare(strict_types=1);

/* ------------------------------------------------------------
 |  1. DATABASE CONNECTION
 * ------------------------------------------------------------ */
return [

    'database' => [
        'driver'    => 'mysql',
        'host'      => getenv('DB_HOST') ?: '127.0.0.1',
        'port'      => getenv('DB_PORT') ?: 3306,
        'name'      => getenv('DB_NAME') ?: 'polymarket_clone',
        'user'      => getenv('DB_USER') ?: 'root',
        'password'  => getenv('DB_PASS') ?: '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'options'   => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ],
    ],

    /* ------------------------------------------------------------
     |  2. PLATFORM IDENTITY
     * ------------------------------------------------------------ */
    'platform' => [
        'name'          => 'Polymarket Clone Engine',
        'version'       => '1.0.0',
        'domain'        => 'https://your-domain.com',
        'locale'        => 'en',
        'locales'       => ['en', 'ru'],
        'timezone'      => 'UTC',
        'demo_mode'     => true,
        'maintenance'   => false,
        'vendor'        => 'Code Core',
        'support'       => 'Mint Scripts Technology Lab',
        'official_url'  => 'https://mintscripts.net/en/market/38-polymarket-clone-prediction-market-script.html',
    ],

    /* ------------------------------------------------------------
     |  3. SHARE PRICING & MATHEMATICAL CORE
     |  Formula:
     |     Share Price ($) = Outcome Probability (%) / 100
     |     Outcome Probability = (Volume on Outcome / Total Volume) × 100
     * ------------------------------------------------------------ */
    'pricing' => [
        'base_share_price'      => 0.01,   // min price in USD (1¢)
        'max_share_price'       => 0.99,   // max price in USD (99¢)
        'settlement_value'      => 1.00,   // winning share settles at $1
        'probability_precision' => 4,      // decimal places for probability
        'price_precision'       => 2,      // cents display precision
        'min_trade_amount'      => 1.00,   // min bet in USD
        'max_trade_amount'      => 10000.00,
        'allow_early_exit'      => true,
        'early_exit_window'     => 0,      // 0 = anytime before resolution
    ],

    /* ------------------------------------------------------------
     |  4. COMMISSION & MARGIN (MONETIZATION)
     * ------------------------------------------------------------ */
    'commission' => [
        'buy_fee_percent'       => 2.0,    // % of bet amount on purchase
        'sell_fee_percent'      => 2.0,    // % of revenue on early sale
        'settlement_fee_percent'=> 0.0,    // % on winning payout
        'withdrawal_fee_percent'=> 1.5,    // % on crypto withdrawal
        'deposit_fee_percent'   => 0.0,    // % on deposit (usually 0)
        'min_fee_usd'           => 0.01,
        'fee_wallet'            => 'platform_reserve',
    ],

    /* ------------------------------------------------------------
     |  5. HOUSE MARGIN & RISK CONTROL
     * ------------------------------------------------------------ */
    'risk' => [
        'house_edge_percent'    => 1.5,    // global house edge
        'max_exposure_per_market' => 50000.00,
        'max_payout_per_user'   => 25000.00,
        'auto_resolve_markets'  => true,
        'dispute_window_hours'  => 24,
        'liquidity_buffer'      => 0.05,   // 5% reserve buffer
        'stop_loss_threshold'   => 0.85,   // halt market at 85% imbalance
    ],

    /* ------------------------------------------------------------
     |  6. CURRENCY & EXCHANGE RATES
     * ------------------------------------------------------------ */
    'currency' => [
        'base'      => 'USD',
        'display'   => ['USD', 'USDT', 'BTC', 'ETH', 'TON'],
        'rates'     => [
            'USD'  => 1.00,
            'USDT' => 1.00,
            'BTC'  => 0.000015,   // 1 USD ≈ 0.000015 BTC (update live)
            'ETH'  => 0.00028,    // 1 USD ≈ 0.00028 ETH
            'TON'  => 0.15,       // 1 USD ≈ 0.15 TON
        ],
        'auto_update_rates' => true,
        'rate_api_endpoint' => 'https://api.coingecko.com/api/v3/simple/price',
        'rate_refresh_sec'  => 60,
    ],

    /* ------------------------------------------------------------
     |  7. CRYPTO GATEWAYS
     * ------------------------------------------------------------ */
    'gateways' => [
        'cryptocloud' => [
            'enabled'   => true,
            'api_key'   => getenv('CRYPTOCLOUD_API_KEY') ?: '',
            'shop_id'   => getenv('CRYPTOCLOUD_SHOP_ID') ?: '',
            'currencies'=> ['USDT', 'BTC', 'ETH', 'TON'],
        ],
        'nowpayments' => [
            'enabled'   => false,
            'api_key'   => getenv('NOWPAYMENTS_API_KEY') ?: '',
            'ipn_secret'=> getenv('NOWPAYMENTS_IPN_SECRET') ?: '',
        ],
    ],

    /* ------------------------------------------------------------
     |  8. SECURITY
     * ------------------------------------------------------------ */
    'security' => [
        'session_lifetime'  => 86400,
        'csrf_enabled'      => true,
        'rate_limit_per_min'=> 120,
        'max_login_attempts'=> 5,
        'lockout_minutes'   => 15,
        'password_algo'     => PASSWORD_ARGON2ID,
        'force_https'       => true,
    ],

    /* ------------------------------------------------------------
     |  9. DEMO ACCOUNT (SANDBOX)
     * ------------------------------------------------------------ */
    'demo' => [
        'enabled'         => true,
        'starting_balance'=> 50000.00,
        'reset_daily'     => true,
        'allow_real_fees' => false,
    ],

    /* ------------------------------------------------------------
     |  10. LIVE BET ANIMATION (FOMO SIMULATION)
     * ------------------------------------------------------------ */
    'live_animation' => [
        'enabled'         => true,
        'min_bet'         => 5.00,
        'max_bet'         => 500.00,
        'interval_sec'    => [3, 12],
        'green_color'     => '#22c55e',
        'red_color'       => '#ef4444',
    ],

    /* ------------------------------------------------------------
     |  11. FEATURE FLAGS
     * ------------------------------------------------------------ */
    'features' => [
        'multilingual'      => true,
        'comments'          => true,
        'holder_badges'     => true,
        'tournaments'       => false,
        'referral_program'  => true,
        'email_verification'=> true,
        'two_factor_auth'   => false,
    ],
];
