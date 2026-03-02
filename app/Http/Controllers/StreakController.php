<?php

namespace App\Http\Controllers;

use App\Models\Streak;
use Illuminate\Http\Request;

class StreakController extends Controller
{
    public function index()
    {
        $streak = Streak::firstOrCreate(['id' => 1]); // Assuming 1 user for demo
        $today = now()->startOfDay();
        $lastDate = $streak->last_commit_date ? \Carbon\Carbon::parse($streak->last_commit_date) : null;

        $status = "Pending";
        
        if ($lastDate && $lastDate->equalTo($today)) {
            $status = "Completed";
        } elseif ($lastDate && $lastDate->diffInDays($today) > 1) {
            // Advanced: Check for Streak Freeze
            if ($streak->freezes_available > 0) {
                $status = "Frozen (Protected)";
            } else {
                $streak->update(['count' => 0]); // Reset streak
                $status = "Broken";
            }
        }

        return view('streak', compact('streak', 'status'));
    }
}
