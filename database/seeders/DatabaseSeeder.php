<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@apiit.lk', // Email with the required domain
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // You can change this to any default password
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $schoolofstudy = [
            'School of Computing',
            'School of Business',
            'School of Law',
            'School of Foundation',
        ];

        foreach ($schoolofstudy as $school) {
            DB::table('school_of_studies')->insert([
                'school_name' => $school,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $levels = [
            'Level 3',
            'Level 4',
            'Level 5',
            'Level 6',
        ];

        foreach ($levels as $level) {
            DB::table('levels')->insert([
                'level_name' => $level,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $degreePrograms = [
            'School of Foundation' => [
                'Business Foundation',
                'Computing Foundation',
                'Law Foundation',
                'NCUK International Foundation Year',
            ],

            'School of Business' => [
                'BSc (Hons) Business Management',
                'BA (Hons) Business Innovation and Entrepreneurship',
                'BSc (Hons) International Business Management',
                'BA (Hons) Digital and Social Media Marketing',
                'BSc (Hons) Accounting and Finance',
            ],

            'School of Computing' => [
                'BEng (Hons) Software Engineering',
                'BSc (Hons) Computer Science',
                'BSc (Hons) Cyber Security',
            ],

            'School of Law' => [
                'LLB (HONS) Law',
                'LLB (HONS) LAW (Part Time)',
            ],
        ];

        foreach ($degreePrograms as $school => $programs) {
            $schoolId = DB::table('school_of_studies')->where('school_name', $school)->first()->id;
            foreach ($programs as $program) {
                DB::table('degree_programs')->insert([
                    'school_id' => $schoolId,
                    'degree_name' => $program,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        };

        $semester = [
            'Semester 1',
            'Semester 2',
            'Break',
        ];

        foreach ($semester as $sem) {
            DB::table('semesters')->insert([
                'semester_name' => $sem,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        };


        $modules = [
              'Business Foundation' => [
                  'Semester 1' => [
                      'Level 3' => [
                          'English for Academic Purposes - 1',
                          'Self & Society - 1',
                          'Business Mathematics',
                          'Introduction to Business Environments',
                      ],
                  ],
                  'Semester 2' => [
                      'Level 3' => [
                          'English for Academic Purposes -1',
                          'Self & Society -1',
                          'Digital Literacy & Communication',
                          'Organisations & Behaviour',
                      ],
                  ],
              ],
            'Computing Foundation' => [
                'Semester 1' => [
                    'Level 3' => [
                        'English for Academic Purposes - 1',
                        'Self & Society - 1',
                        'Computing Mathematics',
                        'Introduction to Business Environment',
                    ],
                ],
                'Semester 2' => [
                    'Level 3' => [
                        'English for Academic Purposes - 2',
                        'Self & Society - 2',
                        'Digital Literacy & Communication',
                        'Programming $ Web Development',
                    ],
                ],
            ],
            'Law Foundation' => [
                'Semester 1' => [
                    'Level 3' => [
                        'English for Academic Purposes - 1',
                        'Self & Society - 1',
                        'Legal Systems & History',
                        'Legal Methods',
                    ],
                ],
                'Semester 2' => [
                    'Level 3' => [
                        'English for Academic Purposes - 2',
                        'Self & Society - 2',
                        'Digital Literacy & Communication',
                        'Legal Skills & Critical Thinking',
                    ],
                ],
            ],
            'NCUK International Foundation Year' => [
                'Semester 1' => [
                    'Level 3' => [
                        'Business Economics',
                        'Business Skills',
                        'Organizational Behaviour',
                        'Financial Accounting',
                    ],
                ],
                'Semester 2' => [
                    'Level 3' => [
                        'Management Themes and Case Studies',
                        'Marketing or International Business',
                        'Management Accounting',
                        'Quantitative Methods for Business',
                    ],
                ],
            ],
            'BEng (Hons) Software Engineering' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Software Development and Application Modelling - 1',
                        'Digital Technologies - 1',
                        'Networking Concepts and Cyber Security - 1',
                        'Web Development and Operating Systems - 1',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers - 1',
                        'Databases and Data Structures - 1',
                        'Server-Side Programming - 1',
                        'Mobile App Development - 1 ',
                    ],
                    'Level 6' => [
                        'Emerging Technologies - 1',
                        'Clean Coding and Networks - 1',
                        'Enterprise Cloud and Distributed Web Applications - 1',
                        'Final Year Project',
                    ]
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Software Development and Application Modelling - 2',
                        'Digital Technologies - 2',
                        'Networking Concepts and Cyber Security - 2',
                        'Web Development and Operating Systems - 2',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers - 2',
                        'Databases and Data Structures - 2',
                        'Server-Side Programming - 2',
                        'Mobile App Development - 2 ',
                    ],
                    'Level 6' => [
                        'Emerging Technologies - 2',
                        'Clean Coding and Networks - 2',
                        'Enterprise Cloud and Distributed Web Applications - 2',
                        'Final Year Project',
                    ]
                ],
            ],
            'BSc (Hons) Computer Science' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Software Development and Application Modelling - 1',
                        'Digital Technologies - 1',
                        'Networking Concepts and Cyber Security - 1',
                        'Web Development and Operating Systems - 1',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers - 1',
                        'Databases and Data Structures - 1',
                        'Server-Side Programming - 1',
                        'Mobile App Development - 1 ',
                        'Cyber Operations and Network Security - 1',
                        'Enterprise Cloud and Infrastructure Automation - 1',
                        'Routed and Switched Architectures - 1',
                        'Web Development - 1'
                    ],
                    'Level 6' => [
                        'Emerging Technologies - 1',
                        'Clean Coding and Networks - 1',
                        'Decision Analytics - 1',
                        'Multiple Devices and User Experience - 1',
                        'Web and Artificial Intelligence - 1',
                        'Advanced Networks and Operating Systems Security - 1',
                        'Cloud Virtualization and Communications - 1',
                        'Enterprise Cloud and Distributed Web Applications - 1',
                        'Final Year Project',
                    ]
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Software Development and Application Modelling - 2',
                        'Digital Technologies - 2',
                        'Networking Concepts and Cyber Security - 2',
                        'Web Development and Operating Systems - 2',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers - 2',
                        'Databases and Data Structures - 2',
                        'Server-Side Programming - 2',
                        'Mobile App Development - 2 ',
                        'Cyber Operations and Network Security - 2',
                        'Enterprise Cloud and Infrastructure Automation - 2',
                        'Routed and Switched Architectures - 2',
                        'Web Development - 2'
                    ],
                    'Level 6' => [
                        'Emerging Technologies - 2',
                        'Clean Coding and Networks - 2',
                        'Decision Analytics - 2',
                        'Multiple Devices and User Experience - 2',
                        'Web and Artificial Intelligence - 2',
                        'Advanced Networks and Operating Systems Security - 2',
                        'Cloud Virtualization and Communications - 2',
                        'Enterprise Cloud and Distributed Web Applications - 2',
                        'Final Year Project',
                    ]
                ],
            ],
            'BSc (Hons) Cyber Security' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Software Development and Application Modelling - 1',
                        'Digital Technologies - 1',
                        'Networking Concepts and Cyber Security - 1',
                        'Web Development and Operating Systems - 1',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers - 1',
                        'Cyber Operations and Network Security - 1',
                        'Ethical Hacking - 1',
                        'Cyber Security - 1',
                    ],
                    'Level 6' => [
                        'IT Infrastructure Security - 1',
                        'Advanced Topics in Cyber Security - 1',
                        'Operating Systems Internals and Biometrics - 1',
                        'Final Year Project',
                    ]
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Software Development and Application Modelling - 2',
                        'Digital Technologies - 2',
                        'Networking Concepts and Cyber Security - 2',
                        'Web Development and Operating Systems - 2',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers - 2',
                        'Cyber Operations and Network Security - 2',
                        'Ethical Hacking - 2',
                        'Cyber Security - 2',
                    ],
                    'Level 6' => [
                        'IT Infrastructure Security - 2',
                        'Advanced Topics in Cyber Security - 2',
                        'Operating Systems Internals and Biometrics - 2',
                        'Final Year Project',
                    ]
                ],
            ],
            'LLB (HONS) Law' => [
                'Semester 1' => [
                    'Level 4' =>[
                        'Legal Skills',
                        'English Legal System',
                        'TORT Law',
                        'Legal English'
                    ],
                    'Level 5' =>[
                        'Criminal Law',
                        'Community Service',
                        'Criminal Law',
                    ],
                    'Level 6' =>[
                        'Contemporary Legal Issues',
                        'Human Rights Law',
                        'Dissertation',
                        'Company and Commercial Law',
                        'Cyber Law',
                    ],
                ],
                'Semester 2' =>[
                    'Level 4' =>[
                        'Constitutional Law',
                        'Contract Law',
                        'Law in Practice',
                        'E-Commerce',
                    ],
                    'Level 5' => [
                        'Business Law and Commercial Awareness',
                        'Introduction to Law Evidence',
                        'Property Law and Application',
                        'European Union Law',
                        'Law of Trust and Equitable Remedies',
                        'Internation Human Rights Law',
                        'Intellectual Property Law',
                    ],
                    'Level 6' =>[
                        'Private International Law',
                        'Artificial Intelligence Law',
                        'Family Law',
                        'Alternative Dispute Resolution',
                        'Mooting',
                        'Jurisprudence',
                        'Dissertation',
                        'Sri Lanka Labour Law',
                        'Sri Lanka Company Law',
                    ],
                ],
            ],
            'LLB (HONS) LAW (Part Time)' => [
                'Semester 1' => [
                    'Level 4' =>[
                        'Legal Skills',
                        'English Legal System',
                    ],
                    'Level 5' => [
                        'TORT Law',
                        'Contract Law',
                    ],
                    'Level 6' =>[
                        'Administrative Law',
                        'Criminal Law',
                        'European Union Law',
                    ],
                ],
                'Semester 2' =>[
                    'Level 4' =>[
                        'Constitutional Law',
                        'Law in Practice '
                    ],
                    'Level 5' =>[
                        'Property Law & Application',
                        'Business Law & Commercial Awareness',
                        'Work Experience',
                    ],
                    'Level 6' =>[
                        'Introduction to Law Evidence',
                        'Law of Trust & Equitable Remedies',
                        'Dissertation',
                        'Alternative Dispute Resolution',
                        'Company & Commercial Law',
                        'Family Law',
                        'Sri Lanka Labour Law',
                        'Intellectual Property Law',
                    ],
                ],
            ],
            'BSc (Hons) Business Management' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Foundations of Management',
                        'Introduction to Management Accounting',
                        'Marketing in the Business Environment',
                    ],
                    'Level 5' => [
                        'Operations Management',
                        'Management Accounting',
                        'Managing Equality, Diversity and Inclusion',
                        'Sustainable Business Development',
                        'An Entrepreneurial Mindset',
                    ],
                    'Level 6' => [
                        'Authentic Leadership',
                        'Data and Decision-Making',
                        'Consultancy Project',
                    ],
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Managerial Economics',
                        'Business Law',
                        'Foundations of Human Resource Management',
                    ],
                    'Level 5' => [
                        'Enterprise in Practice',
                        'Employee Voice and Representation',
                        'Managing and Developing People',
                        'The Business of Doing Good',
                        'Governance and Climate Change',
                    ],
                    'Level 6' => [
                        'Innovative Change Management',
                        'Strategic Management in a Global Context',
                        'Consultancy Project',
                    ],
                ],
            ],

            'BA (Hons) Business Innovation and Entrepreneurship' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Foundations of Management',
                        'Introduction to Management Accounting',
                        'Marketing in the Business Environment',
                    ],
                    'Level 5' => [
                        'Operations Management',
                        'Management Accounting',
                        'Managing Equality, Diversity and Inclusion',
                    ],
                    'Level 6' => [
                        'Authentic Leadership',
                        'Data and Decision-Making',
                        'Consultancy Project',
                    ],
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Managerial Economics',
                        'Business Law',
                        'Foundations of Human Resource Management',
                    ],
                    'Level 5' => [
                        'Sustainable Business Development',
                        'An Entrepreneurial Mindset',
                        'Enterprise in Practice',
                    ],
                    'Level 6' => [
                        'Innovative Change Management',
                        'Entrepreneurial Strategy',
                        'Consultancy Project',
                    ],
                ],
            ],

            'BSc (Hons) International Business Management' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Foundations of Management',
                        'Introduction to Management Accounting',
                        'Marketing in the Business Environment',
                    ],
                    'Level 5' => [
                        'Business Creativity and Innovation',
                        'Global Supply Chain and Logistics',
                        'Information Systems in Organizations',
                    ],
                    'Level 6' => [
                        'International Business Strategy',
                        'Global Marketing Management',
                        'Consultancy Project',
                    ],
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Managerial Economics',
                        'Business Law',
                        'Foundations of Human Resource Management',
                    ],
                    'Level 5' => [
                        'Managing Across Cultures',
                        'International Trade and Finance',
                        'Global Human Resource Management',
                    ],
                    'Level 6' => [
                        'Corporate Reputation and Ethics',
                        'Innovative Change Management',
                        'Consultancy Project',
                    ],
                ],
            ],

            'BA (Hons) Digital and Social Media Marketing' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Marketing in the Business Environment',
                        'Advertising and Marketing Communications',
                        'Digital Content Creation',
                    ],
                    'Level 5' => [
                        'Digital Marketing Strategy',
                        'Data Analysis and Visualization',
                        'Managing Global Digital Brand Responsibility',
                        'Consumer and Organizational Behavior',
                    ],
                    'Level 6' => [
                        'Corporate Reputation and Ethics',
                        'Marketing Research',
                    ],
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Social Media Strategy',
                        'Digital Marketing Techniques',
                        'Monitoring and Measuring in Digital Environments',
                    ],
                    'Level 5' => [
                        'Digital Customer Experience',
                        'Operations Management',
                        'Sustainable Business Development',
                    ],
                    'Level 6' => [
                        'Strategic Marketing Management',
                        'Marketing Research',
                    ],
                ],
            ],

            'BSc (Hons) Accounting and Finance' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Introduction to Management Accounting',
                        'Introduction to Financial Accounting',
                        'The Professional Accountant Toolkit',
                    ],
                    'Level 5' => [
                        'Financial Reporting',
                        'Management Accounting',
                        'Fintech and Digitisation',
                    ],
                    'Level 6' => [
                        'Advanced Financial Reporting',
                        'Corporate Finance',
                        'Term Project',
                    ],
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Financial Management',
                        'Personal Taxation',
                        'Economics and Law for Managers',
                    ],
                    'Level 5' => [
                        'Advanced Management Accounting',
                        'Business Taxation',
                        'Business Research Methods',
                    ],
                    'Level 6' => [
                        'Corporate Governance',
                        'Auditing and Assurance',
                        'Term Project',
                    ],
                ],
            ],


        ];

        foreach ($modules as $degree => $semesters) {
            $degreeId = DB::table('degree_programs')->where('degree_name', $degree)->first()->id;
            foreach ($semesters as $semester => $levels) {
                $semesterId = DB::table('semesters')->where('semester_name', $semester)->first()->id;
                foreach ($levels as $level => $modules) {
                    $levelId = DB::table('levels')->where('level_name', $level)->first()->id;
                    foreach ($modules as $module) {
                        DB::table('modules')->insert([
                            'degree_program_id' => $degreeId,
                            'semester_id' => $semesterId,
                            'level_id' => $levelId,
                            'module_name' => $module,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        };

        $tutors = [
            [
                'name' => 'John Doe',
                'email' => 'cb012345@students.apiit.lk',
                'password' => bcrypt('password'), // Default password
                'cb_number' => 'CB012345',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg', // Replace with actual file if necessary
                'status' => 'approved',
                'approved_modules' => [33, 34, 35, 45, 46, 39, 40],
                'rejected_modules' => [36, 47, 48, 37, 38],
                'reject_reason' => 'Well done',
                'degree' => 10,
                'school_of_study' => 1,
                'level' => 3,
                'semester' => 2,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'cb054321@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB054321',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [57, 59, 61, 63, 80, 81],
                'rejected_modules' => [58, 60, 62, 64, 78, 79],
                'reject_reason' => 'Well done',
                'degree' => 11,
                'school_of_study' => 1,
                'level' => 3,
                'semester' => 2,
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'cb098765@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB098765',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [57, 59, 61, 63, 80, 81],
                'rejected_modules' => [58, 60, 62, 64, 78, 79],
                'reject_reason' => 'Well done',
                'degree' => 11,
                'school_of_study' => 1,
                'level' => 3,
                'semester' => 2,
            ],
            [
                'name' => 'Mark Brown',
                'email' => 'cb076543@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB076543',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [33, 34, 35, 45, 46, 39, 40],
                'rejected_modules' => [36, 47, 48, 37, 38],
                'reject_reason' => 'Well done',
                'degree' => 10,
                'school_of_study' => 1,
                'level' => 3,
                'semester' => 2,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'cb064321@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB064321',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [101, 102, 112, 113, 103, 104, 105],
                'rejected_modules' => [103, 104, 111, 114, 106],
                'reject_reason' => 'Well done',
                'degree' => 12,
                'school_of_study' => 1,
                'level' => 3,
                'semester' => 2,
            ],
            [
                'name' => 'Ben Doe',
                'email' => 'cb064325@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB064325',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [101, 102, 112, 113, 103, 104, 105],
                'rejected_modules' => [103, 104, 111, 114, 106],
                'reject_reason' => 'Well done',
                'degree' => 12,
                'school_of_study' => 1,
                'level' => 3,
                'semester' => 2,
            ]
        ];

        foreach ($tutors as $tutor) {
            // Insert user into users table
            $userId = DB::table('users')->insertGetId([
                'name' => $tutor['name'],
                'email' => $tutor['email'],
                'email_verified_at' => now(),
                'password' => $tutor['password'],
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        
            // Insert tutor profile into profiles table
            DB::table('profiles')->insert([
                'user_id' => $userId,
                'cb_number' => $tutor['cb_number'],
                'profile_pic' => $tutor['profile_pic'],
                'degree_id' => $tutor['degree'],
                'school_id' => $tutor['school_of_study'],
                'level_id' => $tutor['level'],
                'semester_id' => $tutor['semester'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        
            // Insert tutor-specific data into tutors table
            $tutorId = DB::table('tutors')->insertGetId([
                'user_id' => $userId,
                'status' => $tutor['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        
            // Insert approved modules
            foreach ($tutor['approved_modules'] as $moduleId) {
                DB::table('tutor_modules_approved')->insert([
                    'tutor_id' => $tutorId,
                    'module_id' => $moduleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        
            // Insert rejected modules with reasons
            foreach ($tutor['rejected_modules'] as $moduleId) {
                DB::table('tutor_modules_rejected')->insert([
                    'tutor_id' => $tutorId,
                    'module_id' => $moduleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Insert rejected modules with reasons
            DB::table('reject_messages')->insert([
                'tutor_id' => $tutorId,
                'message' => $tutor['reject_reason'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
                    
            // Insert default availability (customize as needed)
            $days = ['Monday', 'Wednesday', 'Friday'];
            foreach ($days as $day) {
                DB::table('available_times')->insert([
                    'tutor_id' => $tutorId,
                    'day' => $day,
                    'start_time' => '10:00:00',
                    'end_time' => '12:00:00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Insert approved modules into tutor_selected_modules table
            foreach ($tutor['approved_modules'] as $moduleId) {
                DB::table('tutor_selected_modules')->insert([
                    'tutor_id' => $tutorId,
                    'module_id' => $moduleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
