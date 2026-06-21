@extends('layouts.app')
@section('title', 'تعديل العميل')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2 text-primary"></i>تعديل: {{ $customer->name }}</h5>
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right me-1"></i>رجوع
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf @method('PUT')
                    @include('customers._form', ['model' => $customer])
                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i class="bi bi-save me-2"></i>حفظ التعديلات
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
