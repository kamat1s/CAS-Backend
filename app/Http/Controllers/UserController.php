<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Student;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $userID = $request->user()->id;
        $role = $request->user()->role;

        $userData = null;

        switch ($role) {
            case 'student':
                $userData = Student::join('users', 'students.userID', '=', 'users.id')->where(
                    'students.userID',
                    $userID
                )->select('students.*', 'users.email')->first();
                break;
        }

        return response()->json(
            $userData,
        );
    }
}
