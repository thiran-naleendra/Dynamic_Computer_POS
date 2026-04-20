<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceNote extends Model
{
    protected $fillable = [
        'service_no','service_date',
        'customer_name','customer_address','customer_tel',
        'item','serial_no','invoice_no','details',
        'customer_complains',
        'received_service_item','grn_customer_name','grn_date'
    ];
}
