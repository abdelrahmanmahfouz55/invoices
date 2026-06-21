@extends('layouts.app')
@section('title', 'إنشاء فاتورة / عرض سعر')

@push('styles')
<style>
    .section-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }
    .type-toggle { display: flex; gap: 8px; }
    .type-toggle input[type=radio] { display: none; }
    .type-toggle label {
        flex: 1;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        cursor: pointer;
        text-align: center;
        transition: all .18s;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }
    .type-toggle label .type-icon { font-size: 22px; display: block; margin-bottom: 4px; }
    .type-toggle input[type=radio]:checked + label {
        border-color: #6366f1;
        background: #ede9fe;
        color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    .items-wrap { background: #f8fafc; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
    .items-head {
        display: grid;
        grid-template-columns: 1fr 80px 110px 90px 100px 36px;
        gap: 0;
        background: #f1f5f9;
        padding: 10px 14px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
    }
    .item-row-grid {
        display: grid;
        grid-template-columns: 1fr 80px 110px 90px 100px 36px;
        gap: 6px;
        padding: 8px 14px;
        border-top: 1px solid #e2e8f0;
        align-items: center;
        background: #fff;
        transition: background .1s;
    }
    .item-row-grid:hover { background: #fafafe; }
    .row-total-cell { font-weight: 700; color: #1e293b; font-size: 13.5px; }
    .totals-panel {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 14px;
        padding: 20px;
        color: #fff;
    }
    .totals-panel .t-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 7px 0;
        font-size: 13.5px;
        color: #94a3b8;
    }
    .totals-panel .t-row .tval { color: #e2e8f0; font-weight: 600; }
    .totals-panel .t-divider { border: none; border-top: 1px solid rgba(255,255,255,.08); margin: 8px 0; }
    .totals-panel .t-grand {
        display: flex; justify-content: space-between; align-items: center;
        padding: 4px 0;
        font-size: 20px;
        font-weight: 800;
        color: #fff;
    }
    .totals-panel .t-grand span { color: #a5b4fc; }
    .save-btn {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        font-family: 'Cairo', sans-serif;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(99,102,241,.4);
        transition: all .2s;
        margin-top: 14px;
    }
    .save-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99,102,241,.5); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h4>إنشاء مستند جديد</h4>
        <p>فاتورة ضريبية أو عرض سعر</p>
    </div>
    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right me-1"></i>رجوع للقائمة
    </a>
</div>

<form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
@csrf

<div class="row g-4">

    {{-- ═══════════════ LEFT: Main Form ═══════════════ --}}
    <div class="col-lg-8">

        {{-- Type & Customer --}}
        <div class="card mb-4">
            <div class="card-body">

                <div class="section-label">نوع المستند</div>
                <div class="type-toggle mb-4">
                    <input type="radio" name="type" id="t_invoice" value="invoice"
                           {{ old('type','invoice') == 'invoice' ? 'checked' : '' }}>
                    <label for="t_invoice">
                        <span class="type-icon">🧾</span>فاتورة ضريبية
                    </label>
                    <input type="radio" name="type" id="t_quote" value="quote"
                           {{ old('type') == 'quote' ? 'checked' : '' }}>
                    <label for="t_quote">
                        <span class="type-icon">📋</span>عرض سعر
                    </label>
                </div>

                <div class="section-label">بيانات العميل والتواريخ</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">العميل <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">— اختر العميل —</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                    @if($c->phone) — {{ $c->phone }} @endif
                                </option>
                            @endforeach
                        </select>
                        <div style="margin-top:6px;font-size:12px;color:#94a3b8">
                            لم تجد العميل؟
                            <a href="{{ route('customers.create') }}" target="_blank" style="color:#6366f1">أضفه هنا</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">تاريخ الإصدار <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" class="form-control"
                               value="{{ old('issue_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" class="form-control"
                               value="{{ old('due_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">نسبة الضريبة %</label>
                        <div style="position:relative">
                            <input type="number" name="tax_rate" id="taxRate" class="form-control"
                                   value="{{ old('tax_rate', 15) }}" min="0" max="100" step="0.01" required>
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px">%</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Items --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul" style="color:#6366f1"></i>بنود المستند
                </div>
                <button type="button" id="addRow" style="
                    background:#ede9fe;color:#4f46e5;border:none;border-radius:8px;
                    padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;
                    font-family:'Cairo',sans-serif;display:flex;align-items:center;gap:5px
                ">
                    <i class="bi bi-plus-lg"></i>إضافة بند
                </button>
            </div>
            <div class="card-body p-0">
                <div class="items-wrap" style="border:none;border-radius:0">
                    <div class="items-head">
                        <span>الوصف</span>
                        <span>الكمية</span>
                        <span>سعر الوحدة</span>
                        <span>خصم %</span>
                        <span>الإجمالي</span>
                        <span></span>
                    </div>
                    <div id="itemsBody">
                        <div class="item-row-grid">
                            <input type="text" name="items[0][description]"
                                   class="form-control form-control-sm" placeholder="وصف البند..." required>
                            <input type="number" name="items[0][quantity]"
                                   class="form-control form-control-sm qty" value="1" min="0.01" step="0.01" required>
                            <input type="number" name="items[0][unit_price]"
                                   class="form-control form-control-sm price" value="0" min="0" step="0.01" required>
                            <input type="number" name="items[0][discount_percent]"
                                   class="form-control form-control-sm disc" value="0" min="0" max="100" step="0.01">
                            <span class="row-total-cell">0.00</span>
                            <span class="row-remove" style="cursor:pointer;color:#cbd5e1;font-size:18px;text-align:center" title="حذف البند">
                                <i class="bi bi-x-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div style="padding:14px 16px;background:#fafafa;border-top:1px solid #e2e8f0">
                    <div style="font-size:12px;color:#94a3b8">
                        <i class="bi bi-info-circle me-1"></i>
                        الإجمالي = (الكمية × سعر الوحدة) × (1 - الخصم%)
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ RIGHT: Summary ═══════════════ --}}
    <div class="col-lg-4">

        <div class="totals-panel mb-4">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#475569;margin-bottom:14px">
                ملخص المستند
            </div>
            <div class="t-row">
                <span>المجموع الفرعي</span>
                <span class="tval" id="sumSubtotal">0.00 ر.س</span>
            </div>
            <div class="t-row" style="color:#f87171">
                <span>إجمالي الخصم</span>
                <span style="color:#f87171;font-weight:600" id="sumDiscount">0.00 ر.س</span>
            </div>
            <div class="t-row">
                <span>ضريبة <span id="taxPct">15</span>%</span>
                <span class="tval" id="sumTax">0.00 ر.س</span>
            </div>
            <hr class="t-divider">
            <div class="t-grand">
                <span>الإجمالي</span>
                <span id="sumTotal">0.00 ر.س</span>
            </div>
            <button type="submit" class="save-btn">
                <i class="bi bi-cloud-check me-2"></i>حفظ المستند
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-sticky" style="color:#d97706"></i>ملاحظات
                </div>
            </div>
            <div class="card-body">
                <textarea name="notes" class="form-control" rows="5"
                          placeholder="أي ملاحظات للعميل...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = 1;
const fmt = n => parseFloat(n||0).toFixed(2);

function calcRow(row) {
    const qty   = parseFloat(row.querySelector('.qty').value)   || 0;
    const price = parseFloat(row.querySelector('.price').value) || 0;
    const disc  = parseFloat(row.querySelector('.disc').value)  || 0;
    const gross = qty * price;
    const total = gross * (1 - disc / 100);
    row.querySelector('.row-total-cell').textContent = fmt(total);
    return { gross, discAmt: gross * disc / 100 };
}

function recalc() {
    let subtotal = 0, discAmt = 0;
    document.querySelectorAll('#itemsBody .item-row-grid').forEach(row => {
        const r = calcRow(row);
        subtotal += r.gross;
        discAmt  += r.discAmt;
    });
    const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
    const taxable = subtotal - discAmt;
    const tax     = taxable * taxRate / 100;
    const total   = taxable + tax;

    document.getElementById('sumSubtotal').textContent = fmt(subtotal) + ' ر.س';
    document.getElementById('sumDiscount').textContent = fmt(discAmt)  + ' ر.س';
    document.getElementById('sumTax').textContent      = fmt(tax)      + ' ر.س';
    document.getElementById('sumTotal').textContent    = fmt(total)    + ' ر.س';
    document.getElementById('taxPct').textContent      = taxRate;
}

function rowHtml(idx) {
    return `<div class="item-row-grid">
        <input type="text" name="items[${idx}][description]" class="form-control form-control-sm" placeholder="وصف البند..." required>
        <input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm qty" value="1" min="0.01" step="0.01" required>
        <input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm price" value="0" min="0" step="0.01" required>
        <input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm disc" value="0" min="0" max="100" step="0.01">
        <span class="row-total-cell">0.00</span>
        <span class="row-remove" style="cursor:pointer;color:#cbd5e1;font-size:18px;text-align:center">
            <i class="bi bi-x-circle"></i>
        </span>
    </div>`;
}

document.getElementById('addRow').addEventListener('click', () => {
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', rowHtml(rowIndex++));
});

document.getElementById('itemsBody').addEventListener('input', recalc);
document.getElementById('itemsBody').addEventListener('mouseover', e => {
    const rm = e.target.closest('.row-remove');
    if (rm) rm.style.color = '#ef4444';
});
document.getElementById('itemsBody').addEventListener('mouseout', e => {
    const rm = e.target.closest('.row-remove');
    if (rm) rm.style.color = '#cbd5e1';
});
document.getElementById('itemsBody').addEventListener('click', e => {
    const rm = e.target.closest('.row-remove');
    if (!rm) return;
    if (document.querySelectorAll('#itemsBody .item-row-grid').length === 1) return;
    rm.closest('.item-row-grid').remove();
    recalc();
});

document.getElementById('taxRate').addEventListener('input', recalc);
recalc();
</script>
@endpush
