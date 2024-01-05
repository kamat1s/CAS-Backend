<?php

namespace App\Http\Controllers;

use App\Models\Sibling;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Guardian;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Models\EmergencyContact;
use App\Models\FamilyBackground;
use Illuminate\Http\JsonResponse;
use App\Models\AcademicBackground;
use App\Models\PhysicalHealthInfo;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalInformation;
use App\Http\Controllers\Controller;

class FacultyConroller extends Controller
{
    public function getFaculty(Request $request): JsonResponse
    {
        $userID = $request->user()->id;
        $userData = Employee::with('user', 'personalInformation', 'college')
            ->where('userID', $userID)
            ->first();

        $response = [
            'faculty' => $userData->only(['id', 'employeeID', 'name']),
            'user' => $userData->user,
            'college' => $userData->college->only(['id', 'collegeName', 'collegeAbbreviation', 'assignedCounselorID']),
            'personalInformation' => $userData->personalInformation,
        ];

        return response()->json($response);
    }

    public function getCPI(Request $request): JsonResponse
    {
        $userID = $request->user()->id;

        $userData = Employee::with('user', 'personalInformation.emergencyContact', 'familyBackground.father', 'familyBackground.mother', 'familyBackground.guardian', 'familyBackground.siblings', 'physicalHealthInfo', 'college', 'academicBackgrounds')
            ->where('userID', $userID)
            ->first();

        $response = [
            'faculty' => $userData->only(['id', 'employeeID', 'name']),
            'user' => $userData->user,
            'personalInformation' => $userData->personalInformation,
            'emergencyContact' => $userData->personalInformation->emergencyContact,
            'college' => $userData->college->only(['id', 'collegeName', 'collegeAbbreviation']),
            'familyBackground' => $userData->familyBackground ? $userData->familyBackground->only(['id', 'relationshipStatus', 'livingArrangement', 'siblingRank']) : null,
            'father' => $userData->familyBackground ? $userData->familyBackground->father : null,
            'mother' => $userData->familyBackground ? $userData->familyBackground->mother : null,
            'guardian' => $userData->familyBackground ? $userData->familyBackground->guardian : null,
            'siblings' => $userData->familyBackground ? $userData->familyBackground->siblings : null,
            'physicalHealthInfo' => $userData->physicalHealthInfo ? $userData->physicalHealthInfo : null,
            'academicBackgrounds' => $userData->academicBackgrounds,
        ];

        return response()->json($response);
    }

    public function updateCpi(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $faculty = Employee::where('userID', $request->user()->id)->first();

            $personalInformation = PersonalInformation::find($request->personalInformation['id']);

            $emergencyContact = null;
            if (!array_key_exists('id', $request->emergencyContact)) {
                $emergencyContact = EmergencyContact::create($request->emergencyContact);
                $personalInformation->emergencyContactID = $emergencyContact->id;
            } else {
                $emergencyContact = EmergencyContact::find($request->emergencyContact['id']);
                if ($emergencyContact) {
                    foreach ([
                        'name',
                        'contactNo',
                        'relation'
                    ] as $attribute) {
                        $emergencyContact->{$attribute} = $request->emergencyContact[$attribute];
                    }

                    $emergencyContact->save();
                }
            }

            if ($personalInformation) {
                foreach ([
                    'religion',
                    'civilStatus',
                    'emailAddress',
                    'mobileNo',
                    'telNo',
                    'presentAddress',
                    'permanentAddress',
                    'bestCharacteristics',
                    'specialSkills',
                    'goals',
                ] as $attribute) {
                    $personalInformation->{$attribute} = $request->personalInformation[$attribute];
                }

                $personalInformation->save();
            }

            $father = $this->createOrUpdateGuardian($request->father);
            $mother = $this->createOrUpdateGuardian($request->mother);
            $guardian = $this->createOrUpdateGuardian($request->guardian);

            $familyBackground = null;

            if (!array_key_exists('id', $request->familyBackground)) {
                $familyBackground = FamilyBackground::create(
                    [
                        'fatherID' => $father->id,
                        'motherID' => $mother->id,
                        'guardianID' => $guardian->id,
                        ...$request->familyBackground,
                    ]
                );

                $faculty->familyBackgroundID = $familyBackground->id;
            } else {
                $familyBackground = FamilyBackground::find($request->familyBackground['id']);
                if ($familyBackground) {
                    $familyBackground->fatherID = $father->id;
                    $familyBackground->motherID = $mother->id;
                    $familyBackground->guardianID = $guardian->id;

                    foreach ([
                        'relationshipStatus',
                        'livingArrangement',
                        'siblingRank',
                    ] as $attribute) {
                        $familyBackground->{$attribute} = $request->familyBackground[$attribute];
                    }

                    $familyBackground->save();
                }
            }

            foreach ($request->siblings as $siblingData) {
                if (!array_key_exists('id', $siblingData)) {
                    Sibling::create(
                        [
                            'familyBackgroundID' => $familyBackground->id,
                            ...$siblingData,
                        ]
                    );
                } else {
                    $sibling = Sibling::find($siblingData['id']);

                    if ($sibling) {
                        foreach ([
                            'name',
                            'sex',
                            'age',
                            'institute'
                        ] as $attribute) {
                            $sibling->{$attribute} = $siblingData[$attribute];
                        }

                        $sibling->save();
                    }
                }
            }

            $physicalHealthInfo = null;

            if (!array_key_exists('id', $request->physicalHealthInfo)) {
                $physicalHealthInfo = PhysicalHealthInfo::create(
                    $request->physicalHealthInfo
                );

                $faculty->physicalHealthInfoID = $physicalHealthInfo->id;
            } else {
                $physicalHealthInfo = PhysicalHealthInfo::find($request->physicalHealthInfo['id']);

                if ($physicalHealthInfo) {
                    foreach ([
                        'currentPhysicalHealth',
                        'physicalActivityEngagement',
                        'reasonForMedCare',
                        'medicineType',
                        'reasonForMedication',
                        'previousCounselingDetail',
                        'ongoingCounselingDetail'
                    ] as $attribute) {
                        $physicalHealthInfo->{$attribute} = $request->physicalHealthInfo[$attribute];
                    }
                }

                $physicalHealthInfo->save();
            }

            foreach ($request->academicBackgrounds as $academicBackgroundData) {
                if ($academicBackgroundData['schoolName'] !== null) {
                    if (!array_key_exists('id', $academicBackgroundData)) {
                        AcademicBackground::create(
                            [
                                ...$academicBackgroundData,
                                'userID' => $request->user()->id,
                            ]
                        );
                    } else {
                        $academicBackground = AcademicBackground::find($academicBackgroundData['id']);

                        if ($sibling) {
                            foreach ([
                                'level',
                                'schoolName',
                                'accomplishments',
                            ] as $attribute) {
                                $academicBackground->{$attribute} = $academicBackgroundData[$attribute];
                            }

                            $academicBackground->save();
                        }
                    }
                }
            }

            $faculty->save();

            DB::commit();

            $response = [
                'message' => 'CPI Updated',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, log, or return an error response
            $response = [
                'message' => 'CPI Update Failed',
                'error' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }

    function createOrUpdateGuardian($requestData)
    {
        $guardian = null;

        if (!array_key_exists('id', $requestData)) {
            $guardian = Guardian::create($requestData);
        } else {
            $guardian = Guardian::find($requestData['id']);

            if ($guardian) {
                foreach ([
                    'name',
                    'address',
                    'contactNo',
                    'occupation',
                ] as $attribute) {
                    $guardian->{$attribute} = $requestData[$attribute];
                }

                $guardian->save();
            }
        }

        return $guardian;
    }

    public function getCpiStatus(Request $request): JsonResponse
    {
        $userID = $request->user()->id;

        $userData = Employee::where('userID', $userID)
            ->first()->only(['familyBackgroundID', 'physicalHealthInfoID']);


        return response()->json([
            'cpiStatus' => $userData['familyBackgroundID'] != null &&
                $userData['physicalHealthInfoID'] != null
        ]);
    }

    public function getFacultyStudents(Request $request): JsonResponse
    {
        $facultyID = $request->facultyID;

        $facultyStudents = SchoolClass::where('facultyID', $facultyID)
            ->with('students', 'students.personalInformation', 'students.block.course.college', 'students.user')
            ->get()
            ->flatMap(function ($class) {
                return $class->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'studentID' => $student->studentID,
                        'name' => $student->name,
                        'collegeName' => $student->block->course->college->collegeName,
                        'email' => $student->user->email,
                        'mobileNo' => $student->personalInformation->mobileNo,
                        'courseAbbreviation' => $student->block->course->courseAbbreviation,
                        'year' => $student->block->yearStanding,
                        'block' => $student->block->block,
                    ];
                });
            });

        return response()->json([
            'students' => $facultyStudents,
        ]);
    }
}
