<div class="row g-3">
    <div class="col-12">
        <label class="form-label">الاسم <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $model->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">الهاتف</label>
        <input type="text" name="phone" class="form-control"
               value="{{ old('phone', $model->phone ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control"
               value="{{ old('email', $model->email ?? '') }}">
    </div>
    <div class="col-12">
        <label class="form-label">العنوان</label>
        <textarea name="address" class="form-control" rows="2">{{ old('address', $model->address ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">الرقم الضريبي</label>
        <input type="text" name="tax_number" class="form-control"
               value="{{ old('tax_number', $model->tax_number ?? '') }}">
    </div>
</div>
