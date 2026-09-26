<?php
/**
 * ============================================================
 *  Prediction Market — Trade Settlement (settlement.php)
 * ------------------------------------------------------------
 *  Handles atomic MySQL transactions for balance protection.
 *
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

final class Settlement
{
    private PDO $db;
    private array $cfg;

    public function __construct(PDO $db, array $config)
    {
        $this->db  = $db;
        $this->cfg = $config;
    }

    /**
     * Atomic trade placement with balance lock.
     */
    public function placeTrade(int $userId, int $marketId, string $outcome, float $amount): array
    {
        $this->db->beginTransaction();
        try {
            /* Lock user balance */
            $stmt = $this->db->prepare(
                'SELECT balance FROM users WHERE id = ? FOR UPDATE'
            );
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user || $user['balance'] < $amount) {
                throw new RuntimeException('Insufficient balance');
            }

            /* Apply buy commission */
            $feePercent = (float) $this->cfg['commission']['buy_fee_percent'];
            $fee        = $amount * ($feePercent / 100.0);
            $net        = $amount - $fee;

            /* Debit user */
            $this->db->prepare('UPDATE users SET balance = balance - ? WHERE id = ?')
                     ->execute([$amount, $userId]);

            /* Record trade */
            $this->db->prepare(
                'INSERT INTO trades (user_id, market_id, outcome, amount, fee, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())'
            )->execute([$userId, $marketId, $outcome, $net, $fee]);

            /* Credit platform reserve */
            $this->db->prepare(
                'INSERT INTO platform_ledger (type, amount, meta, created_at)
                 VALUES ("commission", ?, ?, NOW())'
            )->execute([$fee, json_encode(['market' => $marketId])]);

            $this->db->commit();

            return ['status' => 'ok', 'net' => $net, 'fee' => $fee];
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Settle a market — pay winners at $1 per share.
     */
    public function settleMarket(int $marketId, string $winningOutcome): void
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'SELECT user_id, outcome, shares FROM positions WHERE market_id = ?'
            );
            $stmt->execute([$marketId]);
            $positions = $stmt->fetchAll();

            $settlementValue = (float) $this->cfg['pricing']['settlement_value'];

            foreach ($positions as $pos) {
                if ($pos['outcome'] === $winningOutcome) {
                    $payout = (float) $pos['shares'] * $settlementValue;
                    $this->db->prepare('UPDATE users SET balance = balance + ? WHERE id = ?')
                             ->execute([$payout, $pos['user_id']]);
                }
            }

            $this->db->prepare('UPDATE markets SET status = "resolved", winner = ? WHERE id = ?')
                     ->execute([$winningOutcome, $marketId]);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
