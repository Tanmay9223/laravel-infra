<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'currency_name', 'currency_symbol', 'amount', 'status','default','currency'
    ];
}
