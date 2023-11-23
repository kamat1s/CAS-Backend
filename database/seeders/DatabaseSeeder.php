<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Seeder;

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

        Student::create([
            'userID' => $user->id,
            'studentID' => '202011350',
            'name' => 'Ricky Jr. O. Francisco',
            'yearLevel' => 4,
        ]);
    }
}
