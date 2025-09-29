<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model

{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'country_id',
        'state_id',
        'city_id',
        'zipcode',
        'dob',
        'ekyc_date',
        'address',
        'reason',
        'gender',
        'created_by',
        'updated_by',
        'deleted_at'
    ];

    public function client()
    {
        return $this->belongsTo(User::class);
    }

    public function countries()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(States::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }
}
