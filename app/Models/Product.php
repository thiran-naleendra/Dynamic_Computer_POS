<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'name',
        'dealer_price',
        'sell_price',
        'warranty_days',
    ];

    // ✅ Product -> Stock (one-to-one)
    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    // ✅ Warranty label accessor (optional but recommended)
    public function getWarrantyTextAttribute(): string
    {
        return match ((int) $this->warranty_days) {
            0    => 'No Warranty',
            7    => '1 Week',
            14   => '2 Weeks',
            30   => '1 Month',
            90   => '3 Months',
            180  => '6 Months',
            365  => '1 Year',
            730  => '2 Years',
            1095 => '3 Years',
            1825 => '5 Years',
            default => ((int) $this->warranty_days) . ' Days',
        };
    }
}
