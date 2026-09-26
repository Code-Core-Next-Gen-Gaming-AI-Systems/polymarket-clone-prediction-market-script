<?php
/**
 * ============================================================
 *  Prediction Market — Share Pricing Engine (pricing.php)
 * ------------------------------------------------------------
 *  Implements the core mathematical model:
 *     Share Price ($)      = Outcome Probability (%) / 100
 *     Outcome Probability  = (Volume / Total Volume) × 100
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

final class PricingEngine
{
    private array $cfg;

    public function __construct(array $config)
    {
        $this->cfg = $config['pricing'];
    }

    /**
     * Compute probability and share price for a given outcome.
     *
     * @param float $outcomeVolume  Volume placed on this outcome
     * @param float $totalVolume    Total volume on the market
     * @return array{probability:float, price:float}
     */
    public function quote(float $outcomeVolume, float $totalVolume): array
    {
        if ($totalVolume <= 0.0) {
            return [
                'probability' => 0.5,
                'price'       => 0.5,
            ];
        }

        $probability = ($outcomeVolume / $totalVolume) * 100.0;
        $price       = $probability / 100.0;

        $price = max($this->cfg['base_share_price'],
                 min($this->cfg['max_share_price'], $price));

        return [
            'probability' => round($probability, $this->cfg['probability_precision']),
            'price'       => round($price, $this->cfg['price_precision']),
        ];
    }

    /**
     * Calculate the number of shares and potential payout.
     */
    public function buy(float $amount, float $sharePrice): array
    {
        if ($sharePrice <= 0.0) {
            throw new InvalidArgumentException('Share price must be > 0');
        }
        $shares  = $amount / $sharePrice;
        $payout  = $shares * $this->cfg['settlement_value'];

        return [
            'shares' => round($shares, 6),
            'payout' => round($payout, 2),
        ];
    }

    /**
     * Apply commission to a transaction amount.
     */
    public function applyFee(float $amount, float $feePercent): array
    {
        $fee = $amount * ($feePercent / 100.0);
        return [
            'amount' => round($amount, 2),
            'fee'    => round($fee, 2),
            'net'    => round($amount - $fee, 2),
        ];
    }
}
