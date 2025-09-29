<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplates extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'subject',
        'body',
        'status'
    ];
}
