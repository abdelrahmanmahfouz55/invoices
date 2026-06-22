<?php

namespace App\Services;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface    $invoices,
        private readonly InvoiceCalculationService $calculator,
    ) {}

    public function store(array $validated): Invoice
    {
        $calc = $this->calculator->calculate(
            $validated['items'],
            (float) $validated['tax_rate']
        );

        return DB::transaction(function () use ($validated, $calc) {
            $invoice = $this->invoices->create([
                'invoice_number'  => Invoice::generateNumber($validated['type']),
                'customer_id'     => $validated['customer_id'],
                'type'            => $validated['type'],
                'issue_date'      => $validated['issue_date'],
                'due_date'        => $validated['due_date'] ?? null,
                'notes'           => $validated['notes']    ?? null,
                'status'          => 'draft',
                ...$calc->toInvoiceArray(),
            ]);

            foreach ($calc->items as $item) {
                $invoice->items()->create($item);
            }

            return $invoice;
        });
    }

    public function update(Invoice $invoice, array $validated): Invoice
    {
        $calc = $this->calculator->calculate(
            $validated['items'],
            (float) $validated['tax_rate']
        );

        return DB::transaction(function () use ($invoice, $validated, $calc) {
            $updated = $this->invoices->update($invoice, [
                'customer_id' => $validated['customer_id'],
                'type'        => $validated['type'],
                'issue_date'  => $validated['issue_date'],
                'due_date'    => $validated['due_date'] ?? null,
                'notes'       => $validated['notes']    ?? null,
                ...$calc->toInvoiceArray(),
            ]);

            $updated->items()->delete();

            foreach ($calc->items as $item) {
                $updated->items()->create($item);
            }

            return $updated;
        });
    }

    public function delete(Invoice $invoice): void
    {
        $this->invoices->delete($invoice);
    }

    public function getStats(): array
    {
        return [
            'total'   => $this->invoices->paginate(1)->total(),
            'paid'    => $this->invoices->countByStatus('paid'),
            'draft'   => $this->invoices->countByStatus('draft'),
            'revenue' => $this->invoices->sumByStatus('paid'),
        ];
    }
}
