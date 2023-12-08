<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CpiController extends Controller
{
    public function getStatus(): JsonResponse
    {
        $response = [];

        return response()->json($response);
    }
}
