<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'type', 'issue_date', 'due_date',
        'subtotal', 'discount_amount', 'tax_rate', 'tax_amount', 'total',
        'notes', 'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date'   => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeOfStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeWithSummary(Builder $query): Builder
    {
        return $query->with('customer');
    }

    // ── Accessors / Helpers ────────────────────────────────────────

    public function getTypeLabel(): string
    {
        return $this->type === 'quote' ? 'عرض سعر' : 'فاتورة';
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft'     => 'مسودة',
            'sent'      => 'مُرسلة',
            'paid'      => 'مدفوعة',
            'cancelled' => 'ملغاة',
            default     => $this->status,
        };
    }

    public static function generateNumber(string $type): string
    {
        $prefix = $type === 'quote' ? 'QT' : 'INV';
        $year   = now()->format('Y');
        $count  = self::ofType($type)->whereYear('created_at', $year)->count() + 1;

        return $prefix . '-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
