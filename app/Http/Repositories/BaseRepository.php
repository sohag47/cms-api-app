<?php
namespace App\Http\Repositories;

use App\Http\Interfaces\RepositoryInterface;
use Illuminate\Support\Facades\Auth;

class BaseRepository implements RepositoryInterface
{
    // basic operations
    public function getAll($model)
    {
        return $model::paginate(10);
    }

    public function store($data, $model)
    {
        $request = $data;
        // $request["created_by"] = Auth::id() ?? 1;
        // $request["updated_by"] = Auth::id() ?? 1;
        return $model::create($request);
    }

    public function findById($id, $model, $with = [])
    {
        if (!empty($with)) {
            return $model::with($with)->find($id);
        }
        return $model::find($id);
    }

    public function update($data, $model)
    {
        $request = $data;
        // $request["updated_by"] = Auth::id() ?? 1;
        $model->update($request);
        return $model->refresh();
    }

    public function delete($data)
    {
        // $data["deleted_by"] = Auth::id() ?? 1;
        // $data->save();
        return $data->delete();
    }


     // Advanced operations
    public function getAllWithPaginate($model)
    {
        return $model::paginate(10);
    }
    public function getDropdown($model, $value, $label)
    {
        return $model::get(["$value as value", "$label as label"]);
    }

    public function getAllTrash($model)
    {
        return $model::onlyTrashed()->get();
    }
    public function findTrashByID($id, $model)
    {
        return $model::onlyTrashed()->find($id);
    }
    public function restore($data, $model)
    {

        $data->restore();
        return $model::find($data->id);

    }
    public function insert($data, $model)
    {
        return $model::insert($data);
    }
    public function getAllWithRelationAndPagination($model)
    {
        return $model->paginate(10);
    }
}
