<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Streak extends Model
{
    protected $fillable = [ 
        'count',
        'best_streak',
        'freezes_available',
        'last_commit_date',
        'achievements',
        'xp',
        'level',
        'coins',
        'daily_tasks',
        'last_task_reset',
    ];

    protected $casts = [
        'achievements' => 'array',
        'daily_tasks' => 'array',
    ];

    public function getXpForNextLevel()
    {
        // Simple formula: Level 1 -> 100, Level 2 -> 250, Level 3 -> 500
        return $this->level * 100 + (pow($this->level, 2) * 50);
    }

    public function getLevelProgress()
    {
        $nextLevelXp = $this->getXpForNextLevel();
        $prevLevelXp = $this->level == 1 ? 0 : ($this->level - 1) * 100 + (pow($this->level - 1, 2) * 50);
        
        $currentLevelXp = $this->xp - $prevLevelXp;
        $neededXp = $nextLevelXp - $prevLevelXp;
        
        return min(100, max(0, ($currentLevelXp / $neededXp) * 100));
    }

    public function resetDailyTasks()
    {
        $today = now()->format('Y-m-d');
        if ($this->last_task_reset !== $today) {
            $defaultTasks = $this->daily_tasks ?? [
                ['text' => 'Push code to GitHub', 'completed' => false],
                ['text' => 'Read documentation', 'completed' => false],
                ['text' => 'Fix one bug', 'completed' => false],
            ];
            
            // Keep the task text, but reset completion status
            $tasks = array_map(function($task) {
                $task['completed'] = false;
                return $task;
            }, $defaultTasks);

            $this->update([
                'daily_tasks' => $tasks,
                'last_task_reset' => $today
            ]);
        }
    }
}
