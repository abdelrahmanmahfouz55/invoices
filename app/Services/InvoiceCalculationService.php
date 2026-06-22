<?php

namespace App\Services;

/**
 * Pure calculation logic — no DB, no side effects.
 * Input : raw items array + tax rate percentage.
 * Output: CalculationResult value object.
 */
class InvoiceCalculationService
{
    public function calculate(array $items, float $taxRate): CalculationResult
    {
        $subtotal      = 0.0;
        $totalDiscount = 0.0;
        $processed     = [];

        foreach ($items as $row) {
            $qty     = (float) ($row['quantity']         ?? 0);
            $price   = (float) ($row['unit_price']       ?? 0);
            $discPct = (float) ($row['discount_percent'] ?? 0);

            $lineGross    = $qty * $price;
            $lineDiscount = $lineGross * $discPct / 100;
            $lineTotal    = $lineGross - $lineDiscount;

            $subtotal      += $lineGross;
            $totalDiscount += $lineDiscount;

            $processed[] = [
                'description'      => $row['description'],
                'quantity'         => $qty,
                'unit_price'       => $price,
                'discount_percent' => $discPct,
                'total'            => round($lineTotal, 2),
            ];
        }

        $taxable    = $subtotal - $totalDiscount;
        $taxAmount  = $taxable * $taxRate / 100;
        $grandTotal = $taxable + $taxAmount;

        return new CalculationResult(
            subtotal:       round($subtotal, 2),
            discountAmount: round($totalDiscount, 2),
            taxRate:        $taxRate,
            taxAmount:      round($taxAmount, 2),
            total:          round($grandTotal, 2),
            items:          $processed,
        );
    }
}

/**
 * Immutable DTO — carries all calculation output.
 */
readonly class CalculationResult
{
    public function __construct(
        public float $subtotal,
        public float $discountAmount,
        public float $taxRate,
        public float $taxAmount,
        public float $total,
        public array $items,
    ) {}

    public function toArray(): array
    {
        return [
            'subtotal'        => $this->subtotal,
            'discount_amount' => $this->discountAmount,
            'tax_rate'        => $this->taxRate,
            'tax_amount'      => $this->taxAmount,
            'total'           => $this->total,
        ];
    }
}
