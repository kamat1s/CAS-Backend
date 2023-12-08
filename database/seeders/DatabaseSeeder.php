<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Block;
use App\Models\Course;
use App\Models\College;
use App\Models\EmergencyContact;
use App\Models\Employee;
use App\Models\Student;
use Illuminate\Database\Seeder;
use App\Models\PersonalInformation;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $user = User::create(
            [
                'email' => 'rjofrancisco2020@plm.edu.ph',
                'password' => '12345',
                'role' => 'Student',
            ],
        );

        $user2 = User::create(
            [
                'email' => 'counselor1@plm.edu.ph',
                'password' => '12345',
                'role' => 'Counselor',
            ]
        );

        $counselor1 = Employee::create(
            [
                'userID' => $user2->id,
                'employeeID' => '202000001',
                'name' => 'Counselor 1',
            ]
        );

        $user3 = User::create(
            [
                'email' => 'counselor2@plm.edu.ph',
                'password' => '12345',
                'role' => 'Counselor',
            ]
        );

        $counselor2 = Employee::create(
            [
                'userID' => $user3->id,
                'employeeID' => '202000002',
                'name' => 'Counselor 2',
            ]
        );

        $user4 = User::create(
            [
                'email' => 'counselor3@plm.edu.ph',
                'password' => '12345',
                'role' => 'Counselor',
            ]
        );

        $counselor3 = Employee::create(
            [
                'userID' => $user4->id,
                'employeeID' => '202000003',
                'name' => 'Counselor 3',
            ]
        );

        $emergencyContact = EmergencyContact::create([
            "name" => "Rosalia Oro Francisco",
            "contactNo" => "09762405585",
            "relation" => "Mother",
        ]);

        $personalInformation = PersonalInformation::create([
            'sex' => 'M',
            'DOB' => Carbon::createFromFormat('m/d/Y', '07/21/2002')->toDateString(),
            'religion' => 'Roman Catholic',
            'civilStatus' => 'Single',
            'emailAddress' => 'rickyfranciscojr@outlook.com',
            'mobileNo' => '09762405585',
            'presentAddress' => '321-A Paraiso St. Tondo, Manila',
            'permanentAddress' => '321-A Paraiso St. Tondo, Manila',
            'emergencyContactID' => $emergencyContact->id,
        ]);

        $collegeOne = College::create(
            [
                'collegeName' => 'College of Engineering',
                'collegeAbbreviation' => 'CoE',
                'assignedCounselorID' => $counselor1->id,
            ]
        );

        $courseOne = Course::create(
            [
                'collegeID' => $collegeOne->id,
                'courseName' => 'Bachelor of Science in Computer Science',
                'courseAbbreviation' => 'BSCS',
            ]
        );

        $user5 = User::create(
            [
                'email' => 'faculty1@plm.edu.ph',
                'password' => '12345',
                'role' => 'Faculty',
            ]
        );

        Employee::create(
            [
                'userID' => $user5->id,
                'employeeID' => '202010001',
                'collegeID' => $collegeOne->id,
                'name' => 'Faculty 1',
            ]
        );

        Course::create(
            [
                'collegeID' => $collegeOne->id,
                'courseName' => 'Bachelor of Science in Information Technology',
                'courseAbbreviation' => 'BSIT'
            ]
        );

        Course::create(
            [
                'collegeID' => $collegeOne->id,
                'courseName' => 'Bachelor of Science in Mechanical Engineering',
                'courseAbbreviation' => 'BSME'
            ]
        );

        $collegeTwo = College::create(
            [
                'collegeName' => 'College of Education',
                'collegeAbbreviation' => 'CEd',
                'assignedCounselorID' => $counselor2->id,
            ]
        );

        $user6 = User::create(
            [
                'email' => 'faculty2@plm.edu.ph',
                'password' => '12345',
                'role' => 'Faculty',
            ]
        );

        Employee::create(
            [
                'userID' => $user6->id,
                'employeeID' => '202010002',
                'collegeID' => $collegeTwo->id,
                'name' => 'Faculty 2',
            ]
        );

        Course::create(
            [
                'collegeID' => $collegeTwo->id,
                'courseName' => 'Bachelor of Secondary Education major in Social Studies',
                'courseAbbreviation' => '(BSEd-SS)'
            ]
        );

        Course::create(
            [
                'collegeID' => $collegeTwo->id,
                'courseName' => 'Bachelor of Secondary Education major in English',
                'courseAbbreviation' => '(BSEd-Eng)'
            ]
        );

        Course::create(
            [
                'collegeID' => $collegeTwo->id,
                'courseName' => 'Bachelor of Secondary Education major Mathematics',
                'courseAbbreviation' => '(BSEd-Math)'
            ]
        );

        $collegeThree = College::create(
            [
                'collegeName' => 'College of Humanities, Arts, and Social Sciences',
                'collegeAbbreviation' => 'CHASS',
                'assignedCounselorID' => $counselor3->id,
            ]
        );

        Course::create(
            [
                'collegeID' => $collegeThree->id,
                'courseName' => 'Bachelor of Arts in Communication',
                'courseAbbreviation' => 'BAC'
            ]
        );

        Course::create(
            [
                'collegeID' => $collegeThree->id,
                'courseName' => 'Bachelor of Arts in Public Relations',
                'courseAbbreviation' => 'BAPR'
            ]
        );

        Course::create(
            [
                'collegeID' => $collegeThree->id,
                'courseName' => 'Bachelor of Science in Social Work',
                'courseAbbreviation' => 'BS SW'
            ]
        );


        $block = Block::create(
            [
                'block' => 'Block 4',
                'yearStanding' => '4',
                'courseID' => $courseOne->id,
            ]
        );


        Student::create([
            'userID' => $user->id,
            'studentID' => '202011350',
            'name' => 'Ricky Jr. O. Francisco',
            'personalInformationID' => $personalInformation->id,
            'blockID' => $block->id,
            'yearLevel' => 4,
        ]);
    }
}
