@extends('layouts.app')
@section('title', 'تعديل ' . $invoice->getTypeLabel())

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold">
        <i class="bi bi-pencil-square me-2 text-primary"></i>
        تعديل {{ $invoice->getTypeLabel() }} — {{ $invoice->invoice_number }}
    </h5>
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right me-1"></i>رجوع
    </a>
</div>

<form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
    @csrf @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header py-3">بيانات المستند</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">النوع <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="invoice" {{ old('type', $invoice->type) == 'invoice' ? 'selected' : '' }}>فاتورة</option>
                                <option value="quote"   {{ old('type', $invoice->type) == 'quote'   ? 'selected' : '' }}>عرض سعر</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العميل <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">-- اختر العميل --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id', $invoice->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">تاريخ الإصدار <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control"
                                   value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">تاريخ الاستحقاق</label>
                            <input type="date" name="due_date" class="form-control"
                                   value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">نسبة الضريبة (%) <span class="text-danger">*</span></label>
                            <input type="number" name="tax_rate" id="taxRate" class="form-control"
                                   value="{{ old('tax_rate', $invoice->tax_rate) }}" min="0" max="100" step="0.01" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <span>بنود الفاتورة</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addRow">
                        <i class="bi bi-plus me-1"></i>إضافة بند
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table items-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3" style="min-width:220px">الوصف</th>
                                    <th style="width:90px">الكمية</th>
                                    <th style="width:120px">سعر الوحدة</th>
                                    <th style="width:100px">خصم %</th>
                                    <th style="width:120px">الإجمالي</th>
                                    <th style="width:44px"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                @foreach($invoice->items as $i => $item)
                                <tr class="item-row">
                                    <td class="ps-3">
                                        <input type="text" name="items[{{ $i }}][description]"
                                               class="form-control form-control-sm"
                                               value="{{ old("items.$i.description", $item->description) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][quantity]"
                                               class="form-control form-control-sm qty"
                                               value="{{ old("items.$i.quantity", $item->quantity) }}" min="0.01" step="0.01" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][unit_price]"
                                               class="form-control form-control-sm price"
                                               value="{{ old("items.$i.unit_price", $item->unit_price) }}" min="0" step="0.01" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][discount_percent]"
                                               class="form-control form-control-sm disc"
                                               value="{{ old("items.$i.discount_percent", $item->discount_percent) }}" min="0" max="100" step="0.01">
                                    </td>
                                    <td class="fw-semibold row-total">{{ number_format($item->total, 2) }}</td>
                                    <td>
                                        <span class="row-remove"><i class="bi bi-x-circle fs-5"></i></span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header py-3">ملاحظات</div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" rows="4">{{ old('notes', $invoice->notes) }}</textarea>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-3">الإجماليات</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">المجموع الفرعي</span>
                        <span id="sumSubtotal">0.00 ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">إجمالي الخصم</span>
                        <span id="sumDiscount" class="text-danger">0.00 ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">الضريبة (<span id="taxPct">15</span>%)</span>
                        <span id="sumTax">0.00 ر.س</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>الإجمالي</span>
                        <span id="sumTotal" class="text-primary">0.00 ر.س</span>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-2"></i>حفظ التعديلات
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = {{ $invoice->items->count() }};

function fmt(n) { return parseFloat(n).toFixed(2); }

function calcRow(row) {
    const qty   = parseFloat(row.querySelector('.qty').value)   || 0;
    const price = parseFloat(row.querySelector('.price').value) || 0;
    const disc  = parseFloat(row.querySelector('.disc').value)  || 0;
    const gross = qty * price;
    const total = gross - (gross * disc / 100);
    row.querySelector('.row-total').textContent = fmt(total);
    return { gross, disc: gross * disc / 100 };
}

function recalc() {
    let subtotal = 0, totalDisc = 0;
    document.querySelectorAll('#itemsBody .item-row').forEach(row => {
        const r = calcRow(row);
        subtotal  += r.gross;
        totalDisc += r.disc;
    });
    const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
    const taxable = subtotal - totalDisc;
    const tax     = taxable * taxRate / 100;
    const total   = taxable + tax;

    document.getElementById('sumSubtotal').textContent = fmt(subtotal) + ' ر.س';
    document.getElementById('sumDiscount').textContent = fmt(totalDisc) + ' ر.س';
    document.getElementById('sumTax').textContent      = fmt(tax)      + ' ر.س';
    document.getElementById('sumTotal').textContent    = fmt(total)    + ' ر.س';
    document.getElementById('taxPct').textContent      = taxRate;
}

function makeRow(idx) {
    return `<tr class="item-row">
        <td class="ps-3"><input type="text" name="items[${idx}][description]" class="form-control form-control-sm" required></td>
        <td><input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm qty" value="1" min="0.01" step="0.01" required></td>
        <td><input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm price" value="0" min="0" step="0.01" required></td>
        <td><input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm disc" value="0" min="0" max="100" step="0.01"></td>
        <td class="fw-semibold row-total">0.00</td>
        <td><span class="row-remove"><i class="bi bi-x-circle fs-5"></i></span></td>
    </tr>`;
}

document.getElementById('addRow').addEventListener('click', () => {
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', makeRow(rowIndex++));
    recalc();
});

document.getElementById('itemsBody').addEventListener('input', recalc);
document.getElementById('itemsBody').addEventListener('click', e => {
    const btn = e.target.closest('.row-remove');
    if (!btn) return;
    if (document.querySelectorAll('#itemsBody .item-row').length === 1) return;
    btn.closest('.item-row').remove();
    recalc();
});

document.getElementById('taxRate').addEventListener('input', recalc);
recalc();
</script>
@endpush
