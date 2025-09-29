<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaDataList extends Model
{

    protected $fillable = [
        'group_id', 'type', 'meta_title', 'meta_key', 'meta_value', 'status'
    ];
}
