<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no','shop_name','customer_name','invoice_date',
        'sub_total','grand_total','user_id'
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
     public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
