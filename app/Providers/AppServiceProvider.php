<?php

namespace App\Providers;

use App\Models\Tutor;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use App\Models\Profile;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot() : void
    {
        Vite::prefetch(concurrency: 3);

        Inertia::share([
            'tutor' => function () {
                $user = Auth::user();
                if (!$user) {
                    return null;
                }
                $tutor = Tutor::where('user_id', $user->id)->first();
                $tutorstatus = $tutor ? $tutor->status : null;
                return $tutorstatus;
            },
        ]);
    }
}
