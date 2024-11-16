<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateCorucesLevels extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseLevels = [
            'Business Foundation' => ['Year 1 - Sem 1', 'Year 1 - Sem 2'],
            'Law Foundation' => ['Year 1 - Sem 1', 'Year 1 - Sem 2'],
            'Computing Foundation' => ['Year 1 - Sem 1', 'Year 1 - Sem 2'],
            'BSc (Hons) Computer Science' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Computer Science (Cloud Technologies)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Computer Science (Internet and Web Management)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Computer Science (Network Computing)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Computer Science (Software Development)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Cyber Security' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Accounting and Finance' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Digital and Social Media Marketing' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) International Business Management' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Business Management (Sustainability)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Business Management (Human Resource Management)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Business Management (Innovation and Entrepreneurship)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Business Management' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'LLB (Hons) Law' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'LLB (Hons) Law – Digital' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'LLB (Hons) Law (Part-Time)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
                'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
        ];

        foreach ($courseLevels as $courseName => $levels) {
            $course = DB::table('cources')->where('CourseName', $courseName)->first();

            if ($course) {
                foreach ($levels as $level) {
                    DB::table('cource_levels')->insert([
                        'cource_id' => $course->id,
                        'level' => $level,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
