<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    public function store(array $validated): Customer
    {
        return $this->customers->create($validated);
    }

    public function update(Customer $customer, array $validated): Customer
    {
        return $this->customers->update($customer, $validated);
    }

    public function delete(Customer $customer): void
    {
        $this->customers->delete($customer);
    }
}
