<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\RepositoryInterface;
use App\Models\ContactPerson;
use App\Traits\ApiResponse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ContactPersonController extends Controller
{
    use ApiResponse;
    private $model;
    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = ContactPerson::class;
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
            'email' => ['required', 'string', 'max:255', 'unique:contact_person', "email"],
            'phone' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable'],
            'client_id' => ['nullable', 'integer'],

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
    public function show(ContactPerson $contact_person)
    {
        $contact_person->load('country');
        return $this->respondWithItem($contact_person);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactPerson $contact_person)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', "email", Rule::unique('contact_person', 'email')->ignore($contact_person->id)],
            'phone' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable'],
            'client_id' => ['nullable', 'integer'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $create_item = $this->repositoryInterface->update($request->all(), $contact_person);
        return $this->respondWithCreated($create_item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactPerson $contact_person)
    {
        $this->repositoryInterface->delete($contact_person);
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
