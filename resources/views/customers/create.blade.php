@extends('layouts.app')
@section('title', 'إضافة عميل')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2 text-primary"></i>إضافة عميل جديد</h5>
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right me-1"></i>رجوع
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                    @include('customers._form')
                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i class="bi bi-save me-2"></i>حفظ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
