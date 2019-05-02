<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceRequest extends Model
{
    protected $fillable = [
        'name',
        'memo',
        'enabled',
        'return_url',
        'callback_url',
        'amount',
        'currency',
    ];

}