<?php

namespace App\Repositories\User;

use App\Models\
{
    User
};
use App\Interfaces\User\UserRepositoryInterface;
use App\Helpers\API\CommonHelper;
use App\Repositories\BaseRepository;


class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }


    
}