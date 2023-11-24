<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $userID = $request->user()->id;
        $role = $request->user()->role;

        $userData = null;

        switch ($role) {
            case 'Student':
                $userData = Student::with('user', 'personalInformation.emergencyContact', 'familyBackground.father', 'familyBackground.mother', 'familyBackground.guardian', 'familyBackground.siblings', 'physicalHealthInfo', 'career', 'block.course.college')
                    ->where('userID', $userID)
                    ->first();

                $response = [
                    'student' => $userData->only(['id', 'studentID', 'name', 'yearLevel', 'created_at', 'updated_at']),
                    'user' => $userData->user,
                    'personalInformation' => $userData->personalInformation,
                    'emergencyContact' => $userData->emergencyContact,
                    'block' => $userData->block->only(['id', 'block', 'yearStanding', 'created_at', 'updated_at']),
                    'college' => $userData->block->course->college->only(['id', 'collegeName', 'collegeAbbreviation', 'created_at', 'updated_at']),
                    'course' => $userData->block->course->only(['id', 'collegeID', 'courseName', 'courseAbbreviation', 'created_at', 'updated_at']),
                    'familyBackground' => $userData->familyBackground ? $userData->familyBackground->only(['id', 'relationshipStatus', 'livingArrangement', 'siblingRank']) : null,
                    'father' => $userData->familyBackground ? $userData->familyBackground->father : null,
                    'mother' => $userData->familyBackground ? $userData->familyBackground->mother : null,
                    'guardian' => $userData->familyBackground ? $userData->familyBackground->guardian : null,
                    'siblings' => $userData->familyBackground ? $userData->familyBackground->siblings : null
                ];

                return response()->json($response);
        }
    }
}
