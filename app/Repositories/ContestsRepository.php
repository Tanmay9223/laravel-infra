<?php

namespace App\Repositories\User;

use App\Models\
{
    Contests
};
use App\Interfaces\User\ContestsRepositoryInterface;
use App\Helpers\API\CommonHelper;
use App\Repositories\BaseRepository;


class ContestsRepository extends BaseRepository implements ContestsRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(Contests $model)
    {
        parent::__construct($model);
    }

    
}