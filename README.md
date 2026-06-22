# 🧾 Arabic RTL Invoicing System

A Laravel 12 invoicing application with full Arabic RTL support.  
Supports invoices and quotations, dynamic line items, server-side calculations, and Arabic PDF export.

---

## ✨ Features

- **Arabic RTL** interface — Bootstrap 5 RTL + Cairo font
- **Invoice & Quotation** creation with auto-generated reference numbers (`INV-2026-0001`)
- **Customer management** — add, edit, delete with dropdown in invoice form
- **Dynamic line items** — quantity × unit price − discount %
- **Server-side calculations** via `InvoiceCalculationService` (JS only provides live preview)
- **Configurable tax rate** per invoice
- **Arabic PDF export** via mPDF (proper Arabic text shaping & RTL)
- **Clean architecture** — Controller → Service → Repository → Model
- **FormRequest validation** with Arabic error messages

---

## 🏗️ Project Structure.

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── InvoiceController.php       # Thin — delegates to InvoiceService
│   │   └── CustomerController.php      # Thin — delegates to CustomerService
│   └── Requests/
│       ├── StoreInvoiceRequest.php     # Validation rules + Arabic messages
│       ├── UpdateInvoiceRequest.php
│       ├── StoreCustomerRequest.php
│       └── UpdateCustomerRequest.php
├── Models/
│   ├── Invoice.php
│   ├── InvoiceItem.php
│   └── Customer.php
├── Repositories/
│   ├── Contracts/
│   │   ├── InvoiceRepositoryInterface.php
│   │   └── CustomerRepositoryInterface.php
│   ├── InvoiceRepository.php           # All DB queries for invoices
│   └── CustomerRepository.php
└── Services/
    ├── InvoiceCalculationService.php   # Pure math — no DB, returns CalculationResult DTO
    ├── InvoiceService.php              # Orchestrates: calculate → save invoice + items
    └── CustomerService.php
```

**Request flow:**

```
HTTP Request
  → FormRequest   (validate + Arabic messages)
  → Controller    (thin — only HTTP concerns)
  → Service       (business logic)
  → Repository    (data access)
  → Model / DB
```

---

## ⚙️ Requirements

| Requirement | Version |
|---|---|
| PHP | ≥ 8.2 |
| Laravel | 12.x |
| MySQL | ≥ 8.0 |
| Composer | ≥ 2.x |

---

## 🚀 Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/abdelrahmanmahfouz55/invoices.git
cd invoices
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoices
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Create database

```sql
CREATE DATABASE invoices CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Start the server

```bash
php artisan serve
```

Visit → [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 📐 Calculation Logic

All math lives in `App\Services\InvoiceCalculationService`:

```
Line Total     = Qty × Unit Price × (1 − Discount% ÷ 100)
Subtotal       = Σ (Qty × Unit Price)
Total Discount = Σ (Qty × Unit Price × Discount% ÷ 100)
Taxable        = Subtotal − Total Discount
Tax Amount     = Taxable × Tax Rate ÷ 100
Grand Total    = Taxable + Tax Amount
```

Returns an immutable `CalculationResult` readonly DTO (PHP 8.2).

---

## 🔗 Routes

| Method | URI | Description |
|---|---|---|
| GET | `/invoices` | List + stats dashboard |
| GET | `/invoices/create` | New invoice/quote form |
| POST | `/invoices` | Store (server recalculates) |
| GET | `/invoices/{id}` | Show detail |
| GET | `/invoices/{id}/edit` | Edit form |
| PUT | `/invoices/{id}` | Update |
| DELETE | `/invoices/{id}` | Delete |
| GET | `/invoices/{id}/pdf` | Export Arabic PDF |
| GET | `/customers` | Customer list |
| POST | `/customers` | Add customer |
| PUT | `/customers/{id}` | Update customer |
| DELETE | `/customers/{id}` | Delete customer |

---

## 📦 Packages Used

| Package | Purpose |
|---|---|
| `laravel/framework ^12` | Framework |
| `mpdf/mpdf` | Arabic PDF generation |

---

## 📝 License

MIT
