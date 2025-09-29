<?php

namespace App\Repositories\User;

use App\Models\
{
    KycDetails
};
use App\Interfaces\User\KycRepositoryInterface;
use App\Helpers\API\CommonHelper;
use App\Repositories\BaseRepository;


class KycRepository extends BaseRepository implements KycRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(KycDetails $model)
    {
        parent::__construct($model);
    }

    
}