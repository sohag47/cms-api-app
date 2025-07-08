<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnums;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function validationRules()
    {
        return [
            'paginate' => ['nullable', 'string',  Rule::in(['yes', 'no'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'order_by' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function filterQuery(Request $request, $query)
    {
        // Apply status filter if provided
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Apply date filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->query('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->query('end_date'));
        }

        // Apply ordering
        $orderBy = $request->query('order_by', 'created_at'); // Default to 'created_at'
        $order = $request->query('order', 'desc'); // Default to 'desc'
        $query->orderBy($orderBy, $order);

        if ($request->filled('paginate') && $request->query('paginate') == 'no') {
            $items = $query->get();
            return $this->respondWithItem($items);
        }
        // Paginate results
        $perPage = $request->query('per_page', 10); // Default 10 per page
        $items = $query->paginate($perPage);

        return $items;
    }
}
