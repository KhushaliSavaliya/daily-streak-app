<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Streak;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StreakController extends Controller
{
    public function index()
    {
        $streak = Streak::firstOrCreate(['id' => 1]);
        
        // 1. Handle Streak Logic (Breaks/Freezes)
        $today = now()->startOfDay();
        $lastDate = $streak->last_commit_date ? Carbon::parse($streak->last_commit_date)->startOfDay() : null;
        $status = "Pending";

        if ($lastDate && !$lastDate->equalTo($today)) {
            $diff = $lastDate->diffInDays($today);
            if ($diff > 1) {
                if ($streak->freezes_available > 0) {
                    $streak->decrement('freezes_available');
                    $streak->update(['last_commit_date' => $today->copy()->subDay()]);
                    $status = "Frozen (Protected)";
                } else {
                    $streak->update(['count' => 0]);
                    $status = "Broken";
                }
            }
        } elseif ($lastDate && $lastDate->equalTo($today)) {
            $status = "Completed";
        }

        $history = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            // Get count from DB or default to 0
            $history[$date] = Contribution::where('day', $date)->value('count') ?? 0;
        }

        return view('streak', compact('streak', 'status', 'history'));
    }

    public function store()
    {
        $streak = Streak::find(1);
        $todayDate = now()->format('Y-m-d');

        // Update Heatmap
        $contribution = Contribution::firstOrCreate(['day' => $todayDate]);
        $contribution->increment('count');

        // Update Main Streak
        $lastDate = $streak->last_commit_date ? Carbon::parse($streak->last_commit_date)->format('Y-m-d') : null;

        if ($lastDate !== $todayDate) {
            $streak->increment('count');
            
            // Track Best Streak
            if ($streak->count > $streak->best_streak) {
                $streak->best_streak = $streak->count;
            }
            
            // Achievement Logic
            $milestones = [
                7 => ['name' => 'Week Warrior', 'reward' => 1],
                30 => ['name' => 'Monthly Master', 'reward' => 3],
                100 => ['name' => 'Centurion', 'reward' => 10],
            ];

            $newAchievement = null;
            if (isset($milestones[$streak->count])) {
                $milestone = $milestones[$streak->count];
                $currentAchievements = $streak->achievements ?? [];
                
                // check if already awarded (though based on count it shouldn't be, but safe check)
                $alreadyAwarded = collect($currentAchievements)->contains('name', $milestone['name']);

                if (!$alreadyAwarded) {
                    $streak->increment('freezes_available', $milestone['reward']);
                    $currentAchievements[] = [
                        'name' => $milestone['name'],
                        'earned_at' => now()->format('Y-m-d'),
                        'count' => $streak->count
                    ];
                    $streak->achievements = $currentAchievements;
                    $newAchievement = $milestone['name'];
                }
            }
            
            $streak->last_commit_date = now();
            $streak->save();
        }

        return response()->json([
            'success' => true, 
            'count' => $streak->count,
            'best' => $streak->best_streak,
            'freezes' => $streak->freezes_available,
            'new_achievement' => $newAchievement ?? null
        ]);
    }
}
