<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Enums\CategoryStatus;
use App\Models\Settings\ProductCategories;
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
        $query = $this->model::with('productTypes');

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
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
            'product_types' => ['required', 'array'],
            'product_types.*' => ['integer', 'distinct', 'exists:product_types,id'],
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


        DB::beginTransaction();
        try {
            $category = $this->model::create([
                'name' => $request->name,
                'status' => $request->status ?? CategoryStatus::ACTIVE,
            ]);
            $product_categories = [];
            foreach ($request->product_types as $typeId) {
                $product_categories[] = [
                    'product_type_id' => $typeId,
                    'category_id' => $category->id ?? null,
                    'created_at' => now(),
                ];
            }
            ProductCategories::insert($product_categories);
            DB::commit();
            return $this->respondWithCreated($category);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondServerError('Something went wrong.', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('productTypes');
        return $this->respondWithItem($category);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'product_types' => ['required', 'array'],
            'product_types.*' => ['integer', 'distinct', 'exists:product_types,id'],
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
        $update_category = $this->repositoryInterface->update($request->all(), $category);
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
