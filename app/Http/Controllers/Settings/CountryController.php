<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Interfaces\RepositoryInterface;
use App\Models\Settings\Country;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    use ApiResponse;
    private $model;
    private $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface)
    {
        $this->model = Country::class;
        $this->repositoryInterface = $repositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'search'=> ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        // Query Builder for filtering
        $query = $this->model::query();

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
       
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        return $this->respondWithItem($country);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {

    }

    public function dropdown(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search'=> ['nullable', 'string', 'max:255'],            
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $query = $this->model::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->query('search') . '%');
        }

        $countries = $query->select('id', 'name')
            ->get()
            ->map(fn($country) => [
                'value' => $country->id,
                'label' => $country->name,
            ]);

        return $this->respondWithCollection($countries);
    }
}
