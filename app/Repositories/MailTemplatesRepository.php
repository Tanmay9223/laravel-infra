<?php

namespace App\Repositories;

use App\Models\
{
    MailTemplates
};
use App\Interfaces\MailTemplatesRepositoryInterface;
use App\Helpers\API\CommonHelper;
use App\Repositories\BaseRepository;

class MailTemplatesRepository extends BaseRepository implements MailTemplatesRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(MailTemplates $model)
    {
        parent::__construct($model);
    }

    
}