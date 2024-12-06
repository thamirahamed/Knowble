<?php

use App\Http\Controllers\AdminVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    // Approve tutor
    Route::post('/approve-tutor/{subjectId}', [AdminVerificationController::class, 'approveTutor'])->name('approve.tutor');
    // Reject tutor
    Route::post('/reject-tutor', [AdminVerificationController::class, 'rejectTutor'])->name('reject.tutor');
    // Delete tutor
    Route::delete('/tutors/{id}', [AdminVerificationController::class, 'deleteTutor'])->name('delete.tutor');
    Route::get('admin/data/{id}',[AdminVerificationController::class, 'getData'])->name('tutor.data');

});
//chat
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chatpage');
    Route::get('/api/chat/users', [ChatController::class, 'getUsers']);
    Route::post('/api/chat/start', [ChatController::class, 'startChat']);
    Route::post('/api/chat/message', [ChatController::class, 'sendMessage']);

    Route::get('/api/chat/messages/{chatId}', [ChatController::class, 'getMessages']);

});
// Admin Verification
Route::middleware(['auth' ,'studentportal'])->group(function () {
    Route::get('/admin/request',[AdminVerificationController::class, 'index'])->name('admin.tutor.request');
    Route::get('/tutor/dashboard',[AdminVerificationController::class, 'tutorDashboard'])->name('tutor.dashboard');
});


require __DIR__.'/auth.php';
