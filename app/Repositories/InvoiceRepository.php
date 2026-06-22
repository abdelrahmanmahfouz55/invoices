<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(private readonly Invoice $model) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with('customer')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): Invoice
    {
        return $this->model->findOrFail($id);
    }

    public function findWithRelations(int $id): Invoice
    {
        return $this->model
            ->with('customer', 'items')
            ->findOrFail($id);
    }

    public function create(array $data): Invoice
    {
        return $this->model->create($data);
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice->fresh();
    }

    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
    }

    public function countByType(string $type): int
    {
        return $this->model->where('type', $type)->count();
    }

    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function sumByStatus(string $status): float
    {
        return (float) $this->model->where('status', $status)->sum('total');
    }
}
