<?php

namespace App\Http\Controllers;

use App\Models\Streak;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StreakController extends Controller
{
    public function index()
    {
        $streak = Streak::firstOrCreate(['id' => 1]);
        $today = now()->startOfDay();
        $lastDate = $streak->last_commit_date ? Carbon::parse($streak->last_commit_date)->startOfDay() : null;

        $status = "Pending";

        if ($lastDate) {
            if ($lastDate->equalTo($today)) {
                $status = "Completed";
            } elseif ($lastDate->diffInDays($today) > 1) {
                // Check if we can use a freeze for the missed day(s)
                if ($streak->freezes_available > 0) {
                    $streak->decrement('freezes_available');
                    // Move last_commit to yesterday so the streak continues
                    $streak->update(['last_commit_date' => $today->copy()->subDay()]);
                    $status = "Frozen (Protected)";
                } else {
                    $streak->update(['count' => 0]);
                    $status = "Broken";
                }
            }
        }

        return view('streak', compact('streak', 'status'));
    }

    public function store()
    {
        $streak = Streak::find(1);
        $today = now()->startOfDay();
        $lastDate = $streak->last_commit_date ? Carbon::parse($streak->last_commit_date)->startOfDay() : null;

        // Only increment if not already done today
        if (!$lastDate || $lastDate->lessThan($today)) {
            $streak->increment('count');
            $streak->update(['last_commit_date' => now()]);
        }

        return response()->json(['success' => true, 'count' => $streak->count]);
    }
}
