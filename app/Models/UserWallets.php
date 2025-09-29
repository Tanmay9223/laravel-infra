<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallets extends Model
{
    protected $fillable = [
        'user_id',
        'total_deposit',
        'total_withdraw_btc',
        'total_withdraw_usd',
        'total_winning_btc',
        'total_winning_usd',
        'total_commission_btc',
        'total_commission_usd',
        'other_btc',
        'other_usd',
        'status',
        'total_wallet_btc_amount',
        'total_wallet_usd_amount',
        'created_by',
        'updated_by',
        'deleted_at'
    ];
}
