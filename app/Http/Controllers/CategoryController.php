<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Enums\CategoryStatus;
use App\Http\Interfaces\RepositoryInterface;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;



class CategoryController extends Controller
{
    use ApiResponse;
    private $model;
    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = Category::class;
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
            $categories = Category::where("status", $request->query("status"))->paginate(10);
        }else {
            $categories = Category::paginate(10);
        }
        return $this->respondWithItem(new CategoryCollection($categories));
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
        return $this->respondWithCreated(new CategoryResource($category));
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->respondWithItem(new CategoryResource($category));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
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
        $update_category = $this->repositoryInterface->update($request->all(), $category);
        return $this->respondWithUpdated(new CategoryResource($update_category));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->repositoryInterface->delete($category);
        return $this->respondWithDeleted();
    }
}
