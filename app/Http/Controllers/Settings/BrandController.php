<?php

namespace App\Http\Controllers\Settings;

use App\Enums\StatusEnums;
use App\Http\Controllers\Controller;
use App\Http\Interfaces\RepositoryInterface;
use App\Models\Settings\Brand;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    use ApiResponse;

    private $model;

    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = Brand::class;
        $this->repositoryInterface = $repositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $validator = Validator::make($request->all(), [...$this->validationRules(),  'search' => ['nullable', 'string', 'max:255']]);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        // Query Builder for filtering
        $query = Brand::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%'.$request->query('search').'%');
        }

        $brands = $this->filterQuery($request, $query);

        return $this->respondWithItem($brands);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:brands'],
            'logo' => ['nullable'],
            'status' => ['nullable', 'string', 'max:255', Rule::enum(StatusEnums::class)->only([
                StatusEnums::ACTIVE, StatusEnums::DRAFT, StatusEnums::INACTIVE, StatusEnums::DISABLED]
            )],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $brand = $this->repositoryInterface->store($request->all(), $this->model);

        return $this->respondWithCreated($brand);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return $this->respondWithItem($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('brands', 'name')->ignore($brand->id)],
            'logo' => ['nullable'],
            'status' => ['nullable', 'string', 'max:255', Rule::enum(StatusEnums::class)->only([
                StatusEnums::ACTIVE, StatusEnums::DRAFT, StatusEnums::INACTIVE, StatusEnums::DISABLED]
            )],

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }
        $update_brand = $this->repositoryInterface->update($request->all(), $brand);

        return $this->respondWithUpdated($update_brand);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $this->repositoryInterface->delete($brand);

        return $this->respondWithDeleted();
    }
}
