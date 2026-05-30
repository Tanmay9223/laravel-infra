<?php

namespace App\Interfaces;

interface BaseRepositoryInterface 
{
    public function store(array $data);
    public function getByColumn(array $data);
    public function update($id, array $data);
    public function updateByColumn(array $data, array $data2);
    public function dataExists($whereColumn,$columnValue);
}
