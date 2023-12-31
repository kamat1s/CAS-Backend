<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\CounselorController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FacultyConroller;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Models\College;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth Api
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/validate-access-token', function (Request $request) {
    return response()->json(
        [
            'role' => $request->user()->role,
            'message' => 'Authenticated',
        ]
    );
})->middleware('auth:sanctum');

// User Api
Route::get('/user', [UserController::class, 'getUser'])->middleware('auth:sanctum');

// Student Api
Route::get('/student/cpi', [StudentController::class, 'getCPI'])->middleware('auth:sanctum');
Route::post('/student/cpis', [StudentController::class, 'getCPIs'])->middleware('auth:sanctum');
Route::post('/student/cpi', [StudentController::class, 'updateCPI'])->middleware('auth:sanctum');
Route::get('/student/cpi/status', [StudentController::class, 'getCpiStatus'])->middleware('auth:sanctum');
Route::get('/student', [StudentController::class, 'getStudent'])->middleware('auth:sanctum');

// Faculty
Route::get('/faculty/cpi', [FacultyConroller::class, 'getCPI'])->middleware('auth:sanctum');
Route::post('/faculty/cpi', [FacultyConroller::class, 'updateCPI'])->middleware('auth:sanctum');
Route::get('/faculty/cpi/status', [FacultyConroller::class, 'getCpiStatus'])->middleware('auth:sanctum');
Route::get('/faculty', [FacultyConroller::class, 'getFaculty'])->middleware('auth:sanctum');
Route::post('/faculty/students', [FacultyConroller::class, 'getFacultyStudents'])->middleware('auth:sanctum');

// Counselor Api
Route::post('/counselor/availability', [CounselorController::class, 'getAvailability'])->middleware('auth:sanctum');
Route::get('/counselor', [CounselorController::class, 'getCounselor'])->middleware('auth:sanctum');

// Course Api
Route::get('/courses', [CourseController::class, 'getCourses'])->middleware('auth:sanctum');

// College Api
Route::get('/colleges', [CollegeController::class, 'getColleges'])->middleware('auth:sanctum');
Route::get('/colleges/statistics', [CollegeController::class, 'getCollegesStatistics'])->middleware('auth:sanctum');

// Appointment Api
Route::post('/appointment/new', [AppointmentController::class, 'newAppointment'])->middleware('auth:sanctum');
Route::post('/appointment/approve', [AppointmentController::class, 'approveAppointment'])->middleware('auth:sanctum');
Route::post('/appointment/cancel', [AppointmentController::class, 'cancelAppointment'])->middleware('auth:sanctum');
Route::post('/appointment/no-show', [AppointmentController::class, 'markNoShow'])->middleware('auth:sanctum');
Route::post('/appointment/done', [AppointmentController::class, 'markDone'])->middleware('auth:sanctum');
Route::post('/appointment/follow-up', [AppointmentController::class, 'followUpAppointment'])->middleware('auth:sanctum');

Route::post('/appointment/counselor/pending', [AppointmentController::class, 'getCounselorPendingAppointments'])->middleware('auth:sanctum');
Route::post('/appointment/counselor/upcoming', [AppointmentController::class, 'getCounselorUpcomingAppointments'])->middleware('auth:sanctum');
Route::post('/appointment/counselor/history', [AppointmentController::class, 'getCounselorAppointmentHistory'])->middleware('auth:sanctum');

Route::get('/appointment/faculty/pending', [AppointmentController::class, 'getFacultyPendingAppointments'])->middleware('auth:sanctum');
Route::get('/appointment/faculty/upcoming', [AppointmentController::class, 'getFacultyUpcomingAppointments'])->middleware('auth:sanctum');
Route::get('/appointment/faculty/history', [AppointmentController::class, 'getFacultyAppointmentHistory'])->middleware('auth:sanctum');

Route::get('/appointment/student/pending', [AppointmentController::class, 'getStudentPendingAppointments'])->middleware('auth:sanctum');
Route::get('/appointment/student/upcoming', [AppointmentController::class, 'getStudentUpcomingAppointments'])->middleware('auth:sanctum');
Route::get('/appointment/student/history', [AppointmentController::class, 'getStudentAppointmentHistory'])->middleware('auth:sanctum');

// Referral Api
Route::post('/referral/new', [ReferralController::class, 'newReferral'])->middleware('auth:sanctum');
Route::post('/referral/approve', [ReferralController::class, 'approveReferral'])->middleware('auth:sanctum');
Route::get('/referral/counselor', [ReferralController::class, 'getCounselorReferrals'])->middleware('auth:sanctum');
Route::get('/referral/student', [ReferralController::class, 'getStudentReferrals'])->middleware('auth:sanctum');
Route::get('/referral/faculty', [ReferralController::class, 'getFacultyReferrals'])->middleware('auth:sanctum');
