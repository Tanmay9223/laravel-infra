<?php

namespace App\Repositories\Admin;

use App\Models\
{
    Admin
};
use App\Interfaces\Admin\AdminRepositoryInterface;
use App\Helpers\API\CommonHelper;
use App\Repositories\BaseRepository;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    /**
     * Create a new class instance.
     */
     public function __construct(Admin $model)
    {
        parent::__construct($model);
    }
    public function store(array $data){
        return false;
    }

    
}