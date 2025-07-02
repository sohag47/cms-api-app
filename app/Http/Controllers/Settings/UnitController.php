<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Interfaces\RepositoryInterface;


class UnitController extends Controller
{
    use ApiResponse;
    private $model;
    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = Unit::class;
        $this->repositoryInterface = $repositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        // Query Builder for filtering
        $query = $this->model::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->query('search') . '%');
        }
        return $this->respondWithItem($query->get());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:units'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $currency = $this->repositoryInterface->store($request->all(), $this->model);
        return $this->respondWithCreated($currency);
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return $this->respondWithItem($unit);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('units', 'name')->ignore($unit->id)],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $update_category = $this->repositoryInterface->update($request->all(), $unit);
        return $this->respondWithUpdated($update_category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $this->repositoryInterface->delete($unit);
        return $this->respondWithDeleted();
    }

    public function dropdown(Request $request)
    {
        $rules = [
            'search' => ['nullable', 'string', 'max:255'],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $query = $this->model::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->query('search') . '%');
        }
        $categories = $query->select('id', 'name')
            ->get()
            ->map(fn($category) => [
                'value' => $category->id,
                'label' => $category->name,
            ]);

        return $this->respondWithCollection($categories);
    }
}
