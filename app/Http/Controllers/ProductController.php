<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Enums\CategoryStatus;
use App\Http\Interfaces\RepositoryInterface;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponse;

class ProductController extends Controller
{
    use ApiResponse;
    private $model;
    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = Product::class;
        $this->repositoryInterface = $repositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rules = [
            'status' => ['nullable', 'string', 'max:255', Rule::enum(CategoryStatus::class)->only([
                CategoryStatus::ACTIVE, CategoryStatus::ARCHIVED, CategoryStatus::INACTIVE, CategoryStatus::DISABLED]
            )],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $categories = [];
        if ($request->has("status")) {
            $categories = Product::where("status", $request->query("status"))->paginate(10);
        }else {
            $categories = Product::paginate(10);
        }
        return $this->respondWithItem(new ProductCollection($categories));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories'],
            'image' => ['nullable'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer'],
            'order' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:255', Rule::enum(CategoryStatus::class)->only([
                CategoryStatus::ACTIVE, CategoryStatus::ARCHIVED, CategoryStatus::INACTIVE, CategoryStatus::DISABLED]
            )],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $category = $this->repositoryInterface->store($request->all(), $this->model);        
        return $this->respondWithCreated(new ProductResource($category));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->respondWithItem(new ProductResource($product));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($product->id)],
            'slug' => ['required', 'string', 'max:255', 'unique:categories'],
            'image' => ['nullable'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer'],
            'order' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:255', Rule::enum(CategoryStatus::class)->only([
                CategoryStatus::ACTIVE, CategoryStatus::ARCHIVED, CategoryStatus::INACTIVE, CategoryStatus::DISABLED]
            )],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }
        $update_category = $this->repositoryInterface->update($request->all(), $product);
        return $this->respondWithUpdated(new ProductResource($update_category));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->repositoryInterface->delete($product);
        return $this->respondWithDeleted();
    }
}
