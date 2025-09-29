<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Google2faDetail extends Model
{
    protected $table = 'google_2fa_details';
    protected $fillable = ['google_2fa_key'];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

}
