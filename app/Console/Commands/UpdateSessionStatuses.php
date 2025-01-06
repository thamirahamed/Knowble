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
        // Get current timestamp
        $now = Carbon::now();
        
        // Update "pending" sessions if session_date and start_time combined have passed
        $pendingSessions = TutorSession::where('status', 'pending')
        ->whereRaw('datetime(session_date || " " || start_time) <= ?', [$now->toDateTimeString()])
        ->get();

        foreach ($pendingSessions as $session) {
        $session->status = 'completed';
        $session->meeting_url = null;
        $session->save();
        }

        // Update "booked" sessions if session_date and end_time combined have passed
        $bookedSessions = TutorSession::where('status', 'booked')
        ->whereRaw('datetime(session_date || " " || end_time) <= ?', [$now->toDateTimeString()])
        ->get();

        foreach ($bookedSessions as $session) {
        $session->status = 'completed';
        $session->meeting_url = null;
        $session->save();
        }

        $this->info('Session statuses updated successfully. Current time: ' . $now);
    }
}
