<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modules extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'position',
        'show',
        'status'
    ];

    public function permissions()
    {
        return $this->hasMany(Permissions::class, 'module_id');
    }
}
