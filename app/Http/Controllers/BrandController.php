<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnums;
use App\Http\Interfaces\RepositoryInterface;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
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
    public function index()
    {
        return $this->respondWithItem($this->model::paginate(10));
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
