<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $table = 'return_items';

    protected $fillable = [
        'return_id','product_id','item_name','serial_no','qty'
    ];

    public function returnNote()
    {
        return $this->belongsTo(ReturnNote::class, 'return_id');
    }
}
