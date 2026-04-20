<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnNote extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'return_no','return_date','invoice_id','customer_name','customer_tel',
        'reason','total_qty','created_by'
    ];

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
}
