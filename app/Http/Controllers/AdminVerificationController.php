<?php

namespace App\Http\Controllers;

use App\Mail\ApproveTutorMail;
use App\Models\Chat;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\DegreeProgram;
use App\Models\Level;
use App\Models\Module;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use App\Models\Profile;
use App\Models\SchoolOfStudy;
use App\Models\Semester;
use App\Models\Tutor;
use App\Models\TutorSession;
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

    // Tutor Details Modal Data
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

    public function sessions()
    {
        //get all the users with profile data is aviailable
        $users = User::all();

        foreach ($users as $user) {
            $profile = Profile::where('user_id', $user->id)->first();
            if ($profile) {
                $user->profile = $profile;
            }
            //remove admin user from the list
            if ($user->name == 'Admin') {
                $users = $users->except($user->id);
            }

            //is a tutor
            $tutor = Tutor::where('user_id', $user->id)->exists();

            if ($tutor) {
                $user->tutor = 'Yes';
            } else {
                $user->tutor = 'No';
            }
        }

        return Inertia::render('Admin/Sessions', [
            'users' => $users
        ]);

    }

    public function userHistory($id)
    {
        $user = User::find($id);
        $profile = Profile::where('user_id', $id)->first();

        $isTutor = Tutor::where('user_id', $id)->exists();

        $tutordetails = [];
        if ($isTutor) {
            $tutor = Tutor::where('user_id', $id)->first();
            $tutorsession = TutorSession::where('tutor_id', $tutor->id)->get();
            $tuorselectedmodules = $tutor->selectedModules()->get();

            $tutordetails = [
                'tutor' => $tutor,
                'tutorsession' => $tutorsession,
                'tuorselectedmodules' => $tuorselectedmodules
            ];
        }

        $sessions = TutorSession::where('user_id', $id)->get();
        $peerGroups  = [];

        $peerGroup = PeerGroup::where('leader', $id)->get();

        foreach ($peerGroup as $group) {
            $group->course = Module::where('id', $group->course_id)->first();
            $group->courseLevel = Level::where('id', $group->course_level_id)->first();
            $group->leader = 'yes';
        }

        $peergroupmember = PeerGroupMember::where('user_id', $id)->get();

        foreach ($peergroupmember as $member) {
            $member->group = PeerGroup::where('id', $member->peer_group_id)->first();
        }

        $peerGroups = [
            'peerGroup' => $peerGroup,
            'peergroupmember' => $peergroupmember
        ];




        $chat = Chat::where('user_id_1', $id)->orWhere('user_id_2', $id)->get();

        if ($chat) {
            foreach ($chat as $ch) {
                //if its user only add the other user
                if ($ch->user_id_1 == $id) {
                    $ch->user = User::where('id', $ch->user_id_2)->first();
                } else {
                    $ch->user = User::where('id', $ch->user_id_1)->first();
                }
            }
        }

        $degree = DegreeProgram::where('id', $profile->degree_id)->first();
        $level = Level::where('id', $profile->level_id)->first();
        $semester = Semester::where('id', $profile->semester_id)->first();


        return Inertia::render('Admin/UserHistory', [
            'user' => $user,
            'profile' => $profile,
            'tutor' => $tutordetails,
            'sessions' => $sessions,
            'peerGroup' => $peerGroups,
            'chat' => $chat,
            'degree' => $degree,
            'level' => $level,
            'semester' => $semester,
            'isTutor' => $isTutor
        ]);
    }

}
