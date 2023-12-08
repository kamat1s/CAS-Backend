<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\College;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    public function getColleges(): JsonResponse
    {
        $colleges = College::all();

        return response()->json($colleges);
    }
}
