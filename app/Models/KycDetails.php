<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycDetails extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'no_of_attempts',
        'block_time',
        'email_otp',
        'email_validity',
        'email_verification_status',
        'email_verify_at',
        'mobile_otp',
        'mobile_validity',
        'mobile_verification_status',
        'mobile_verify_at',
        'profile_verification_status',
        'profile_verify_at',
        'id_verification_status',
        'id_verify_at',
        'address_verification_status',
        'address_verify_at',
        'kyc_approved_status'
    ];

    protected $casts = [
        'email_validity' => 'datetime',
        'mobile_validity' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
