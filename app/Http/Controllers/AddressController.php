<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnums;
use App\Http\Interfaces\RepositoryInterface;
use App\Models\Address;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    use ApiResponse;

    private $model;

    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = Address::class;
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
            'street_address' => ['required', 'string', 'max:255'],
            'country_id' => ['nullable', 'integer'],
            'client_id' => ['nullable', 'integer'],
            'contact_person_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:255', Rule::enum(StatusEnums::class)->only(
                [
                    StatusEnums::ACTIVE,
                    StatusEnums::DRAFT,
                    StatusEnums::INACTIVE,
                    StatusEnums::DISABLED,
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
    public function show(Address $address)
    {
        // $address->load('country');
        return $this->respondWithItem($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        $rules = [
            'street_address' => ['required', 'string', 'max:255'],
            'country_id' => ['nullable', 'integer'],
            'client_id' => ['nullable', 'integer'],
            'contact_person_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:255', Rule::enum(StatusEnums::class)->only(
                [
                    StatusEnums::ACTIVE,
                    StatusEnums::DRAFT,
                    StatusEnums::INACTIVE,
                    StatusEnums::DISABLED,
                ]
            )],

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $create_item = $this->repositoryInterface->update($request->all(), $address);

        return $this->respondWithCreated($create_item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $this->repositoryInterface->delete($address);

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
            $query->where('name', 'LIKE', '%'.$request->query('search').'%');
        }

        $address = $query->select('id', 'name')
            ->get()
            ->map(fn ($client) => [
                'value' => $client->id,
                'label' => $client->name,
            ]);

        return $this->respondWithCollection($address);
    }
}
