<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
        private readonly CustomerService             $customerService,
    ) {}

    public function index()
    {
        $customers = $this->customers->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $this->customerService->store($request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->customerService->update($customer, $request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function destroy(Customer $customer)
    {
        $this->customerService->delete($customer);

        return redirect()
            ->route('customers.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }
}
