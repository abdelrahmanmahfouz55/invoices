<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\InvoiceService;
use Mpdf\Mpdf;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceRepositoryInterface  $invoices,
        private readonly CustomerRepositoryInterface $customers,
        private readonly InvoiceService              $invoiceService,
    ) {}

    public function index()
    {
        $invoices = $this->invoices->paginate(15);
        $stats    = $this->invoiceService->getStats();

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $customers = $this->customers->all();

        return view('invoices.create', compact('customers'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->store($request->validated());

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'تم إنشاء ' . $invoice->getTypeLabel() . ' بنجاح');
    }

    public function show(Invoice $invoice)
    {
        $invoice = $this->invoices->findWithRelations($invoice->id);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice   = $this->invoices->findWithRelations($invoice->id);
        $customers = $this->customers->all();

        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $invoice = $this->invoiceService->update($invoice, $request->validated());

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'تم تحديث ' . $invoice->getTypeLabel() . ' بنجاح');
    }

    public function destroy(Invoice $invoice)
    {
        $this->invoiceService->delete($invoice);

        return redirect()
            ->route('invoices.index')
            ->with('success', 'تم حذف المستند بنجاح');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice = $this->invoices->findWithRelations($invoice->id);
        $html    = view('invoices.pdf', compact('invoice'))->render();

        $mpdf = new Mpdf([
            'mode'           => 'utf-8',
            'format'         => 'A4',
            'margin_top'     => 15,
            'margin_bottom'  => 15,
            'margin_left'    => 15,
            'margin_right'   => 15,
            'directionality' => 'rtl',
            'default_font'   => 'dejavusans',
        ]);

        $mpdf->SetTitle($invoice->invoice_number);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }
}
