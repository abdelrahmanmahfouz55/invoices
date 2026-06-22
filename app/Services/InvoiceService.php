<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceCalculationService $calculator,
    ) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::withSummary()->latest()->paginate($perPage);
    }

    public function find(int $id): Invoice
    {
        return Invoice::with('customer', 'items')->findOrFail($id);
    }

    public function store(array $validated): Invoice
    {
        $calc = $this->calculator->calculate(
            $validated['items'],
            (float) $validated['tax_rate'],
        );

        return DB::transaction(function () use ($validated, $calc) {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateNumber($validated['type']),
                'customer_id'    => $validated['customer_id'],
                'type'           => $validated['type'],
                'issue_date'     => $validated['issue_date'],
                'due_date'       => $validated['due_date']  ?? null,
                'notes'          => $validated['notes']     ?? null,
                'status'         => 'draft',
                ...$calc->toArray(),
            ]);

            $invoice->items()->createMany($calc->items);

            return $invoice;
        });
    }

    public function update(Invoice $invoice, array $validated): Invoice
    {
        $calc = $this->calculator->calculate(
            $validated['items'],
            (float) $validated['tax_rate'],
        );

        return DB::transaction(function () use ($invoice, $validated, $calc) {
            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'type'        => $validated['type'],
                'issue_date'  => $validated['issue_date'],
                'due_date'    => $validated['due_date'] ?? null,
                'notes'       => $validated['notes']    ?? null,
                ...$calc->toArray(),
            ]);

            $invoice->items()->delete();
            $invoice->items()->createMany($calc->items);

            return $invoice->fresh('customer', 'items');
        });
    }

    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
    }

    public function stats(): array
    {
        return [
            'total'   => Invoice::count(),
            'paid'    => Invoice::ofStatus('paid')->count(),
            'draft'   => Invoice::ofStatus('draft')->count(),
            'revenue' => (float) Invoice::ofStatus('paid')->sum('total'),
        ];
    }
}
