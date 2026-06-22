<?php

namespace App\Providers;

use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            InvoiceRepositoryInterface::class,
            InvoiceRepository::class,
        );

        $this->app->bind(
            CustomerRepositoryInterface::class,
            CustomerRepository::class,
        );
    }

    public function boot(): void {}
}
