<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'doc_type',
        'type',
        'description',
        'front_side',
        'back_side',
        'status',
        'comment',
        'is_admin',
        'admin_created_by'
    ];
}
