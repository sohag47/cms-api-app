<?php

namespace App\Http\Controllers;

use App\Enums\ClientOrigin;
use App\Enums\ClientType;
use App\Http\Interfaces\RepositoryInterface;
use App\Models\BatchPrice;
use App\Traits\ApiResponse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BatchPriceController extends Controller
{
    use ApiResponse;
    private $model;
    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = BatchPrice::class;
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
        $query = $this->model::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->query('search') . '%');
        }
        // $query->with(['brand', 'currency', 'product_type', 'origin', 'manufacture']);
        $brands = $this->filterQuery($request, $query);
        return $this->respondWithItem($brands);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'batch_no' => ['required', 'string', 'max:255', 'unique:batch_no'],
            'price' => ['required', 'string', 'max:255'],
            'product_id' => ['nullable', 'integer'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }
        // dd($request->all());
        $create_item = $this->repositoryInterface->store($request->all(), $this->model);
        return $this->respondWithCreated($create_item);
    }

    /**
     * Display the specified resource.
     */
    public function show(BatchPrice $batch_price)
    {
        // $batch_price->load('country');
        return $this->respondWithItem($batch_price);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BatchPrice $batch_price)
    {
        $rules = [
            'batch_no' => ['required', 'string', 'max:255', Rule::unique('batch_prices', 'batch_no')->ignore($batch_price->id)],
            'price' => ['required', 'string', 'max:255'],
            'product_id' => ['nullable', 'integer'],

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $create_item = $this->repositoryInterface->update($request->all(), $batch_price);
        return $this->respondWithCreated($create_item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BatchPrice $batch_price)
    {
        $this->repositoryInterface->delete($batch_price);
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

        $query = $this->model::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->query('search') . '%');
        }

        $clients = $query->select('id', 'name')
            ->get()
            ->map(fn($client) => [
                'value' => $client->id,
                'label' => $client->name,
            ]);

        return $this->respondWithCollection($clients);
    }
}
