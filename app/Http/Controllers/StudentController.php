<?php

namespace App\Http\Controllers;

use App\Models\Sibling;
use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Http\Request;
use App\Models\EmergencyContact;
use App\Models\FamilyBackground;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalInformation;
use App\Http\Controllers\Controller;
use App\Models\AcademicBackground;
use App\Models\Career;
use App\Models\PhysicalHealthInfo;

class StudentController extends Controller
{

    public function getStudent(Request $request): JsonResponse
    {
        $userID = $request->user()->id;
        $userData = Student::with('user', 'block.course.college', 'personalInformation')
            ->where('userID', $userID)
            ->first();

        $response = [
            'student' => $userData->only(['id', 'studentID', 'name', 'yearLevel']),
            'user' => $userData->user,
            'college' => $userData->block->course->college->only(['id', 'collegeName', 'collegeAbbreviation', 'assignedCounselorID']),
            'course' => $userData->block->course->only(['id', 'collegeID', 'courseName', 'courseAbbreviation']),
            'personalInformation' => $userData->personalInformation,
        ];

        return response()->json($response);
    }

    public function getCPI(Request $request): JsonResponse
    {
        $userID = $request->user()->id;

        $userData = Student::with('user', 'personalInformation.emergencyContact', 'familyBackground.father', 'familyBackground.mother', 'familyBackground.guardian', 'familyBackground.siblings', 'physicalHealthInfo', 'career', 'block.course.college', 'academicBackgrounds')
            ->where('userID', $userID)
            ->first();

        $response = [
            'student' => $userData->only(['id', 'studentID', 'name', 'yearLevel']),
            'user' => $userData->user,
            'personalInformation' => $userData->personalInformation,
            'emergencyContact' => $userData->personalInformation->emergencyContact,
            'block' => $userData->block->only(['id', 'block', 'yearStanding']),
            'college' => $userData->block->course->college->only(['id', 'collegeName', 'collegeAbbreviation']),
            'course' => $userData->block->course->only(['id', 'collegeID', 'courseName', 'courseAbbreviation']),
            'familyBackground' => $userData->familyBackground ? $userData->familyBackground->only(['id', 'relationshipStatus', 'livingArrangement', 'siblingRank']) : null,
            'father' => $userData->familyBackground ? $userData->familyBackground->father : null,
            'mother' => $userData->familyBackground ? $userData->familyBackground->mother : null,
            'guardian' => $userData->familyBackground ? $userData->familyBackground->guardian : null,
            'siblings' => $userData->familyBackground ? $userData->familyBackground->siblings : null,
            'physicalHealthInfo' => $userData->physicalHealthInfo ? $userData->physicalHealthInfo : null,
            'academicBackgrounds' => $userData->academicBackgrounds,
            'career' => $userData->career,
        ];

        return response()->json($response);
    }

    public function updateCpi(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $student = Student::where('userID', $request->user()->id)->first();

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

                $student->familyBackgroundID = $familyBackground->id;
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

                $student->physicalHealthInfoID = $physicalHealthInfo->id;
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

            $career = null;

            if (!array_key_exists('id', $request->career)) {
                $career = Career::create(
                    $request->career
                );

                $student->careerID = $career->id;
            } else {
                $career = Career::find($request->career['id']);

                if ($career) {
                    foreach ([
                        'firstCourseID',
                        'secondCourseID',
                        'thirdCourseID',
                        'factors',
                        'otherFactors',
                        'futureVision'
                    ] as $attribute) {
                        $career->{$attribute} = $request->career[$attribute];
                    }

                    $career->save();
                }
            }

            $student->save();

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
}
