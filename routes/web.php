<?php

use App\Http\Controllers\AdminVerificationController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\VideoRoomController;
use App\Models\Tutor;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

 Route::get('/', function () {
     return Inertia::render('Welcome', [
         'canLogin' => Route::has('login'),
         'canRegister' => Route::has('register'),
         'laravelVersion' => Application::VERSION,
         'phpVersion' => PHP_VERSION,
     ]
);
 });

// Landing page
Route::get('/', function () {
    return redirect()->route('login', [
        'canRegister' => Route::has('register')
    ]);
});

// Route::middleware('auth')->group(function () {
//
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Ensure only authenticated users can access the profile routes
Route::middleware(['auth','studentportal'])->group(function () {
    // Route to view the profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // Route to create a new profile (for users who haven't filled out their profile)
    Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');

    // Route to store the new profile data (when user submits the form to create their profile)
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    // Route to update existing profile information
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Route to delete the user profile (optional based on your needs)
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Get the picture from the storage
    Route::get('/private-profile-picture/{filename}', [ProfileController::class, 'getimage'])->name('private.profile.picture');
});

Route::middleware(['auth','adminportal'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminVerificationController::class, 'adminDashboard'])->name('admin.dashboard');

    });
});


Route::middleware(['auth'])->group(function (){
    // Approve/reject tutor
    Route::post('/process-tutor', [AdminVerificationController::class, 'processTutor'])->name('process.tutor');
    // Route::post('/approve-tutor', [AdminVerificationController::class, 'approveTutor'])->name('approve.tutor');
    // Route::post('/reject-tutor', [AdminVerificationController::class, 'rejectTutor'])->name('reject.tutor');
    // Delete tutor
    Route::delete('/tutors/{id}', [AdminVerificationController::class, 'deleteTutor'])->name('delete.tutor');
    Route::get('admin/data/{id}',[AdminVerificationController::class, 'getData'])->name('tutor.data');
    Route::post('/reject-tutor/{id}', [AdminVerificationController::class, 'rejectTutorFully'])->name('reject.tutor.full');
    Route::get('/admin/tutordata/{id}', [AdminVerificationController::class, 'tutordata'])->name('get.tutor.data');
});
//chat
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chatpage');
    Route::get('/api/chat/users', [ChatController::class, 'getUsers']);
    Route::post('/api/chat/start', [ChatController::class, 'startChat']);
    Route::post('/api/chat/message', [ChatController::class, 'sendMessage']);

    Route::get('/api/chat/messages/{chatId}', [ChatController::class, 'getMessages']);

    Route::prefix('tutor')->group(function () {
        Route::get('/dashboard',[TutorController::class, 'index'])->name('tutor.dashboard');
        Route::post('select/{id}',[TutorController::class, 'selectModule'])->name('tutor.select.module');
        Route::post('remove/{id}',[TutorController::class, 'removeModule'])->name('tutor.remove.module');
        Route::post('/available-times', [TutorController::class, 'storeAvailableTimes'])->name('tutor.available.times');
    });

});
// Admin Verification
Route::middleware(['auth' ,'studentportal'])->group(function () {
    Route::post('/admin/request',[AdminVerificationController::class, 'index'])->name('admin.tutor.request');
    Route::post('/admin/request/{module_id}',[AdminVerificationController::class, 'singleModule'])->name('admin.tutor.single.request');
    Route::get('/dashboard',[StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/tutor/profile/{id}',[StudentController::class, 'tutorProfile'])->name('tutor.profile');
    Route::get('/tutor/session/request/{id}',[StudentController::class, 'requestSession'])->name('tutor.session.request');
    Route::get('/meetings', [MeetingController::class, 'index'])->name('meetings.index');
    Route::post('/meetings/create', [MeetingController::class, 'create'])->name('meetings.create');

    Route::post('/tutor/sessions/request', [StudentController::class, 'requestSession'])->name('tutor.session.request');
});


require __DIR__.'/auth.php';
