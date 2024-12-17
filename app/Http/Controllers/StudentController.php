<?php

namespace App\Http\Controllers;

use App\Models\DegreeProgram;
use App\Models\Module;
use App\Models\Profile;
use App\Models\SchoolOfStudy;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function dashboard()
    {
        $userid = auth()->user()->id;
        $profile = Profile::where('user_id', $userid)->first();

        $semesterModules = Module::where('semester_id', $profile->semester_id)->where('degree_program_id', $profile->degree_id)->where('level_id', $profile->level_id)->get();

        //get the semesterModules id
        $semesterModulesId = [];
        foreach ($semesterModules as $module) {
            $semesterModulesId[] = $module->id;
        }

        //get the tutors who have selected the modules in the semester
        $tutors = Tutor::whereHas('selectedModules', function ($query) use ($semesterModulesId) {
            $query->whereIn('module_id', $semesterModulesId);
        })->where('user_id', '!=', $userid) // Exclude current user
        ->get();

        //get tutors profile
        $tutorIds = [];
        foreach ($tutors as $tutor) {
            $tutorIds[] = $tutor->user_id;
        }

        //send the tutors with the user details and profile details
        $allDegree = DegreeProgram::where('school_id', $profile->school_id)->get();
        $tutordetails = [];
        foreach ($tutors as $tutor) {
            $tutordetails[] = [
                'user' => $tutor->user,
                'profile' => Profile::where('user_id', $tutor->user_id)->first(),
                'modules' => $tutor->selectedModules,
            ];
        }

        //get all modules of the school
        $degrees = DegreeProgram::where('school_id', $profile->school_id)->get();
        $degreeModules = [];
        foreach ($degrees as $degree) {
            $degreeModules[] = Module::where('degree_program_id', $degree->id)->get();
        }
        // get the tutor who have selected the modules
        $degreeModulesId = [];
        foreach ($degreeModules as $module) {
            foreach ($module as $mod) {
                $degreeModulesId[] = $mod->id;
            }
        }

        $degreetutors = Tutor::whereHas('selectedModules', function ($query) use ($degreeModulesId) {
            $query->whereIn('module_id', $degreeModulesId);
        })->whereNotIn('user_id', $tutorIds) // Exclude tutors from the first set
        ->where('user_id', '!=', $userid) // Exclude current user
        ->get();

        $degreetutorIds = [];
        foreach ($degreetutors as $tutor) {
            $degreetutorIds[] = $tutor->user_id;
        }

        $degreetutordetails = [];
        foreach ($degreetutorIds as $tutor) {
            $degreetutordetail = Tutor::where('user_id', $tutor)->first();
            $degreetutordetails[] = [
                'user' => $degreetutordetail->user,
                'profile' => Profile::where('user_id', $degreetutordetail->user_id)->first(),
                'modules' => $degreetutordetail->selectedModules,
            ];
        }

        return Inertia::render('Dashboard',[
            'semstertutors' => $tutordetails,
            'allDegree' => $allDegree,
            'tutors' => $degreetutordetails,
        ]);
    }
}
