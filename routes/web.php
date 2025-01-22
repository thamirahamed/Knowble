<?php

use App\Http\Controllers\AdminVerificationController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ResourceShareController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\PeerGroupController;
use App\Http\Controllers\FeedbackRatingController;
use App\Http\Controllers\VideoRoomController;
use App\Models\Tutor;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page
Route::get('/', function () {
    return redirect()->route('login', [
        'canRegister' => Route::has('register')
    ]);
});

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
    // Delete tutor
    Route::delete('/tutors/{id}', [AdminVerificationController::class, 'deleteTutor'])->name('delete.tutor');

    Route::get('admin/data/{id}',[AdminVerificationController::class, 'getData'])->name('tutor.data');

    Route::get('/admin/tutordata/{id}', [AdminVerificationController::class, 'tutordata'])->name('get.tutor.data');
});

//Chat and Tutor Portal
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chatpage');
    Route::get('/api/chat/users', [ChatController::class, 'getUsers']);
    Route::post('/api/chat/start', [ChatController::class, 'startChat']);
    Route::post('/api/chat/message', [ChatController::class, 'sendMessage']);

    Route::get('/api/chat/messages/{chatId}', [ChatController::class, 'getMessages']);

    // tutor dashboard overview
    Route::prefix('tutor')->group(function () {
        Route::get('/dashboard',[TutorController::class, 'index'])->name('tutor.dashboard');
        Route::post('select/{id}',[TutorController::class, 'selectModule'])->name('tutor.select.module');
        Route::post('remove/{id}',[TutorController::class, 'removeModule'])->name('tutor.remove.module');
        Route::post('/sessions/create', [TutorController::class, 'createSession'])->name('tutor.session.create');
        Route::post('/sessions/cancel', [TutorController::class, 'cancelRequest'])->name('tutor.session.cancel');
        Route::post('/sessions/delete/{id}', [TutorController::class, 'deleteSession'])->name('tutor.session.delete');
        Route::post('/resource-shares', [ResourceShareController::class, 'store'])->name('resource-shares.store');
        Route::delete('/resource-shares/{id}', [ResourceShareController::class, 'destroy'])->name('resource-shares.destroy');
        Route::get('/resource-shares/download/{id}', [ResourceShareController::class, 'download'])->name('resource-shares.download');
    });
});

// Student Portak
Route::middleware(['auth' ,'studentportal'])->group(function () {
    // Show all tutors in dashboard
    Route::get('/dashboard',[StudentController::class, 'dashboard'])->name('dashboard');

    // Send Tutor Request
    Route::post('/admin/request',[AdminVerificationController::class, 'index'])->name('admin.tutor.request');

    // Show Tutor Profile Data
    Route::get('/tutor/profile/{id}',[StudentController::class, 'tutorProfile'])->name('tutor.profile');

    // Join meeting
    Route::get('/meetings', [MeetingController::class, 'index'])->name('meetings.index');

    // Book a session
    Route::post('/tutor/sessions/book', [StudentController::class, 'bookSession'])->name('tutor.session.book');
    Route::post('/tutor/sessions/acceptCancel', [StudentController::class, 'acceptCancelRequest'])->name('tutor.session.acceptCancel');
    Route::post('/tutor/sessions/denyCancel', [StudentController::class, 'denyCancelRequest'])->name('tutor.session.denyCancel');

    Route::prefix('peer-group')->group(function () {
        Route::get('/{id}',[PeerGroupController::class, 'index'])->name('peergroup');
        // Create, Join, Leave, Delete Group
        Route::post('/create', [PeerGroupController::class, 'createGroup'])->name('peergroup.create');
        Route::post('/add', [PeerGroupController::class, 'addMember'])->name('peergroup.add');
        Route::post('/remove', [PeerGroupController::class, 'removeMember'])->name('peergroup.remove');
        Route::post('/leave', [PeerGroupController::class, 'leaveGroup'])->name('peergroup.leave');
        Route::post('/delete', [PeerGroupController::class, 'deleteGroup'])->name('peergroup.delete');
        Route::post('/book', [PeerGroupController::class, 'bookSession'])->name('peergroup.book.session');
        Route::post('/sessions/acceptCancel', [PeerGroupController::class, 'acceptCancelRequest'])->name('peerGroup.session.acceptCancel');
        Route::post('/sessions/denyCancel', [PeerGroupController::class, 'denyCancelRequest'])->name('peerGroup.session.denyCancel');
    });
    
    Route::prefix('feedback')->group(function () {
        Route::post('/create', [FeedbackRatingController::class, 'createFeedback'])->name('feedback.create');
        Route::post('/edit', [FeedbackRatingController::class, 'editFeedback'])->name('feedback.edit');
        Route::post('/delete', [FeedbackRatingController::class, 'deleteFeedback'])->name('feedback.delete');
    });
});


require __DIR__.'/auth.php';
