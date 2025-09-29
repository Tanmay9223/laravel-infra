<?php

namespace App\Repositories\Admin;

use App\Models\
{
    Role
};
use App\Interfaces\Admin\RoleRepositoryInterface;
use App\Helpers\API\CommonHelper;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    /**
     * Create a new class instance.
     */
     public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    
}