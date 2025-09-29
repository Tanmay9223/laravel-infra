<?php

namespace App\Repositories\User;

use App\Models\
{
    UserDetails
};
use App\Interfaces\User\UserDetailsRepositoryInterface;
use App\Helpers\API\CommonHelper;
use App\Repositories\BaseRepository;


class UserDetailsRepository extends BaseRepository implements UserDetailsRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(UserDetails $model)
    {
        parent::__construct($model);
    }

    
}