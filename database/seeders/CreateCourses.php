<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateCourses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            'Business Foundation',
            'Law Foundation',
            'Computing Foundation',
            'BSc (Hons) Computer Science',
            'BSc (Hons) Computer Science (Cloud Technologies)',
            'BSc (Hons) Computer Science (Internet and Web Management)',
            'BSc (Hons) Computer Science (Network Computing)',
            'BSc (Hons) Computer Science (Software Development)',
            'BSc (Hons) Cyber Security',
            'BSc (Hons) Accounting and Finance',
            'BSc (Hons) Digital and Social Media Marketing',
            'BSc (Hons) International Business Management',
            'BSc (Hons) Business Management (Sustainability)',
            'BSc (Hons) Business Management (Human Resource Management)',
            'BSc (Hons) Business Management (Innovation and Entrepreneurship)',
            'BSc (Hons) Business Management',
            'LLB (Hons) Law',
            'LLB (Hons) Law – Digital',
            'LLB (Hons) Law (Part-Time)',
        ];

        foreach ($courses as $course) {
            DB::table('courses')->insert([
                'CourseName' => $course,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
