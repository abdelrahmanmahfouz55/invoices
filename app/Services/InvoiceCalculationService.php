<?php

namespace App\Services;

/**
 * Pure calculation logic — no DB, no side effects.
 * Input: raw items array + tax rate.
 * Output: CalculationResult DTO.
 */
class InvoiceCalculationService
{
    public function calculate(array $items, float $taxRate): CalculationResult
    {
        $subtotal       = 0.0;
        $totalDiscount  = 0.0;
        $processedItems = [];

        foreach ($items as $row) {
            $qty      = (float) ($row['quantity']         ?? 0);
            $price    = (float) ($row['unit_price']       ?? 0);
            $discPct  = (float) ($row['discount_percent'] ?? 0);

            $lineGross    = $qty * $price;
            $lineDiscount = $lineGross * $discPct / 100;
            $lineTotal    = $lineGross - $lineDiscount;

            $subtotal      += $lineGross;
            $totalDiscount += $lineDiscount;

            $processedItems[] = [
                'description'      => $row['description'],
                'quantity'         => $qty,
                'unit_price'       => $price,
                'discount_percent' => $discPct,
                'total'            => round($lineTotal, 2),
            ];
        }

        $taxableAmount = $subtotal - $totalDiscount;
        $taxAmount     = $taxableAmount * $taxRate / 100;
        $grandTotal    = $taxableAmount + $taxAmount;

        return new CalculationResult(
            subtotal:       round($subtotal, 2),
            discountAmount: round($totalDiscount, 2),
            taxRate:        $taxRate,
            taxAmount:      round($taxAmount, 2),
            total:          round($grandTotal, 2),
            items:          $processedItems,
        );
    }
}

/**
 * Immutable value object carrying all calculation results.
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

    public function toInvoiceArray(): array
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
