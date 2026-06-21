<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('invoices')->latest()->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
            'address'    => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
        ]);

        Customer::create($request->only('name', 'phone', 'email', 'address', 'tax_number'));
        return redirect()->route('customers.index')->with('success', 'تم إضافة العميل بنجاح');
    }

    public function show(Customer $customer)
    {
        $invoices = $customer->invoices()->latest()->paginate(10);
        return view('customers.show', compact('customer', 'invoices'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
            'address'    => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
        ]);

        $customer->update($request->only('name', 'phone', 'email', 'address', 'tax_number'));
        return redirect()->route('customers.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'تم الحذف بنجاح');
    }
}
