<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\TutorSession;

class UpdateSessionStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-session-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current timestamp
        $now = Carbon::now();

        // Update "pending" sessions
        TutorSession::where('status', 'pending')
        ->where('session_date', '<=', $now->toDateString())
        ->where(function ($query) use ($now) {
            $query->where('session_date', '<', $now->toDateString()) // Past dates
                ->orWhere(function ($query) use ($now) {
                    $query->where('session_date', $now->toDateString()) // Today
                            ->where('start_time', '<=', $now->toTimeString());
                });
        })
        ->update(['status' => 'unbooked', 'meeting_url' => null]);

        // Update "cancelRequest" sessions
        TutorSession::where('status', 'cancelRequest')
        ->where('session_date', '<=', $now->toDateString())
        ->where(function ($query) use ($now) {
            $query->where('session_date', '<', $now->toDateString()) // Past dates
                ->orWhere(function ($query) use ($now) {
                    $query->where('session_date', $now->toDateString()) // Today
                        ->where('start_time', '<=', $now->toTimeString());
                });
        })
        ->update(['status' => 'cancelled', 'meeting_url' => null]);

        // Update "alt" sessions
        TutorSession::where('status', 'alt')
        ->where('session_date', '<=', $now->toDateString())
        ->where(function ($query) use ($now) {
            $query->where('session_date', '<', $now->toDateString()) // Past dates
                ->orWhere(function ($query) use ($now) {
                    $query->where('session_date', $now->toDateString()) // Today
                        ->where('start_time', '<=', $now->toTimeString());
                });
        })
        ->update(['status' => 'unbooked', 'meeting_url' => null]);

        // Update "booked" sessions
        TutorSession::where('status', 'booked')
        ->where('session_date', '<=', $now->toDateString())
        ->where(function ($query) use ($now) {
            $query->where('session_date', '<', $now->toDateString()) // Past dates
                ->orWhere(function ($query) use ($now) {
                    $query->where('session_date', $now->toDateString()) // Today
                            ->where('end_time', '<=', $now->toTimeString());
                });
        })
        ->update(['status' => 'completed', 'meeting_url' => null]);

        $this->info('Session statuses updated successfully. Current time: ' . $now);
    }
}
