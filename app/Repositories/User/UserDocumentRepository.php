<?php

namespace App\Repositories\User;

use App\Models\
{
    UserDocument
};
use App\Interfaces\User\UserDocumentRepositoryInterface;
use App\Helpers\API\CommonHelper;
use App\Repositories\BaseRepository;


class UserDocumentRepository extends BaseRepository implements UserDocumentRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(UserDocument $model)
    {
        parent::__construct($model);
    }

    
}