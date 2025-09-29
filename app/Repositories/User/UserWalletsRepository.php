<?php

namespace App\Repositories\User;

use App\Models\
{
    UserWallets
};
use App\Interfaces\User\UserWalletsRepositoryInterface;
use App\Helpers\API\CommonHelper;
use App\Repositories\BaseRepository;


class UserWalletsRepository extends BaseRepository implements UserWalletsRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(UserWallets $model)
    {
        parent::__construct($model);
    }

    
}