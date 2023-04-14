<?php

namespace App\Http\Controllers;

use App\Http\Resources\InterestsResource;
use App\Models\Interest;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Here you can',
            'data' => [
                'interests' => InterestsResource::collection(Interest::all())
            ]
        ]);
    }
}