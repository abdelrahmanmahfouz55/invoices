<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Customer::withCount('invoices')->latest()->paginate($perPage);
    }

    public function all(): Collection
    {
        return Customer::orderBy('name')->get();
    }

    public function store(array $validated): Customer
    {
        return Customer::create($validated);
    }

    public function update(Customer $customer, array $validated): Customer
    {
        $customer->update($validated);
        return $customer->fresh();
    }

    public function delete(Customer $customer): void
    {
        $customer->delete();
    }
}
