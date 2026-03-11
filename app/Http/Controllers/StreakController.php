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
        $streak->resetDailyTasks();
        
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

        $startDate = now()->subDays(364)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        // Fetch all contributions for the last 365 days in one query
        $contributions = Contribution::whereBetween('day', [$startDate, $endDate])
            ->pluck('count', 'day')
            ->toArray();

        $history = [];
        for ($i = 364; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $history[$date] = $contributions[$date] ?? 0;
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
            
            // Award XP
            $streak->xp += 10;
            $streak->coins += 5; // Award coins for daily contribution
            $leveledUp = false;
            $nextLevelThreshold = $streak->getXpForNextLevel();
            $bonusXp = 0; // Initialize bonusXp
            
            if ($streak->xp >= $nextLevelThreshold) {
                $streak->level++;
                $leveledUp = true;
                // Bonus for leveling up? Maybe a freeze?
                $streak->increment('freezes_available');
            }

            $streak->save();
        }

        return response()->json([
            'success' => true, 
            'count' => $streak->count,
            'best' => $streak->best_streak,
            'freezes' => $streak->freezes_available,
            'new_achievement' => $newAchievement ?? null,
            'xp' => $streak->xp,
            'coins' => $streak->coins,
            'level' => $streak->level,
            'leveled_up' => $leveledUp,
            'next_level_xp' => $streak->getXpForNextLevel(),
            'xp_progress' => $streak->getLevelProgress(),
            'bonus_xp' => $bonusXp ?? 0
        ]);
    }

    public function updateTasks(Request $request)
    {
        $streak = Streak::find(1);
        $tasks = $streak->daily_tasks;
        $index = $request->index;

        if (isset($tasks[$index])) {
            $alreadyCompleted = $tasks[$index]['completed'];
            $tasks[$index]['completed'] = $request->completed;
            $streak->daily_tasks = $tasks;
            
            $xpAwarded = 0;
            $coinsAwarded = 0;
            $bonusAwarded = false;

            if ($request->completed && !$alreadyCompleted) {
                // Award rewards for completing a task
                $xpAwarded = 2;
                $coinsAwarded = 1;
                $streak->xp += $xpAwarded;
                $streak->coins += $coinsAwarded;
            }

            // If all tasks are completed, award a bonus!
            $allCompleted = collect($tasks)->every('completed', true);
            
            if ($allCompleted && $request->completed && !$alreadyCompleted) {
                $xpAwarded += 10;
                $coinsAwarded += 5;
                $streak->xp += 10;
                $streak->coins += 5;
                $bonusAwarded = true;
            }

            $leveledUp = false;
            if ($streak->xp >= $streak->getXpForNextLevel()) {
                $streak->level++;
                $leveledUp = true;
                $streak->increment('freezes_available');
            }

            $streak->save();

            return response()->json([
                'success' => true,
                'all_completed' => $allCompleted,
                'xp_awarded' => $xpAwarded,
                'coins_awarded' => $coinsAwarded,
                'bonus_awarded' => $bonusAwarded,
                'xp' => $streak->xp,
                'coins' => $streak->coins,
                'level' => $streak->level,
                'leveled_up' => $leveledUp,
                'xp_progress' => $streak->getLevelProgress()
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    public function buyFreeze()
    {
        $streak = Streak::find(1);
        $cost = 50; // Cost of 1 freeze

        if ($streak->coins >= $cost) {
            $streak->decrement('coins', $cost);
            $streak->increment('freezes_available');
            return response()->json([
                'success' => true,
                'coins' => $streak->coins,
                'freezes' => $streak->freezes_available
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Not enough coins!'
        ], 400);
    }

    public function saveTaskNames(Request $request)
    {
        $streak = Streak::find(1);
        $newTasks = $request->tasks; // Array of strings
        
        $currentTasks = $streak->daily_tasks;
        $updatedTasks = [];
        
        foreach ($newTasks as $i => $text) {
            $updatedTasks[] = [
                'text' => $text ?: ($currentTasks[$i]['text'] ?? "Task " . ($i + 1)),
                'completed' => $currentTasks[$i]['completed'] ?? false
            ];
        }
        
        $streak->update(['daily_tasks' => $updatedTasks]);
        
        return response()->json(['success' => true]);
    }
}
