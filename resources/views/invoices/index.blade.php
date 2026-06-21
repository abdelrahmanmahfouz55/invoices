@extends('layouts.app')
@section('title', 'الفواتير وعروض الأسعار')

@section('content')

{{-- ── Stats row ── --}}
@php
    $total   = $invoices->total();
    $paid    = \App\Models\Invoice::where('status','paid')->count();
    $draft   = \App\Models\Invoice::where('status','draft')->count();
    $revenue = \App\Models\Invoice::where('status','paid')->sum('total');
@endphp
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-file-earmark-text"></i></span>
                <span style="font-size:11px;color:#94a3b8">إجمالي المستندات</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#1e293b">{{ $total }}</div>
            <div style="font-size:12px;color:#94a3b8;margin-top:2px">فاتورة وعرض سعر</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-icon" style="background:#dcfce7;color:#16a34a"><i class="bi bi-check-circle"></i></span>
                <span style="font-size:11px;color:#94a3b8">مدفوعة</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#1e293b">{{ $paid }}</div>
            <div style="font-size:12px;color:#94a3b8;margin-top:2px">فاتورة مكتملة</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-icon" style="background:#fef3c7;color:#d97706"><i class="bi bi-clock"></i></span>
                <span style="font-size:11px;color:#94a3b8">مسودة</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#1e293b">{{ $draft }}</div>
            <div style="font-size:12px;color:#94a3b8;margin-top:2px">بانتظار الإرسال</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-icon" style="background:#dbeafe;color:#2563eb"><i class="bi bi-currency-dollar"></i></span>
                <span style="font-size:11px;color:#94a3b8">الإيرادات</span>
            </div>
            <div style="font-size:22px;font-weight:800;color:#1e293b">{{ number_format($revenue, 0) }}</div>
            <div style="font-size:12px;color:#94a3b8;margin-top:2px">ريال سعودي</div>
        </div>
    </div>
</div>

{{-- ── Table card ── --}}
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-table" style="color:#6366f1"></i>
            <span>جميع المستندات</span>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>إنشاء جديد
        </a>
    </div>

    <div style="overflow-x:auto">
        <table class="table">
            <thead>
                <tr>
                    <th style="padding-right:20px">رقم المستند</th>
                    <th>النوع</th>
                    <th>العميل</th>
                    <th>تاريخ الإصدار</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td style="padding-right:20px">
                        <span style="font-weight:700;color:#4f46e5;font-size:13px">{{ $invoice->invoice_number }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $invoice->type === 'quote' ? 'badge-quote' : 'badge-invoice' }}">
                            {{ $invoice->getTypeLabel() }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#818cf8);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0">
                                {{ mb_substr($invoice->customer->name, 0, 1) }}
                            </div>
                            <span style="font-weight:500">{{ $invoice->customer->name }}</span>
                        </div>
                    </td>
                    <td style="color:#64748b;font-size:13px">{{ $invoice->issue_date->format('Y/m/d') }}</td>
                    <td style="font-weight:700;color:#1e293b">{{ number_format($invoice->total, 2) }} <span style="color:#94a3b8;font-weight:400;font-size:12px">ر.س</span></td>
                    <td>
                        <span class="badge badge-{{ $invoice->status }}">{{ $invoice->getStatusLabel() }}</span>
                    </td>
                    <td style="padding-left:16px">
                        <div class="d-flex align-items-center gap-1 justify-content-end">
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn-icon" title="عرض">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn-icon primary" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn-icon success" title="PDF" target="_blank">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon danger" title="حذف" style="background:none;border:1px solid #e2e8f0">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h6>لا توجد فواتير بعد</h6>
                            <p style="font-size:13px">أنشئ أول فاتورة أو عرض سعر الآن</p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="bi bi-plus-lg me-1"></i>إنشاء الأول
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices->hasPages())
    <div class="card-footer">{{ $invoices->links() }}</div>
    @endif
</div>

@endsection
