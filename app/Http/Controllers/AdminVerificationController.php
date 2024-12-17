<?php

namespace App\Http\Controllers;

use App\Mail\ApproveTutorMail;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\DegreeProgram;
use App\Models\Level;
use App\Models\Module;
use App\Models\Profile;
use App\Models\SchoolOfStudy;
use App\Models\Semester;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use function Pest\Laravel\json;

class AdminVerificationController extends Controller
{
    public function index()
    {
        Tutor::create(
            [
                'user_id' => auth()->id(),
                'status' => 'pending'
            ]
        );

        $tutor = Tutor::where('user_id', auth()->id())->first();

        Mail::to('admin@example.com')->send(new ApproveTutorMail($tutor));

        return Inertia::render(route('profile.show'));
    }

    public function adminDashboard()
    {
        $tutors = Tutor::all();
        $user = User::all();
        $profile = Profile::all();
        $school = SchoolOfStudy::all();
        $degree = DegreeProgram::all();
        $level = Level::all();
        $semester = Semester::all();


        return Inertia::render('Admin/Dashboard', [
            'tutors' => $tutors,
            'users' => $user,
            'profiles' => $profile,
            'schools' => $school,
            'degrees' => $degree,
            'levels' => $level,
            'semesters' => $semester
        ]
        );
    }

    // public function approveTutor(Request $request)
    // {
    //     $tutor_id = $request->tutor_id;
    //     $subjectId = $request->subject_ids;

    //     // $tutor = Tutor::find($tutor_id);
    //     $tutor = Tutor::find($request['tutor_id']);
    //     $tutor->status = 'approved';
    //     $tutor->save();

    //     $tutor->approvedModules()->attach($subjectId);

    //     $tutor->rejectMessage()->create([
    //         'message' => $request['reason']
    //     ]);

    //     return Inertia::render('Admin/Dashboard',
    //     [
    //         'showModal' => false,
    //     ]);
    // }

    public function processTutor(Request $request)
    {
        $tutor = Tutor::find($request->tutor_id);

        if (!$tutor) {
            return redirect()->back()->with('error', 'Tutor not found.');
        }

        // Process approved modules
        if (!empty($request->approved_module_ids)) {
            $tutor->approvedModules()->syncWithoutDetaching($request->approved_module_ids);

            // Save rejection message if provided
            if ($request->rejection_reason) {
                $tutor->rejectMessage()->create([
                    'message' => $request->rejection_reason,
                ]);
            }

            $tutor->status = 'approved';
        }

        // Process rejected modules
        if (!empty($request->unapproved_module_ids)) {
            $tutor->rejectedModules()->attach($request->unapproved_module_ids);

            // Save rejection message if provided
            if ($request->rejection_reason) {
                $tutor->rejectMessage()->create([
                    'message' => $request->rejection_reason,
                ]);
            }
        }

        // If only rejections exist and no approvals, set status to 'approved'
        if (!empty($request->unapproved_module_ids) && empty($request->approved_module_ids)) {
            $tutor->status = 'rejected';
        }

        // Save tutor status
        $tutor->save();

        return redirect()->back()->with('success', 'Modules processed successfully.');
    }

    // public function rejectTutor(Request $request)
    // {
    //     $tutor = Tutor::find($request['tutor_id']);

    //     $tutor->status = 'rejected';
    //     $tutor->save();
    //     $tutor->rejectedModules()->attach($request['module_ids']);

    //     $tutor->rejectMessage()->create([
    //         'message' => $request['reason']
    //     ]);

    //     return redirect()->back()->with('error' , 404);
    // }

    public function deleteTutor($id)
    {
        $tutor = Tutor::find($id);
        $tutor->approvedModules()->detach();
        $tutor->rejectedModules()->detach();
        $tutor->rejectMessage()->delete();
        $tutor->delete();

        return redirect()->route('admin.dashboard');
    }

    public function getData($id)
    {
        $tutor = Tutor::find($id);
        $tutorprofile = Profile::where('user_id', $tutor->user_id)->first();

        $levelid = $tutorprofile->level_id;
        $degreeid = $tutorprofile->degree_id;
        $semesterid = $tutorprofile->semester_id;

        $modules = [];

        for ($i = 1; $i <= $levelid - 1; $i++) {
            $levelname = Level::where('id', $i)->first()->level_name;
            $subjects = [];
            for ($j = 1; $j <= 2; $j++) {
                $module = Module::where('level_id', $i)
                    ->where('degree_program_id', $degreeid)
                    ->where('semester_id', $j)
                    ->get();

                $semestername = Semester::where('id', $j)->first()->semester_name;
                $sub = []; // Initialize as an empty array

                foreach ($module as $mod) {
                    $sub[] = $mod; // Add module names
                }

                if (!empty($sub)) { // Only include non-empty semester data
                    $subjects[] = [$semestername => $sub];
                }
            }
            if (!empty($subjects)) { // Only include non-empty level data
                $modules[] = [$levelname => $subjects];
            }
        }
        for ($i =1; $i < $semesterid; $i++) {
            $levelname = Level::where('id', $levelid)->first()->level_name;
            $subjects = [];

                $module = Module::where('level_id', $levelid)
                    ->where('degree_program_id', $degreeid)
                    ->where('semester_id', $i)
                    ->get();

                $semestername = Semester::where('id', $i)->first()->semester_name;
                $sub = []; // Initialize as an empty array

                foreach ($module as $mod) {
                    $sub[] = $mod; // Add module names
                }

                if (!empty($sub)) { // Only include non-empty semester data
                    $subjects[] = [$semestername => $sub];
                }

            if (!empty($subjects)) { // Only include non-empty level data
                $modules[] = [$levelname => $subjects];
            }
        }

        return response()->json($modules);

    }

    public function tutordata($id)
    {
        $tutor = Tutor::find($id);

        $approvedModules = $tutor->approvedModules()->get();
        $rejectedModules = $tutor->rejectedModules()->get();
        $rejectedreason = $tutor->rejectMessage()->get();



        return response()->json([
            'approvedModules' => $approvedModules,
            'rejectedModules' => $rejectedModules,
            'rejectedreason' => $rejectedreason
        ]);
    }

    // public function rejectTutorFully($id)
    // {
    //     $tutor = Tutor::find($id);
    //     $tutor->status = 'rejected';
    //     $tutor->save();

    //     return redirect()->route('admin.dashboard');

    // }

}
