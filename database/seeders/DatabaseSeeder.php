<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Block;
use App\Models\Course;
use App\Models\College;
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

        $personalInformation = PersonalInformation::create([
            'sex' => 'M',
            'DOB' => Carbon::createFromFormat('m/d/Y', '07/21/2002')->toDateString(),
        ]);

        $collegeOne = College::create(
            [
                'collegeName' => 'College of Engineering',
                'collegeAbbreviation' => 'CoE',
            ]
        );

        College::create(
            [
                'collegeName' => 'College of Education',
                'collegeAbbreviation' => 'CEd',
            ]
        );

        College::create(
            [
                'collegeName' => 'College of Humanities, Arts, and Social Sciences',
                'collegeAbbreviation' => 'CHASS',
            ]
        );

        $course = Course::create(
            [
                'collegeID' => $collegeOne->id,
                'courseName' => 'Bachelor of Science in Computer Science',
                'courseAbbreviation' => 'BSCS'
            ]
        );

        $block = Block::create(
            [
                'block' => 'Block 4',
                'yearStanding' => '4',
                'courseID' => $course->id,
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
