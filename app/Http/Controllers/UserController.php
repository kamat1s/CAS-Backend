<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function getUser(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
