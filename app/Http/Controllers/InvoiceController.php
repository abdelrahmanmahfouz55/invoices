<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Mpdf\Mpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('invoices.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'              => 'required|exists:customers,id',
            'type'                     => 'required|in:invoice,quote',
            'issue_date'               => 'required|date',
            'due_date'                 => 'nullable|date|after_or_equal:issue_date',
            'tax_rate'                 => 'required|numeric|min:0|max:100',
            'items'                    => 'required|array|min:1',
            'items.*.description'      => 'required|string',
            'items.*.quantity'         => 'required|numeric|min:0.01',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            [$subtotal, $discountAmount, $taxAmount, $total, $items] = $this->calculateTotals(
                $request->items,
                (float) $request->tax_rate
            );

            $invoice = Invoice::create([
                'invoice_number'  => Invoice::generateNumber($request->type),
                'customer_id'     => $request->customer_id,
                'type'            => $request->type,
                'issue_date'      => $request->issue_date,
                'due_date'        => $request->due_date,
                'tax_rate'        => $request->tax_rate,
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount'      => $taxAmount,
                'total'           => $total,
                'notes'           => $request->notes,
                'status'          => 'draft',
            ]);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'تم الحفظ بنجاح');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $customers = Customer::orderBy('name')->get();
        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'customer_id'              => 'required|exists:customers,id',
            'type'                     => 'required|in:invoice,quote',
            'issue_date'               => 'required|date',
            'due_date'                 => 'nullable|date|after_or_equal:issue_date',
            'tax_rate'                 => 'required|numeric|min:0|max:100',
            'items'                    => 'required|array|min:1',
            'items.*.description'      => 'required|string',
            'items.*.quantity'         => 'required|numeric|min:0.01',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            [$subtotal, $discountAmount, $taxAmount, $total, $items] = $this->calculateTotals(
                $request->items,
                (float) $request->tax_rate
            );

            $invoice->update([
                'customer_id'     => $request->customer_id,
                'type'            => $request->type,
                'issue_date'      => $request->issue_date,
                'due_date'        => $request->due_date,
                'tax_rate'        => $request->tax_rate,
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount'      => $taxAmount,
                'total'           => $total,
                'notes'           => $request->notes,
            ]);

            $invoice->items()->delete();
            foreach ($items as $item) {
                $invoice->items()->create($item);
            }
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'تم الحذف بنجاح');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('customer', 'items');
        $html = view('invoices.pdf', compact('invoice'))->render();

        $mpdf = new Mpdf([
            'mode'            => 'utf-8',
            'format'          => 'A4',
            'margin_top'      => 15,
            'margin_bottom'   => 15,
            'margin_left'     => 15,
            'margin_right'    => 15,
            'directionality'  => 'rtl',
            'default_font'    => 'dejavusans',
        ]);

        $mpdf->SetTitle($invoice->invoice_number);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }

    private function calculateTotals(array $rawItems, float $taxRate): array
    {
        $subtotal       = 0;
        $discountAmount = 0;
        $items          = [];

        foreach ($rawItems as $row) {
            $qty      = (float) $row['quantity'];
            $price    = (float) $row['unit_price'];
            $disc     = (float) ($row['discount_percent'] ?? 0);
            $lineGross = $qty * $price;
            $lineDisc  = $lineGross * $disc / 100;
            $lineTotal = $lineGross - $lineDisc;

            $subtotal       += $lineGross;
            $discountAmount += $lineDisc;

            $items[] = [
                'description'      => $row['description'],
                'quantity'         => $qty,
                'unit_price'       => $price,
                'discount_percent' => $disc,
                'total'            => $lineTotal,
            ];
        }

        $taxable   = $subtotal - $discountAmount;
        $taxAmount = $taxable * $taxRate / 100;
        $total     = $taxable + $taxAmount;

        return [$subtotal, $discountAmount, $taxAmount, $total, $items];
    }
}
