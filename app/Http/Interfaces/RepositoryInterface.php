<?php

namespace App\Http\Interfaces;

interface RepositoryInterface
{
    // basic operations
    public function getAll($model);
    public function store($data, $model);
    public function findById($id, $model, $with = []);
    public function update($data, $model);
    public function delete($data);

     // Advanced operations
    public function getAllWithPaginate($model);
    public function getDropdown($model, $value, $label);
    public function getAllTrash($model);
    public function findTrashByID($id, $model);
    public function restore($data, $model);
    public function insert($data, $model);

    public function getAllWithRelationAndPagination($model);
}