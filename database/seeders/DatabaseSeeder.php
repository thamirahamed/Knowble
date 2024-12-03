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
                          'English for Academic Purposes',
                          'Self & Society',
                          'Business Mathematics',
                          'Introduction to Business Environments',
                      ],
                  ],
                  'Semester 2' => [
                      'Level 3' => [
                          'English for Academic Purposes',
                          'Self & Society',
                          'Digital Literacy & Communication',
                          'Organisations & Behaviour',
                      ],
                  ],
              ],
            'Computing Foundation' => [
                'Semester 1' => [
                    'Level 3' => [
                        'English for Academic Purposes',
                        'Self & Society',
                        'Computing Mathematics',
                        'Introduction to Business Environment',
                    ],
                ],
                'Semester 2' => [
                    'Level 3' => [
                        'English for Academic Purposes',
                        'Self & Society',
                        'Digital Literacy & Communication',
                        'Programming $ Web Development',
                    ],
                ],
            ],
            'Law Foundation' => [
                'Semester 1' => [
                    'Level 3' => [
                        'English for Academic Purposes',
                        'Self & Society',
                        'Legal Systems & History',
                        'Legal Methods',
                    ],
                ],
                'Semester 2' => [
                    'Level 3' => [
                        'English for Academic Purposes',
                        'Self & Society',
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
                        'Software Development and Application Modelling',
                        'Digital Technologies',
                        'Networking Concepts and Cyber Security',
                        'Web Development and Operating Systems',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers',
                        'Databases and Data Structures',
                        'Server-Side Programming',
                        'Mobile App Development ',
                    ],
                    'Level 6' => [
                        'Emerging Technologies',
                        'Clean Coding and Networks',
                        'Enterprise Cloud and Distributed Web Applications',
                        'Final Year Project',
                    ]
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Software Development and Application Modelling',
                        'Digital Technologies',
                        'Networking Concepts and Cyber Security',
                        'Web Development and Operating Systems',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers',
                        'Databases and Data Structures',
                        'Server-Side Programming',
                        'Mobile App Development ',
                    ],
                    'Level 6' => [
                        'Emerging Technologies',
                        'Clean Coding and Networks',
                        'Enterprise Cloud and Distributed Web Applications',
                        'Final Year Project',
                    ]
                ],
            ],
            'BSc (Hons) Computer Science' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Software Development and Application Modelling',
                        'Digital Technologies',
                        'Networking Concepts and Cyber Security',
                        'Web Development and Operating Systems',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers',
                        'Databases and Data Structures',
                        'Server-Side Programming',
                        'Mobile App Development ',
                        'Cyber Operations and Network Security',
                        'Enterprise Cloud and Infrastructure Automation',
                        'Routed and Switched Architectures',
                        'Web Development'
                    ],
                    'Level 6' => [
                        'Emerging Technologies',
                        'Clean Coding and Networks',
                        'Decision Analytics',
                        'Multiple Devices and User Experience',
                        'Web and Artificial Intelligence',
                        'Advanced Networks and Operating Systems Security',
                        'Cloud Virtualization and Communications',
                        'Enterprise Cloud and Distributed Web Applications',
                        'Final Year Project',
                    ]
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Software Development and Application Modelling',
                        'Digital Technologies',
                        'Networking Concepts and Cyber Security',
                        'Web Development and Operating Systems',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers',
                        'Databases and Data Structures',
                        'Server-Side Programming',
                        'Mobile App Development ',
                        'Cyber Operations and Network Security',
                        'Enterprise Cloud and Infrastructure Automation',
                        'Routed and Switched Architectures',
                        'Web Development'
                    ],
                    'Level 6' => [
                        'Emerging Technologies',
                        'Clean Coding and Networks',
                        'Decision Analytics',
                        'Multiple Devices and User Experience',
                        'Web and Artificial Intelligence',
                        'Advanced Networks and Operating Systems Security',
                        'Cloud Virtualization and Communications',
                        'Enterprise Cloud and Distributed Web Applications',
                        'Final Year Project',
                    ]
                ],
            ],
            'BSc (Hons) Cyber Security' => [
                'Semester 1' => [
                    'Level 4' => [
                        'Software Development and Application Modelling',
                        'Digital Technologies',
                        'Networking Concepts and Cyber Security',
                        'Web Development and Operating Systems',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers',
                        'Cyber Operations and Network Security',
                        'Ethical Hacking',
                        'Cyber Security',
                    ],
                    'Level 6' => [
                        'IT Infrastructure Security',
                        'Advanced Topics in Cyber Security',
                        'Operating Systems Internals and Biometrics',
                        'Final Year Project',
                    ]
                ],
                'Semester 2' => [
                    'Level 4' => [
                        'Software Development and Application Modelling',
                        'Digital Technologies',
                        'Networking Concepts and Cyber Security',
                        'Web Development and Operating Systems',
                    ],
                    'Level 5' => [
                        'Commercial Computing for Software Engineers',
                        'Cyber Operations and Network Security',
                        'Ethical Hacking',
                        'Cyber Security',
                    ],
                    'Level 6' => [
                        'IT Infrastructure Security',
                        'Advanced Topics in Cyber Security',
                        'Operating Systems Internals and Biometrics',
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
        }
    }
}
