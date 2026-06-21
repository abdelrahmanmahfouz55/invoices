<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<title>{{ $invoice->invoice_number }}</title>
<style>
/* mPDF-compatible styles — no flexbox/grid, use tables for layout */
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'dejavusans', 'sans-serif';
    font-size: 11pt;
    color: #1e293b;
    direction: rtl;
    background: #fff;
}

/* ── Header ── */
.header-table { width: 100%; border-collapse: collapse; margin-bottom: 24pt; }
.header-logo  { width: 55%; vertical-align: middle; }
.header-logo h1 { font-size: 22pt; font-weight: bold; color: #0f172a; margin: 0 0 3pt 0; }
.header-logo p  { font-size: 9pt; color: #64748b; margin: 0; }
.header-badge { width: 45%; vertical-align: middle; text-align: left; }
.doc-type-badge {
    display: inline-block;
    background: #0f172a;
    color: #ffffff;
    font-size: 13pt;
    font-weight: bold;
    padding: 6pt 18pt;
    border-radius: 6pt;
    margin-bottom: 5pt;
}
.doc-number { font-size: 11pt; color: #64748b; margin-top: 3pt; }

/* ── Divider ── */
.divider { border: none; border-top: 2pt solid #e2e8f0; margin: 0 0 18pt 0; }
.divider-accent { border: none; border-top: 3pt solid #6366f1; width: 60pt; margin: 0 0 18pt 0; }

/* ── Info grid (2 columns via table) ── */
.info-table { width: 100%; border-collapse: collapse; margin-bottom: 20pt; }
.info-cell {
    width: 50%;
    vertical-align: top;
    padding: 14pt 16pt;
    background: #f8fafc;
    border: 1pt solid #e2e8f0;
}
.info-cell-left  { border-right: none; }
.info-cell-right { border-left: none; }
.info-label { font-size: 8pt; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6pt; }
.info-name  { font-size: 13pt; font-weight: bold; color: #0f172a; margin-bottom: 3pt; }
.info-sub   { font-size: 9pt; color: #64748b; margin-bottom: 2pt; }

/* ── Items table ── */
.items-table { width: 100%; border-collapse: collapse; margin-bottom: 20pt; }
.items-table th {
    background: #0f172a;
    color: #ffffff;
    font-size: 9pt;
    font-weight: bold;
    padding: 9pt 12pt;
    text-align: right;
}
.items-table td {
    padding: 9pt 12pt;
    font-size: 10pt;
    border-bottom: 1pt solid #f1f5f9;
    vertical-align: middle;
}
.items-table tr:nth-child(even) td { background: #f8fafc; }
.items-table td.ta-c { text-align: center; }
.items-table td.ta-l { text-align: left; }
.items-table td.bold { font-weight: bold; color: #4f46e5; }

/* ── Totals ── */
.totals-wrap { width: 100%; border-collapse: collapse; }
.totals-space { width: 55%; vertical-align: top; }
.totals-box   { width: 45%; vertical-align: top; }

.totals-table { width: 100%; border-collapse: collapse; }
.totals-table td { padding: 5pt 10pt; font-size: 10pt; }
.totals-table .tlabel { color: #64748b; text-align: right; }
.totals-table .tval   { color: #1e293b; font-weight: bold; text-align: left; }
.totals-table .disc-val { color: #dc2626; font-weight: bold; }
.grand-row td {
    border-top: 2pt solid #0f172a;
    font-size: 14pt;
    font-weight: bold;
    padding-top: 10pt;
}
.grand-row .tlabel { color: #0f172a; }
.grand-row .tval   { color: #4f46e5; font-size: 16pt; }

/* ── Notes ── */
.notes-box {
    background: #f8fafc;
    border-right: 4pt solid #6366f1;
    padding: 10pt 14pt;
    margin-top: 20pt;
    font-size: 9.5pt;
    color: #475569;
}
.notes-box strong { color: #0f172a; display: block; margin-bottom: 4pt; }

/* ── Footer ── */
.footer {
    text-align: center;
    font-size: 8pt;
    color: #94a3b8;
    margin-top: 30pt;
    padding-top: 10pt;
    border-top: 1pt solid #e2e8f0;
}
</style>
</head>
<body>

{{-- ── Header ── --}}
<table class="header-table">
    <tr>
        <td class="header-logo">
            <h1>شركتك</h1>
            <p>نظام الفواتير الإلكترونية</p>
        </td>
        <td class="header-badge">
            <div class="doc-type-badge">{{ $invoice->getTypeLabel() }}</div>
            <div class="doc-number"># {{ $invoice->invoice_number }}</div>
        </td>
    </tr>
</table>

<hr class="divider">

{{-- ── Info grid ── --}}
<table class="info-table">
    <tr>
        <td class="info-cell info-cell-right">
            <div class="info-label">بيانات العميل</div>
            <div class="info-name">{{ $invoice->customer->name }}</div>
            @if($invoice->customer->phone)
            <div class="info-sub">{{ $invoice->customer->phone }}</div>
            @endif
            @if($invoice->customer->email)
            <div class="info-sub">{{ $invoice->customer->email }}</div>
            @endif
            @if($invoice->customer->address)
            <div class="info-sub">{{ $invoice->customer->address }}</div>
            @endif
            @if($invoice->customer->tax_number)
            <div class="info-sub">الرقم الضريبي: {{ $invoice->customer->tax_number }}</div>
            @endif
        </td>
        <td class="info-cell info-cell-left" style="text-align:left">
            <div class="info-label">تفاصيل المستند</div>
            <div class="info-sub">تاريخ الإصدار: <strong>{{ $invoice->issue_date->format('d/m/Y') }}</strong></div>
            @if($invoice->due_date)
            <div class="info-sub">تاريخ الاستحقاق: <strong>{{ $invoice->due_date->format('d/m/Y') }}</strong></div>
            @endif
            <div class="info-sub">الحالة: <strong>{{ $invoice->getStatusLabel() }}</strong></div>
        </td>
    </tr>
</table>

{{-- ── Items table ── --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width:30pt; text-align:center">#</th>
            <th>الوصف</th>
            <th style="width:55pt; text-align:center">الكمية</th>
            <th style="width:75pt; text-align:left">سعر الوحدة</th>
            <th style="width:55pt; text-align:center">خصم %</th>
            <th style="width:75pt; text-align:left">الإجمالي</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $i => $item)
        <tr>
            <td class="ta-c" style="color:#94a3b8; font-weight:bold">{{ $i + 1 }}</td>
            <td>{{ $item->description }}</td>
            <td class="ta-c">{{ $item->quantity }}</td>
            <td class="ta-l">{{ number_format($item->unit_price, 2) }}</td>
            <td class="ta-c">{{ $item->discount_percent > 0 ? $item->discount_percent.'%' : '—' }}</td>
            <td class="ta-l bold">{{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ── Totals ── --}}
<table class="totals-wrap">
    <tr>
        <td class="totals-space"></td>
        <td class="totals-box">
            <table class="totals-table">
                <tr>
                    <td class="tlabel">المجموع الفرعي</td>
                    <td class="tval">{{ number_format($invoice->subtotal, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td class="tlabel">الخصم</td>
                    <td class="tval disc-val">- {{ number_format($invoice->discount_amount, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td class="tlabel">الضريبة ({{ $invoice->tax_rate }}%)</td>
                    <td class="tval">{{ number_format($invoice->tax_amount, 2) }} ر.س</td>
                </tr>
                <tr class="grand-row">
                    <td class="tlabel">الإجمالي</td>
                    <td class="tval">{{ number_format($invoice->total, 2) }} ر.س</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ── Notes ── --}}
@if($invoice->notes)
<div class="notes-box">
    <strong>ملاحظات:</strong>
    {{ $invoice->notes }}
</div>
@endif

{{-- ── Footer ── --}}
<div class="footer">
    تم إنشاء هذا المستند إلكترونياً &mdash; {{ $invoice->invoice_number }}
</div>

</body>
</html>
