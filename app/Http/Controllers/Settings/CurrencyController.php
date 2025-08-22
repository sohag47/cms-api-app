<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Interfaces\RepositoryInterface;
use App\Models\Settings\Currency;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    use ApiResponse;

    private $model;

    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = Currency::class;
        $this->repositoryInterface = $repositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['nullable', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],

        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        // Query Builder for filtering
        $query = $this->model::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%'.$request->query('search').'%');
        }
        if ($request->filled('code')) {
            $query->where('code', $request->query('code'));
        }
        if ($request->filled('symbol')) {
            $query->where('symbol', $request->query('symbol'));
        }

        return $this->respondWithItem($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:currencies'],
            'code' => ['required', 'string', 'max:255', 'unique:currencies'],
            'symbol' => ['required', 'string', 'max:255', 'unique:currencies'],
            'usd_exchange_rate' => ['required', 'numeric', 'min:0'],
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
    public function show(Currency $currency)
    {
        return $this->respondWithItem($currency);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('currencies', 'name')->ignore($currency->id)],
            'symbol' => ['required', 'string', 'max:255', Rule::unique('currencies', 'symbol')->ignore($currency->id)],
            'code' => ['required', 'string', 'max:255', Rule::unique('currencies', 'code')->ignore($currency->id)],
            'usd_exchange_rate' => ['required', 'numeric', 'min:0'],

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $update_category = $this->repositoryInterface->update($request->all(), $currency);

        return $this->respondWithUpdated($update_category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        $this->repositoryInterface->delete($currency);

        return $this->respondWithDeleted();
    }

    public function dropdown(Request $request)
    {
        $rules = [
            'search' => ['nullable', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $query = $this->model::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%'.$request->query('search').'%');
        }
        if ($request->filled('code')) {
            $query->where('code', $request->query('code'));
        }
        if ($request->filled('symbol')) {
            $query->where('symbol', $request->query('symbol'));
        }
        $categories = $query->select('id', 'name')
            ->get()
            ->map(fn ($category) => [
                'value' => $category->id,
                'label' => $category->name,
            ]);

        return $this->respondWithCollection($categories);
    }
}
