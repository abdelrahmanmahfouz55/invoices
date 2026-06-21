@extends('layouts.app')
@section('title', $invoice->invoice_number)

@push('styles')
<style>
    .show-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 14px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .doc-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 12px;
        font-weight: 700;
        color: #a5b4fc;
        margin-bottom: 8px;
    }
    .show-header h2 { font-size: 24px; font-weight: 800; margin: 0; color: #fff; }
    .show-header p  { color: #94a3b8; font-size: 13px; margin: 4px 0 0; }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1px;
        background: #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .info-cell { background: #fff; padding: 18px 20px; }
    .info-cell h6 { font-size: 11px; font-weight: 700; text-transform: uppercase;
                    letter-spacing: .06em; color: #94a3b8; margin-bottom: 8px; }
    .info-cell .value { font-size: 15px; font-weight: 600; color: #1e293b; }
    .info-cell .sub   { font-size: 12px; color: #64748b; margin-top: 3px; }

    .items-show-table thead th { background:#f8fafc; font-size:11px; font-weight:700;
                                  text-transform:uppercase; letter-spacing:.05em; color:#64748b; padding:11px 16px; }
    .items-show-table td { padding:12px 16px; font-size:13.5px; vertical-align:middle; border-bottom:1px solid #f1f5f9; }
    .items-show-table tbody tr:last-child td { border-bottom: none; }

    .totals-side { background: #f8fafc; border-radius: 12px; padding: 18px; border: 1px solid #e2e8f0; }
    .totals-side .tr { display:flex; justify-content:space-between; padding:6px 0;
                       font-size:13.5px; color:#64748b; border-bottom:1px solid #f1f5f9; }
    .totals-side .tr:last-child { border-bottom:none; }
    .totals-side .tr .v { font-weight:600; color:#1e293b; }
    .totals-side .grand { font-size:18px; font-weight:800; color:#4f46e5; }
    .totals-side .grand .v { color:#4f46e5; }
</style>
@endpush

@section('content')

{{-- ── Hero header ── --}}
<div class="show-header">
    <div>
        <div class="doc-badge">
            <i class="bi bi-{{ $invoice->type === 'quote' ? 'clipboard' : 'receipt' }}"></i>
            {{ $invoice->getTypeLabel() }}
        </div>
        <h2>{{ $invoice->invoice_number }}</h2>
        <p>أُنشئ بتاريخ {{ $invoice->created_at->format('Y/m/d') }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <span class="badge badge-{{ $invoice->status }}" style="padding:8px 16px;font-size:13px">
            {{ $invoice->getStatusLabel() }}
        </span>
        <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" style="
            background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.2);
            border-radius:8px;padding:7px 16px;text-decoration:none;font-size:13px;font-weight:600;
            display:flex;align-items:center;gap:6px
        ">
            <i class="bi bi-file-pdf"></i>تصدير PDF
        </a>
        <a href="{{ route('invoices.edit', $invoice) }}" style="
            background:rgba(99,102,241,.15);color:#a5b4fc;border:1px solid rgba(99,102,241,.2);
            border-radius:8px;padding:7px 16px;text-decoration:none;font-size:13px;font-weight:600;
            display:flex;align-items:center;gap:6px
        ">
            <i class="bi bi-pencil"></i>تعديل
        </a>
        <a href="{{ route('invoices.index') }}" style="
            background:rgba(255,255,255,.08);color:#94a3b8;border:1px solid rgba(255,255,255,.1);
            border-radius:8px;padding:7px 16px;text-decoration:none;font-size:13px;font-weight:600;
            display:flex;align-items:center;gap:6px
        ">
            <i class="bi bi-arrow-right"></i>رجوع
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">

        {{-- Info grid --}}
        <div class="info-grid">
            <div class="info-cell">
                <h6>العميل</h6>
                <div class="value">{{ $invoice->customer->name }}</div>
                @if($invoice->customer->phone)
                    <div class="sub"><i class="bi bi-telephone me-1"></i>{{ $invoice->customer->phone }}</div>
                @endif
                @if($invoice->customer->address)
                    <div class="sub"><i class="bi bi-geo-alt me-1"></i>{{ $invoice->customer->address }}</div>
                @endif
            </div>
            <div class="info-cell">
                <h6>تفاصيل المستند</h6>
                <div class="value">{{ $invoice->issue_date->format('d / m / Y') }}</div>
                @if($invoice->due_date)
                    <div class="sub">الاستحقاق: {{ $invoice->due_date->format('d / m / Y') }}</div>
                @endif
                @if($invoice->customer->tax_number)
                    <div class="sub">الرقم الضريبي: {{ $invoice->customer->tax_number }}</div>
                @endif
            </div>
        </div>

        {{-- Items table --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-list-ul me-2" style="color:#6366f1"></i>بنود المستند</div>
            <div style="overflow-x:auto">
                <table class="table items-show-table mb-0">
                    <thead>
                        <tr>
                            <th style="padding-right:20px">#</th>
                            <th>الوصف</th>
                            <th class="text-center">الكمية</th>
                            <th class="text-center">سعر الوحدة</th>
                            <th class="text-center">خصم %</th>
                            <th class="text-end" style="padding-left:20px">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $i => $item)
                        <tr>
                            <td style="padding-right:20px;color:#94a3b8;font-weight:700">{{ $i + 1 }}</td>
                            <td style="font-weight:500">{{ $item->description }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-center">
                                @if($item->discount_percent > 0)
                                    <span style="background:#fee2e2;color:#dc2626;border-radius:20px;padding:2px 10px;font-size:12px;font-weight:600">
                                        {{ $item->discount_percent }}%
                                    </span>
                                @else
                                    <span style="color:#cbd5e1">—</span>
                                @endif
                            </td>
                            <td class="text-end" style="padding-left:20px;font-weight:700;color:#4f46e5">
                                {{ number_format($item->total, 2) }} ر.س
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="totals-side">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:14px">
                الإجماليات
            </div>
            <div class="tr">
                <span>المجموع الفرعي</span>
                <span class="v">{{ number_format($invoice->subtotal, 2) }} ر.س</span>
            </div>
            <div class="tr">
                <span>الخصم</span>
                <span class="v" style="color:#dc2626">-{{ number_format($invoice->discount_amount, 2) }} ر.س</span>
            </div>
            <div class="tr">
                <span>الضريبة ({{ $invoice->tax_rate }}%)</span>
                <span class="v">{{ number_format($invoice->tax_amount, 2) }} ر.س</span>
            </div>
            <div class="tr grand" style="margin-top:8px;padding-top:8px;border-top:2px solid #e2e8f0">
                <span>الإجمالي</span>
                <span class="v">{{ number_format($invoice->total, 2) }} ر.س</span>
            </div>
        </div>

        @if($invoice->notes)
        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-sticky me-2" style="color:#d97706"></i>ملاحظات</div>
            <div class="card-body" style="font-size:13.5px;color:#475569">
                {{ $invoice->notes }}
            </div>
        </div>
        @endif

        <div class="mt-3">
            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المستند؟')">
                @csrf @method('DELETE')
                <button type="submit" style="
                    width:100%;padding:10px;border:1.5px solid #fee2e2;border-radius:10px;
                    background:#fff;color:#ef4444;font-family:'Cairo',sans-serif;font-size:13px;
                    font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px
                ">
                    <i class="bi bi-trash"></i>حذف هذا المستند
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
