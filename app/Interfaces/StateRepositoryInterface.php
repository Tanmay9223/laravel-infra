<?php

namespace App\Interfaces;

interface StateRepositoryInterface
{
    public function getByColumnAll(array $data);
}
