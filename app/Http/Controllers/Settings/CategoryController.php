<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Enums\CategoryStatus;
use App\Http\Controllers\Controller;
use App\Http\Interfaces\RepositoryInterface;
use App\Models\Settings\Category;
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
        $validator = Validator::make($request->all(), [
            ...$this->validationRules(),
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255', Rule::enum(CategoryStatus::class)->only(
                [
                    CategoryStatus::ACTIVE,
                    CategoryStatus::ARCHIVED,
                    CategoryStatus::INACTIVE,
                    CategoryStatus::DISABLED
                ]
            )],
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        // Query Builder for filtering
        $query = Category::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->query('search') . '%');
        }

        $categories = $this->filterQuery($request, $query);
        return $this->respondWithItem($categories);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories'],
            'parent_id' => ['nullable', 'integer'],
            'order' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:255', Rule::enum(CategoryStatus::class)->only(
                [
                    CategoryStatus::ACTIVE,
                    CategoryStatus::ARCHIVED,
                    CategoryStatus::INACTIVE,
                    CategoryStatus::DISABLED
                ]
            )],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }
        $data = $request->all();
        if (!isset($data['order'])) {
            $data['order'] = $this->model::max('order') + 1 ?? 1;
        }
        $category = $this->repositoryInterface->store($data, $this->model);
        return $this->respondWithCreated($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->respondWithItem($category);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'parent_id' => ['nullable', 'integer'],
            'order' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:255', Rule::enum(CategoryStatus::class)->only(
                [
                    CategoryStatus::ACTIVE,
                    CategoryStatus::ARCHIVED,
                    CategoryStatus::INACTIVE,
                    CategoryStatus::DISABLED
                ]
            )],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $data = $request->all();
        if (!isset($data['order'])) {
            $data['order'] = $this->model::max('order') + 1 ?? 1;
        }
        $update_category = $this->repositoryInterface->update($data, $category);
        return $this->respondWithUpdated($update_category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->repositoryInterface->delete($category);
        return $this->respondWithDeleted();
    }

    public function dropdown(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->query('search') . '%');
        }

        $categories = $query->select('id', 'name')
            ->where('status', CategoryStatus::ACTIVE)
            ->orderBy('order', 'DESC')
            ->get()
            ->map(fn($category) => [
                'value' => $category->id,
                'label' => $category->name,
            ]);

        return $this->respondWithCollection($categories);
    }

    public function bulkInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categories' => ['required', 'array'],
            'categories.*.name' => ['required', 'string', 'max:255', 'unique:categories'],
            'categories.*.slug' => ['required', 'string', 'max:255', 'unique:categories'],
            'categories.*.status' => ['required', 'string', Rule::in([
                CategoryStatus::ACTIVE,
                CategoryStatus::ARCHIVED,
                CategoryStatus::INACTIVE,
                CategoryStatus::DISABLED
            ])],
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $data = [];
        foreach ($request->input('categories') as $item) {
            $data[] = [
                'name' => $item['name'],
                'slug' => $item['slug'],
                'status' => $item['status'],
                'created_at' => now()
            ];
        }
        Category::insert($data);

        return $this->respondWithCreated(null, 'Categories imported successfully');
    }
}
