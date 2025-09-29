<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\BaseRepositoryInterface;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    public function store(array $data){
        return $this->model->create($data);
    }

    public function getByColumn(array $data){
        return $this->model->where($data)->first();
    }

    public function update($id, array $data){
        $updated = $this->model->where('id', $id)->update($data);
        return $updated ? true : false;
    }

    public function updateByColumn(array $data, array $data2){
        $updated = $this->model->where($data)->update($data2);
        return $updated ?
         true : false;
    }

    public function dataExists($whereColumn ,$columnValue){
        return $this->model->where($whereColumn, $columnValue)->exists();
    }
}
