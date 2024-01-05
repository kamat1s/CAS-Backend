<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function newAppointment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $preferredDate1 = Carbon::parse($request->preferredDate1);
            $preferredSchedule1 = $this->newSchedule($preferredDate1, $request->preferredTime1);
            $preferredDate2 = Carbon::parse($request->preferredDate2);
            $preferredSchedule2 = $this->newSchedule($preferredDate2, $request->preferredTime2);
            $preferredDate3 = Carbon::parse($request->preferredDate3);
            $preferredSchedule3 = $this->newSchedule($preferredDate3, $request->preferredTime3);

            Appointment::create(
                [
                    'userID' => $request->userID,
                    'contactNo' => $request->contactNo,
                    'meetingType' => $request->meetingType,
                    'description' => $request->description,
                    'preferredSchedule1ID' => $preferredSchedule1->id,
                    'preferredSchedule2ID' => $preferredSchedule2->id,
                    'preferredSchedule3ID' => $preferredSchedule3->id,
                    'status' => "Pending",
                ]
            );

            DB::commit();

            $response = [
                'message' => 'Appointment created',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, log, or return an error response
            $response = [
                'message' => 'Appoinment Creation Failed',
                'error' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }

    public function approveAppointment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::findOrFail($request->id);

            $appointment->status = "Active";
            $appointment->scheduleID = $request->approvedSchedule;

            $appointment->save();

            DB::commit();

            $response = [
                'message' => 'Appointment approved',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, log, or return an error response
            $response = [
                'message' => 'Appointment Approval Failed',
                'error' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }

    public function cancelAppointment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::findOrFail($request->id);

            $appointment->status = "Cancelled";

            $appointment->save();

            DB::commit();

            $response = [
                'message' => 'Appointment cancelled',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, log, or return an error response
            $response = [
                'message' => 'Appointment Cancellation Failed',
                'error' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }

    public function markNoShow(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::findOrFail($request->id);

            $appointment->status = "No-Show";

            $appointment->save();

            DB::commit();

            $response = [
                'message' => 'Appointment marked as no-show',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, log, or return an error response
            $response = [
                'message' => 'Process Failed',
                'error' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }

    public function markDone(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::findOrFail($request->id);

            $appointment->remarks = $request->remarks;
            $appointment->status = "Done";

            $appointment->save();

            DB::commit();

            $response = [
                'message' => 'Appointment marked as done',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, log, or return an error response
            $response = [
                'message' => 'Process Failed',
                'error' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }

    public function followUpAppointment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $scheduleDate = Carbon::parse($request->date);
            $schedule = $this->newSchedule($scheduleDate, $request->time);

            Appointment::create(
                [
                    'userID' => $request->userID,
                    'contactNo' => $request->contactNo,
                    'meetingType' => $request->meetingType,
                    'description' => $request->description,
                    'scheduleID' => $schedule->id,
                    'status' => "Active",
                ]
            );

            DB::commit();

            $response = [
                'message' => 'Follow-up Appointment created',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, log, or return an error response
            $response = [
                'message' => 'Follow-up Appoinment Creation Failed',
                'error' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }


    public function getCounselorPendingAppointments(Request $request): JsonResponse
    {
        $pendingAppointments = Appointment::with(['user', 'user.student.block.course.college', 'user.faculty.college'])
            ->where('status', '=', 'Pending')
            ->get();

        $counselorAppointments = [];

        foreach ($pendingAppointments as $appointment) {
            $user = $appointment->user;

            $counselorID = null;

            if ($user->role == "Student") {
                $counselorID = $user->student->block->course->college->assignedCounselorID;
            } elseif ($user->role == "Faculty") {
                $counselorID = $user->faculty->college->assignedCounselorID;
            }

            if ($counselorID == $request->counselorID) {
                $counselorAppointments[] = [
                    'appointment' =>
                    $appointment->only('id', 'contactNo', 'meetingType', 'description', 'created_at'),
                    'user' => $appointment->user->only('email', 'role'),
                    'college' => $appointment->user->student ? $appointment->user->student->block->course->college->only('collegeAbbreviation') : $appointment->user->faculty->college->only('collegeAbbreviation'),
                    'student' => $appointment->user->student ? $appointment->user->student->only('studentID', 'name') : null,
                    'faculty' => $appointment->user->faculty ? $appointment->user->faculty->only('employeeID', 'name') : null,
                    'preferredSchedule1' => $appointment->preferredSchedule1->only('id', 'date', 'time'),
                    'preferredSchedule2' => $appointment->preferredSchedule2->only('id', 'date', 'time'),
                    'preferredSchedule3' => $appointment->preferredSchedule3->only('id', 'date', 'time'),
                ];
            }
        }

        return response()->json($counselorAppointments);
    }

    public function getCounselorUpcomingAppointments(Request $request): JsonResponse
    {
        $upcomingAppointmets = Appointment::with(['user', 'user.student.block.course.college', 'user.faculty.college'])
            ->whereIn('status', ['Active', 'Follow-up'])
            ->get();

        $counselorAppointments = [];

        foreach ($upcomingAppointmets as $appointment) {
            $user = $appointment->user;

            $counselorID = null;

            if ($user->role == "Student") {
                $counselorID = $user->student->block->course->college->assignedCounselorID;
            } elseif ($user->role == "Faculty") {
                $counselorID = $user->faculty->college->assignedCounselorID;
            }

            if ($counselorID == $request->counselorID) {
                $counselorAppointments[] = [
                    'appointment' =>
                    $appointment->only('id', 'contactNo', 'meetingType', 'description', 'status', 'created_at'),
                    'user' => $appointment->user->only('id', 'email', 'role'),
                    'college' => $appointment->user->student ? $appointment->user->student->block->course->college->only('collegeAbbreviation', 'collegeName') : $appointment->user->faculty->college->only('collegeAbbreviation'),
                    'course' => $appointment->user->student ? $appointment->user->student->block->course->only('courseName') : null,
                    'student' => $appointment->user->student ? $appointment->user->student->only('studentID', 'name') : null,
                    'faculty' => $appointment->user->faculty ? $appointment->user->faculty->only('employeeID', 'name') : null,
                    'schedule' => $appointment->schedule->only('id', 'date', 'time'),
                ];
            }
        }

        return response()->json($counselorAppointments);
    }

    public function getStudentPendingAppointments(Request $request): JsonResponse
    {
        $pendingAppointments = Appointment::with(['user', 'user.student.block.course.college'])
            ->where('status', '=', 'Pending')
            ->get();

        $studentAppointments = [];

        foreach ($pendingAppointments as $appointment) {
            $user = $appointment->user;

            if ($user->id == $request->user()->id) {
                $studentAppointments[] = [
                    'appointment' =>
                    $appointment->only('id', 'contactNo', 'meetingType', 'description', 'created_at'),
                    'user' => $appointment->user->only('email', 'role'),
                    'college' => $appointment->user->student->block->course->college->only('collegeAbbreviation'),
                    'student' => $appointment->user->student->only('studentID', 'name'),
                    'preferredSchedule1' => $appointment->preferredSchedule1->only('id', 'date', 'time'),
                    'preferredSchedule2' => $appointment->preferredSchedule2->only('id', 'date', 'time'),
                    'preferredSchedule3' => $appointment->preferredSchedule3->only('id', 'date', 'time'),
                ];
            }
        }

        return response()->json($studentAppointments);
    }

    public function getFacultyPendingAppointments(Request $request): JsonResponse
    {
        $pendingAppointments = Appointment::with(['user', 'user.faculty.college'])
            ->where('status', '=', 'Pending')
            ->get();

        $facultyAppointments = [];

        foreach ($pendingAppointments as $appointment) {
            $user = $appointment->user;

            if ($user->id == $request->user()->id) {
                $facultyAppointments[] = [
                    'appointment' =>
                    $appointment->only('id', 'contactNo', 'meetingType', 'description', 'created_at'),
                    'user' => $appointment->user->only('email', 'role'),
                    'college' => $appointment->user->faculty->college->only('collegeAbbreviation'),
                    'faculty' => $appointment->user->faculty->only('employeeID', 'name'),
                    'preferredSchedule1' => $appointment->preferredSchedule1->only('id', 'date', 'time'),
                    'preferredSchedule2' => $appointment->preferredSchedule2->only('id', 'date', 'time'),
                    'preferredSchedule3' => $appointment->preferredSchedule3->only('id', 'date', 'time'),
                ];
            }
        }

        return response()->json($facultyAppointments);
    }

    public function getStudentUpcomingAppointments(Request $request): JsonResponse
    {
        $upcomingAppointmets = Appointment::with(['user', 'user.student.block.course.college'])
            ->whereIn('status', ['Active', 'Follow-up'])
            ->get();

        $studentAppointments = [];

        foreach ($upcomingAppointmets as $appointment) {
            $user = $appointment->user;

            if ($user->id == $request->user()->id) {
                $studentAppointments[] = [

                    'appointment' =>
                    $appointment->only('id', 'meetingType', 'description', 'status', 'created_at'),
                    'user' => $appointment->user->only('id', 'email', 'role'),
                    'college' => $appointment->user->student->block->course->college->only('collegeAbbreviation', 'collegeName'),
                    'course' => $appointment->user->student->block->course->only('courseName'),
                    'student' => $appointment->user->student->only('studentID', 'name'),
                    'schedule' => $appointment->schedule->only('id', 'date', 'time'),
                ];
            }
        }

        return response()->json($studentAppointments);
    }

    public function getFacultyUpcomingAppointments(Request $request): JsonResponse
    {
        $upcomingAppointmets = Appointment::with(['user', 'user.faculty.college'])
            ->whereIn('status', ['Active', 'Follow-up'])
            ->get();

        $facultyAppointments = [];

        foreach ($upcomingAppointmets as $appointment) {
            $user = $appointment->user;

            if ($user->id == $request->user()->id) {
                $facultyAppointments[] = [
                    'appointment' =>
                    $appointment->only('id', 'meetingType', 'description', 'status', 'created_at'),
                    'user' => $appointment->user->only('id', 'email', 'role'),
                    'college' => $appointment->user->faculty->college->only('collegeAbbreviation', 'collegeName'),
                    'faculty' => $appointment->user->faculty->only('employeeID', 'name'),
                    'schedule' => $appointment->schedule->only('id', 'date', 'time'),
                ];
            }
        }

        return response()->json($facultyAppointments);
    }

    public function getStudentAppointmentHistory(Request $request): JsonResponse
    {
        $appointments = Appointment::with(['user', 'user.student.block.course.college'])
            ->whereIn('status', ['Done', 'Cancelled'])
            ->get();

        $history = [];

        foreach ($appointments as $appointment) {
            $user = $appointment->user;

            if ($user->id == $request->user()->id) {
                $history[] = [
                    'appointment' =>
                    $appointment->only('id', 'meetingType', 'description', 'remarks', 'status', 'created_at'),
                    'user' => $appointment->user->only('id', 'email', 'role'),
                    'college' => $appointment->user->student->block->course->college->only('collegeAbbreviation', 'collegeName'),
                    'course' => $appointment->user->student->block->course->only('courseName'),
                    'student' => $appointment->user->student->only('studentID', 'name'),
                    'schedule' => $appointment->schedule ? $appointment->schedule->only('id', 'date', 'time') : null,
                ];
            }
        }

        return response()->json($history);
    }

    public function getFacultyAppointmentHistory(Request $request): JsonResponse
    {
        $appointments = Appointment::with(['user', 'user.faculty.college'])
            ->whereIn('status', ['Done', 'Cancelled'])
            ->get();

        $history = [];

        foreach ($appointments as $appointment) {
            $user = $appointment->user;

            if ($user->id == $request->user()->id) {
                $history[] = [
                    'appointment' =>
                    $appointment->only('id', 'meetingType', 'description', 'remarks', 'status', 'created_at'),
                    'user' => $appointment->user->only('id', 'email', 'role'),
                    'college' => $appointment->user->faculty->college->only('collegeAbbreviation', 'collegeName'),
                    'faculty' => $appointment->user->faculty->only('employeeID', 'name'),
                    'schedule' => $appointment->schedule ? $appointment->schedule->only('id', 'date', 'time') : null,
                ];
            }
        }

        return response()->json($history);
    }

    public function getCounselorAppointmentHistory(Request $request): JsonResponse
    {
        $appointments = Appointment::with(['user', 'user.student.block.course.college', 'user.faculty.college'])
            ->whereIn('status', ['Done', 'Cancelled'])
            ->get();

        $history = [];

        foreach ($appointments as $appointment) {
            $user = $appointment->user;

            $counselorID = null;

            if ($user->role == "Student") {
                $counselorID = $user->student->block->course->college->assignedCounselorID;
            } elseif ($user->role == "Faculty") {
                $counselorID = $user->faculty->college->assignedCounselorID;
            }

            if ($counselorID == $request->counselorID) {
                $history[] = [
                    'appointment' =>
                    $appointment->only('id', 'contactNo', 'meetingType', 'description', 'remarks', 'status', 'created_at'),
                    'user' => $appointment->user->only('email', 'role'),
                    'college' => $appointment->user->student ? $appointment->user->student->block->course->college->only('collegeAbbreviation') : $appointment->user->faculty->college->only('collegeAbbreviation'),
                    'student' => $appointment->user->student ? $appointment->user->student->only('studentID', 'name') : null,
                    'faculty' => $appointment->user->faculty ? $appointment->user->faculty->only('employeeID', 'name') : null,
                    'schedule' => $appointment->schedule ? $appointment->schedule->only('id', 'date', 'time') : null,
                ];
            }
        }

        return response()->json($history);
    }

    public function newSchedule($date, $time): Schedule
    {
        return Schedule::create(
            [
                'date' => $date,
                'time' => $time,
            ]
        );
    }
}
