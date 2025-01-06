<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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

        // Update "pending" sessions if session_date and start_time have passed
        $pendingSessions = TutorSession::where('status', 'pending')
            ->where(function ($query) use ($now) {
                $query->whereDate('session_date', '<=', $now->toDateString())
                      ->whereTime('start_time', '<=', $now->toTimeString());
            })
            ->get();

        foreach ($pendingSessions as $session) {
            $session->status = 'completed';
            $session->meeting_url = null;
            $session->save();
        }

        // Update "booked" sessions if session_date and end_time have passed
        $bookedSessions = TutorSession::where('status', 'booked')
            ->where(function ($query) use ($now) {
                $query->whereDate('session_date', '<=', $now->toDateString())
                      ->whereTime('end_time', '<=', $now->toTimeString());
            })
            ->get();

        foreach ($bookedSessions as $session) {
            $session->status = 'completed';
            $session->meeting_url = null;
            $session->save();
        }

        $this->info('Session statuses updated successfully.');
    }
}
