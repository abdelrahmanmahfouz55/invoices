@extends('layouts.app')
@section('title', 'إدارة العملاء')

@section('content')
<div class="page-header">
    <div>
        <h4>العملاء</h4>
        <p>{{ $customers->total() }} عميل مسجل</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>إضافة عميل
    </a>
</div>

<div class="card">
    <div style="overflow-x:auto">
        <table class="table">
            <thead>
                <tr>
                    <th style="padding-right:20px">العميل</th>
                    <th>الهاتف</th>
                    <th>البريد الإلكتروني</th>
                    <th>الرقم الضريبي</th>
                    <th class="text-center">الفواتير</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td style="padding-right:20px">
                        <div class="d-flex align-items-center gap-2">
                            <div style="
                                width:36px;height:36px;border-radius:50%;
                                background:linear-gradient(135deg,#6366f1,#818cf8);
                                display:flex;align-items:center;justify-content:center;
                                color:#fff;font-size:14px;font-weight:700;flex-shrink:0
                            ">{{ mb_substr($customer->name, 0, 1) }}</div>
                            <div>
                                <div style="font-weight:600;color:#1e293b;font-size:14px">{{ $customer->name }}</div>
                                @if($customer->address)
                                <div style="font-size:12px;color:#94a3b8">{{ Str::limit($customer->address, 35) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="color:#475569;font-size:13px">{{ $customer->phone ?: '—' }}</td>
                    <td style="color:#475569;font-size:13px">{{ $customer->email ?: '—' }}</td>
                    <td style="color:#475569;font-size:13px">{{ $customer->tax_number ?: '—' }}</td>
                    <td class="text-center">
                        <span style="
                            background:#ede9fe;color:#6d28d9;border-radius:20px;
                            padding:3px 12px;font-size:12px;font-weight:700
                        ">{{ $customer->invoices_count }}</span>
                    </td>
                    <td style="padding-left:16px">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('customers.edit', $customer) }}" class="btn-icon primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon danger" style="background:none;border:1px solid #e2e8f0">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-people"></i>
                            <h6>لا يوجد عملاء بعد</h6>
                            <p style="font-size:13px">أضف أول عميل لتبدأ</p>
                            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="bi bi-plus me-1"></i>إضافة عميل
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="card-footer">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
