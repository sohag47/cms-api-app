<?php

namespace App\Http\Controllers;

use App\Enums\ClientOrigin;
use App\Enums\ClientType;
use App\Http\Interfaces\RepositoryInterface;
use App\Models\Client;
use App\Traits\ApiResponse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    use ApiResponse;
    private $model;
    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = Client::class;
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
        $query->with('country');
        $brands = $this->filterQuery($request, $query);
        return $this->respondWithItem($brands);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:clients', "email"],
            'phone' => ['nullable', 'string', 'max:255'],
            'thumb_image' => ['nullable'],
            'address' => ['nullable'],
            'city' => ['nullable'],
            'state' => ['nullable'],
            'postal_code' => ['nullable'],
            'country_id' => ['nullable', 'integer'],
            'client_type' => ['required', 'string', 'max:255', Rule::enum(ClientType::class)->only(
                [
                    ClientType::CUSTOMER,
                    ClientType::SUPPLIER,
                    ClientType::BOTH
                ]
            )],
            'client_origin' => ['required', 'string', 'max:255', Rule::enum(ClientOrigin::class)->only(
                [
                    ClientOrigin::LOCAL,
                    ClientOrigin::FOREIGN,
                ]
            )],
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
    public function show(Client $client)
    {
        $client->load('country');
        return $this->respondWithItem($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', "email", Rule::unique('clients', 'email')->ignore($client->id)],
            'phone' => ['nullable', 'string', 'max:255'],
            'thumb_image' => ['nullable'],
            'address' => ['nullable'],
            'city' => ['nullable'],
            'state' => ['nullable'],
            'postal_code' => ['nullable'],
            'country_id' => ['nullable', 'integer'],
            'client_type' => ['required', 'string', 'max:255', Rule::enum(ClientType::class)->only(
                [
                    ClientType::CUSTOMER,
                    ClientType::SUPPLIER,
                    ClientType::BOTH
                ]
            )],
            'client_origin' => ['required', 'string', 'max:255', Rule::enum(ClientOrigin::class)->only(
                [
                    ClientOrigin::LOCAL,
                    ClientOrigin::FOREIGN,
                ]
            )],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $create_item = $this->repositoryInterface->update($request->all(), $client);
        return $this->respondWithCreated($create_item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $this->repositoryInterface->delete($client);
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
