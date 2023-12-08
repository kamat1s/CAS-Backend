<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function getCourses(): JsonResponse
    {
        $courses = Course::all();

        return response()->json($courses);
    }
}
