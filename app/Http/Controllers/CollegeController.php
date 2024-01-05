<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Employee;

class CollegeController extends Controller
{
    public function getColleges(): JsonResponse
    {
        $colleges = College::all();

        return response()->json($colleges);
    }

    public function getCollegesStatistics(Request $request): JsonResponse
    {
        $counselorID = $request->user()->counselor['id'];

        $colleges = College::where('assignedCounselorID', $counselorID)
            ->get();

        foreach ($colleges as $college) {
            $students = Student::with('user', 'block.course.college')
                ->whereHas('block.course.college', function ($query) use ($counselorID) {
                    $query->where('assignedCounselorID', $counselorID);
                })
                ->get();

            /*
            $faculties = Employee::with('user', 'college')
                ->whereHas('college', function ($query) use ($counselorID) {
                    $query->where('assignedCounselorID', $counselorID);
                })
                ->get();
            */

            $totalAppointments = 0;
            $studentCPIs = [];

            foreach ($students as $student) {
                $userID = $student->user['id'];
                $totalAppointments += Appointment::where('userID', $userID)
                    ->get()
                    ->count();

                $userData = Student::where('userID', $userID)
                    ->first()->only(['familyBackgroundID', 'physicalHealthInfoID', 'careerID']);

                if (
                    $userData['familyBackgroundID'] != null &&
                    $userData['physicalHealthInfoID'] != null
                    && $userData['careerID'] != null
                ) {
                    $studentCPIs[] = [
                        'userID' => $student->user->id,
                        'name' => $student->name,
                        'studentID' => $student->studentID,
                        'course' => $student->block->course->courseAbbreviation
                    ];
                }
            }

            /*
            foreach ($faculties as $faculty) {
                $userID = $faculty->user['id'];

                $totalAppointments += Appointment::where('userID',  $userID)
                    ->get()
                    ->count();

                $userData = Employee::where('userID', $userID)
                    ->first()->only(['familyBackgroundID', 'physicalHealthInfoID']);

                if ($userData['familyBackgroundID'] != null && $userData['physicalHealthInfoID'] != null) {
                    $totalCPIs += 1;
                }
            }
            */

            $college['totalAppointments'] = $totalAppointments;
            $college['studentCPIs'] = $studentCPIs;
        }


        return response()->json([
            "collegeStats" => $colleges,
        ]);
    }
}
