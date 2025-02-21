<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LearningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = [
            "success" => true,
            "message" => "Item Found Successfully",
            "data" => null, 
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $response = [
            "success" => true,
            "message" => "Item Store Successfully",
            "data" => $request->all(), 
        ];
        return response()->json($response, Response::HTTP_ACCEPTED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $response = [
            "success" => true,
            "message" => "Item Found Successfully",
            "data" => [
                "id"=> $id,
                "name" => "Sohag"
            ], 
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $response = [
            "success" => true,
            "message" => "Item Update Successfully",
            "data" => [
                "id"=> $id,
                ...$request->all()
            ], 
        ];
        return response()->json($response, Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = [
            "success" => true,
            "message" => "Item Delete Successfully",
            "data" => [
                "id"=> $id,
            ], 
        ];
        return response()->json($response, Response::HTTP_OK);
    }
}
