<?php

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;

interface InvoiceRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): Invoice;

    public function findWithRelations(int $id): Invoice;

    public function create(array $data): Invoice;

    public function update(Invoice $invoice, array $data): Invoice;

    public function delete(Invoice $invoice): void;

    public function countByType(string $type): int;

    public function countByStatus(string $status): int;

    public function sumByStatus(string $status): float;
}
