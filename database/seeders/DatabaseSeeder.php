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
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@apiit.lk',
            ],
            [
                'name' => 'Thamir Ahamed',
                'email' => 'cb012829@students.apiit.lk',
            ],
            [
                'name' => 'Nuski Ahamed Naleem',
                'email' => 'cb012282@students.apiit.lk',
            ],
            [
                'name' => 'Shakeel Ahamed Shajahan',
                'email' => 'cb009882@students.apiit.lk',
            ],
            [
                'name' => 'Ammar Mohamed Zubair',
                'email' => 'cb011246@students.apiit.lk',
            ],
        ];
        
        foreach ($users as $user) {
            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }        

        $schoolofstudy = [
            'School of Computing',
            'School of Business',
            'School of Law',
        ];

        foreach ($schoolofstudy as $school) {
            DB::table('school_of_studies')->insert([
                'school_name' => $school,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $levels = [
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
            'BEng (Hons) Software Engineering' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Software Development and Application Modelling - 1',
                        'Digital Technologies - 1',
                        'Networking Concepts and Cyber Security - 1',
                        'Web Development and Operating Systems - 1',
                    ],
                    'Level 5' => [
                        'Commercial Computing - 1',
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
                        'Commercial Computing - 2',
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
                        'Commercial Computing - 1',
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
                        'Commercial Computing - 2',
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
                        'Commercial Computing - 1',
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
                        'Commercial Computing - 2',
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

        $profiles = [
            [
                'cb_number' => 'CB012828',
                'degree_id' => 8,
                'school_id' => 1,
                'level_id' => 1,
                'semester_id' => 2,
            ],
            [
                'cb_number' => 'CB012282',
                'degree_id' => 6,
                'school_id' => 1,
                'level_id' => 1,
                'semester_id' => 2,
            ],
            [
                'cb_number' => 'CB009882',
                'degree_id' => 7,
                'school_id' => 1,
                'level_id' => 1,
                'semester_id' => 2,
            ],
            [
                'cb_number' => 'CB011246',
                'degree_id' => 8,
                'school_id' => 1,
                'level_id' => 1,
                'semester_id' => 2,
            ],
        ];
        
        $user_id = 2; // Start with user_id 2, as admin (user_id 1) is excluded.
        
        foreach ($profiles as $profile) {
            DB::table('profiles')->insert([
                'user_id' => $user_id++,
                'cb_number' => $profile['cb_number'],
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'degree_id' => $profile['degree_id'],
                'school_id' => $profile['school_id'],
                'level_id' => $profile['level_id'],
                'semester_id' => $profile['semester_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }        

        $tutors = [
            [
                'name' => 'John Doe',
                'email' => 'cb012345@students.apiit.lk',
                'password' => bcrypt('password'), // Default password
                'cb_number' => 'CB012345',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg', // Replace with actual file if necessary
                'status' => 'approved',
                'approved_modules' => [1, 2, 4, 13, 16, 5,],
                'rejected_modules' => [3, 6, 7, 14, 15, 8],
                'reject_reason' => 'Well done',
                'degree' => 6,
                'school_of_study' => 1,
                'level' => 2,
                'semester' => 2,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'cb054321@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB054321',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [3, 6, 7, 14, 15, 8],
                'rejected_modules' => [1, 2, 4, 13, 16, 5],
                'reject_reason' => 'Well done',
                'degree' => 6,
                'school_of_study' => 1,
                'level' => 2,
                'semester' => 2,
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'cb098765@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB098765',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [25, 28, 29, 30, 34, 36, 47, 49],
                'rejected_modules' => [26, 27, 31, 32, 33, 35, 46, 48],
                'reject_reason' => 'Well done',
                'degree' => 7,
                'school_of_study' => 1,
                'level' => 2,
                'semester' => 2,
            ],
            [
                'name' => 'Mark Brown',
                'email' => 'cb076543@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB076543',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [26, 27, 31, 32, 33, 35, 46, 48],
                'rejected_modules' => [25, 28, 29, 30, 34, 36, 47, 49],
                'reject_reason' => 'Well done',
                'degree' => 7,
                'school_of_study' => 1,
                'level' => 2,
                'semester' => 2,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'cb064321@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB064321',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [67, 70, 71, 73, 80, 81],
                'rejected_modules' => [68, 69, 72, 74, 79, 82],
                'reject_reason' => 'Well done',
                'degree' => 8,
                'school_of_study' => 1,
                'level' => 2,
                'semester' => 2,
            ],
            [
                'name' => 'Ben Doe',
                'email' => 'cb064325@students.apiit.lk',
                'password' => bcrypt('password'),
                'cb_number' => 'CB064325',
                'profile_pic' => 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg',
                'status' => 'approved',
                'approved_modules' => [68, 69, 72, 74, 79, 82],
                'rejected_modules' => [67, 70, 71, 73, 80, 81],
                'reject_reason' => 'Well done',
                'degree' => 8,
                'school_of_study' => 1,
                'level' => 2,
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

        $peerGroups = [
            [
                'name' => 'The Innovators',  // Group name with meaning
                'leader' => 3,  // Set leader user ID
                'module_id' => 79,  // Set module ID
                'total_members' => 3,  // Set total members
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'The Trailblazers',  // Group name with meaning
                'leader' => 3,  // Set leader user ID
                'module_id' => 81,  // Set module ID
                'total_members' => 4,  // Set total members
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'The Pioneers',  // Group name with meaning
                'leader' => 4,  // Set leader user ID
                'module_id' => 80,  // Set module ID
                'total_members' => 5,  // Set total members
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'The Visionaries',  // Group name with meaning
                'leader' => 4,  // Set leader user ID
                'module_id' => 82,  // Set module ID
                'total_members' => 3,  // Set total members
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'The Achievers',  // Group name with meaning
                'leader' => 5,  // Set leader user ID
                'module_id' => 79,  // Set module ID
                'total_members' => 4,  // Set total members
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'The Mavericks',  // Group name with meaning
                'leader' => 5,  // Set leader user ID
                'module_id' => 82,  // Set module ID
                'total_members' => 3,  // Set total members
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('peer_groups')->insert($peerGroups);

        $peerGroupMembers = [
            [
                'peer_group_id' => 1,  // Set the peer group ID (e.g., The Innovators)
                'user_id' => 4,        // Set the user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'peer_group_id' => 2,  // Set the peer group ID (e.g., The Innovators)
                'user_id' => 5,        // Set the user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'peer_group_id' => 3,  // Set the peer group ID (e.g., The Trailblazers)
                'user_id' => 3,        // Set the user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'peer_group_id' => 3,  // Set the peer group ID (e.g., The Trailblazers)
                'user_id' => 5,        // Set the user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'peer_group_id' => 5,  // Set the peer group ID (e.g., The Pioneers)
                'user_id' => 3,        // Set the user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'peer_group_id' => 6,  // Set the peer group ID (e.g., The Pioneers)
                'user_id' => 4,        // Set the user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('peer_group_members')->insert($peerGroupMembers);

        $tutorSessions = [
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2024-12-15',  // Example date, modify as needed
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'status' => 'booked',  // You can change the status as needed
                'user_id' => 2,  // Set user ID or null for peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 80,  // Set module ID
                'notes' => 'Test session notes',
                'meeting_url' => 'https://meetinglink.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2024-12-16',  // Example date, modify as needed
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'status' => 'booked',  // You can change the status as needed
                'user_id' => 2,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 81,  // Set module ID
                'notes' => 'Another test session.',
                'meeting_url' => 'https://anothermeetinglink.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2024-12-17',  // Example date, modify as needed
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'status' => 'cancelled',  // You can change the status as needed
                'user_id' => 3,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 46,  // Set module ID
                'notes' => 'Another test session.',
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 3,  // Set tutor ID
                'session_date' => '2024-12-17',  // Example date, modify as needed
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'status' => 'cancelled',  // You can change the status as needed
                'user_id' => 4,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 48,  // Set module ID
                'notes' => 'Another test session.',
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 3,  // Set tutor ID
                'session_date' => '2024-12-17',  // Example date, modify as needed
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'status' => 'cancelled',  // You can change the status as needed
                'user_id' => 3,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 80,  // Set module ID
                'notes' => 'Another test session.',
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2024-12-18',  // Example date, modify as needed
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'status' => 'completed',  // You can change the status as needed
                'user_id' => 3,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 81,  // Set module ID
                'notes' => 'Another test session.',
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2024-12-19',  // Example date, modify as needed
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'status' => 'completed',  // You can change the status as needed
                'user_id' => 4,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 80,  // Set module ID
                'notes' => 'Another test session.',
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2024-12-20',  // Example date, modify as needed
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'status' => 'completed',  // You can change the status as needed
                'user_id' => 4,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 81,  // Set module ID
                'notes' => 'Another test session.',
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2024-12-21',  // Example date, modify as needed
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'status' => 'completed',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => 3,  // Set this if applicable
                'module_id' => 80,  // Set module ID
                'notes' => 'Another test session.',
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2025-01-30',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2025-01-31',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 6,  // Set tutor ID
                'session_date' => '2025-01-30',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 6,  // Set tutor ID
                'session_date' => '2025-01-31',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 4,  // Set tutor ID
                'session_date' => '2025-01-30',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 4,  // Set tutor ID
                'session_date' => '2025-01-31',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 3,  // Set tutor ID
                'session_date' => '2025-01-30',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 3,  // Set tutor ID
                'session_date' => '2025-01-30',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'completed',  // You can change the status as needed
                'user_id' => 2,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 46,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 2,  // Set tutor ID
                'session_date' => '2025-01-30',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 2,  // Set tutor ID
                'session_date' => '2025-01-31',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 1,  // Set tutor ID
                'session_date' => '2025-01-30',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 1,  // Set tutor ID
                'session_date' => '2025-01-31',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 5,  // Set tutor ID
                'session_date' => '2025-02-01',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'pending',  // You can change the status as needed
                'user_id' => null,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => null,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 3,  // Set tutor ID
                'session_date' => '2025-01-10',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'booked',  // You can change the status as needed
                'user_id' => 4,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 46,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 3,  // Set tutor ID
                'session_date' => '2025-01-11',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'booked',  // You can change the status as needed
                'user_id' => 5,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 48,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 2,  // Set tutor ID
                'session_date' => '2025-01-10',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'booked',  // You can change the status as needed
                'user_id' => 5,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 14,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 2,  // Set tutor ID
                'session_date' => '2025-01-11',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'booked',  // You can change the status as needed
                'user_id' => 3,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 15,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 6,  // Set tutor ID
                'session_date' => '2025-01-10',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'booked',  // You can change the status as needed
                'user_id' => 4,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 79,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tutor_id' => 6,  // Set tutor ID
                'session_date' => '2025-01-11',  // Example date, modify as needed
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'status' => 'booked',  // You can change the status as needed
                'user_id' => 3,  // Set user ID or peer_group_id
                'peer_group_id' => null,  // Set this if applicable
                'module_id' => 82,  // Set module ID
                'notes' => null,
                'meeting_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more sessions as needed
        ];

        foreach ($tutorSessions as $session) {
            DB::table('tutor_sessions')->insert($session);
        }

        $feedbackRatings = [
            [
                'user_id' => 3,  // Set the user ID
                'tutor_id' => 5,  // Set the tutor ID
                'rating' => 5,  // Rating out of 5
                'feedback' => 'Great session! The tutor was very helpful and explained everything clearly.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,  // Set the user ID
                'tutor_id' => 5,  // Set the tutor ID
                'rating' => 3,  // Rating out of 5
                'feedback' => 'The session was not as helpful as I expected. I had difficulty understanding some of the concepts.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,  // Set the user ID
                'tutor_id' => 5,  // Set the tutor ID
                'rating' => 3,  // Rating out of 5
                'feedback' => 'Good session, but the tutor could improve on the pacing.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,  // Set the user ID
                'tutor_id' => 3,  // Set the tutor ID
                'rating' => 4,  // Rating out of 5
                'feedback' => 'Good session, Very helpful.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'tutor_id' => 3,
                'rating' => 5,
                'feedback' => 'Amazing session! I learned a lot, and the tutor was very engaging.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'tutor_id' => 2,
                'rating' => 4,
                'feedback' => 'The session was informative, but I wish it was a bit longer.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'tutor_id' => 2,
                'rating' => 3,
                'feedback' => 'The tutor was knowledgeable, but the examples could have been more relevant.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'tutor_id' => 6,
                'rating' => 5,
                'feedback' => 'Excellent tutor! The explanations were clear, and the examples were spot-on.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'tutor_id' => 6,
                'rating' => 2,
                'feedback' => 'The session didn’t meet my expectations. The tutor seemed unprepared.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('feedback_ratings')->insert($feedbackRatings);

    }
}
