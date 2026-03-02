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
        $lastDate = $streak->last_commit_date ? Carbon::parse($streak->last_commit_date) : null;

        $status = "Pending";
        
        if ($lastDate && $lastDate->equalTo($today)) {
            $status = "Completed";
        } elseif ($lastDate && $lastDate->diffInDays($today) > 1) {
            if ($streak->freezes_available > 0) {
                $status = "Frozen (Protected)";
            } else {
                $streak->update(['count' => 0]);
                $status = "Broken";
            }
        }

        return view('streak', compact('streak', 'status'));
    }
}
