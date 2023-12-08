<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CounselorController extends Controller
{
    public function getCounselor(Request $request): JsonResponse
    {
        $userID = $request->user()->id;
        $userData = Employee::with('user', 'college')
            ->where('userID', $userID)
            ->first();

        $response = [
            'counselor' => $userData->only(['id', 'employeeID', 'name']),
            'college' => $userData->college,
            'user' => $userData->user,
        ];

        return response()->json($response);
    }

    public function getAvailability(Request $request): JsonResponse
    {
        try {
            $appointments = Appointment::with(['user', 'user.student.block.course.college', 'user.faculty.college'])
                ->where('status', 'Active')
                ->get();

            $takenSlots = [];

            foreach ($appointments as $appointment) {
                $user = $appointment->user;

                $counselorID = null;

                if ($user->role == "Student") {
                    $counselorID = $user->student->block->course->college->assignedCounselorID;
                } elseif ($user->role == "Faculty") {
                    $counselorID = $user->faculty->college->assignedCounselorID;
                }

                if ($counselorID == $request->counselorID) {
                    $schedule = $appointment->schedule;

                    $date = $schedule->date;
                    $timeSlot = $schedule->time;

                    if (!isset($takenSlots[$date]) || !in_array($timeSlot, $takenSlots[$date])) {
                        // Add the time slot if it doesn't exist
                        $takenSlots[$date][] = $timeSlot;
                    }
                }
            }

            $response = [
                'message' => 'Success',
                'takenSlots' => $takenSlots,
            ];

            return response()->json($response);
        } catch (\Exception $e) {

            $response = [
                'message' => 'Error',
                'error' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }
}
