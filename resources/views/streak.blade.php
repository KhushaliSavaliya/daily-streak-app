<!DOCTYPE html>
<html>
<title>Daily Streak Managment</title>

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
</head>

<body class="bg-slate-900 flex items-center justify-center h-screen text-white">

    <div class="bg-slate-800 p-8 rounded-3xl shadow-2xl w-96 border border-slate-700 relative overflow-hidden">
        <div class="flex justify-between items-center mb-6">
            <div id="freezeBadge"
                class="flex items-center gap-1 bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-xs font-bold border border-blue-500/30">
                ❄️ {{ $streak->freezes_available }} Freezes
            </div>
            <div id="levelBadge"
                class="flex items-center gap-1 bg-purple-500/20 text-purple-400 px-3 py-1 rounded-full text-xs font-bold border border-purple-500/30">
                ⭐ Level {{ $streak->level }}
            </div>
        </div>

        <div class="text-center">
            <h2 class="text-slate-400 uppercase tracking-widest text-xs font-bold mb-1">Current Streak</h2>
            <div class="text-7xl font-black text-indigo-500 mb-2">{{ $streak->count }}</div>
            <p class="text-slate-400 text-sm mb-4">Days of consistency</p>

            <div class="flex justify-center gap-4 mb-6">
                <div class="text-center">
                    <div class="text-xs text-slate-500 font-bold uppercase">Best</div>
                    <div class="text-lg font-bold text-slate-300">{{ $streak->best_streak }}</div>
                </div>
                <div class="border-l border-slate-700"></div>
                <div class="text-center">
                    <div class="text-xs text-slate-500 font-bold uppercase">XP</div>
                    <div class="text-lg font-bold text-slate-300">{{ $streak->xp }}</div>
                </div>
            </div>

            @php
                $milestones = [7, 30, 100];
                $nextMilestone = collect($milestones)->first(fn($m) => $m > $streak->count) ?? 365;
                $prevMilestone = collect($milestones)->reverse()->first(fn($m) => $m <= $streak->count) ?? 0;
                $progress = (($streak->count - $prevMilestone) / ($nextMilestone - $prevMilestone)) * 100;
                
                $xpProgress = $streak->getLevelProgress();
            @endphp

            <div class="mb-4">
                <div class="flex justify-between text-[10px] text-slate-500 font-bold uppercase mb-1">
                    <span>Next Milestone: {{ $nextMilestone }} Days</span>
                    <span>{{ round($progress) }}%</span>
                </div>
                <div class="w-full bg-slate-700 h-1.5 rounded-full overflow-hidden">
                    <div id="progressBar" class="bg-indigo-500 h-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between text-[10px] text-slate-500 font-bold uppercase mb-1">
                    <span>Level Progress</span>
                    <span id="xpPercent">{{ round($xpProgress) }}%</span>
                </div>
                <div class="w-full bg-slate-700 h-1.5 rounded-full overflow-hidden">
                    <div id="xpBar" class="bg-purple-500 h-full transition-all duration-1000" style="width: {{ $xpProgress }}%"></div>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 mb-6">
                <span
                    class="w-3 h-3 rounded-full {{ $status == 'Completed' ? 'bg-green-500 animate-pulse' : 'bg-yellow-500' }}"></span>
                <span class="text-sm font-medium">{{ $status }}</span>
            </div>

            <button id="markDone" {{ $status == 'Completed' ? 'disabled' : '' }}
                class="w-full {{ $status == 'Completed' ? 'bg-green-600 cursor-not_allowed' : 'bg-indigo-600 hover:bg-indigo-500 active:scale-95' }} py-4 rounded-2xl font-bold transition-all transform shadow-lg shadow-indigo-500/20">
                {{ $status == 'Completed' ? 'Contribution Pushed!' : 'Deploy Contribution' }}
            </button>
        </div>

        @if($streak->achievements && count($streak->achievements) > 0)
        <div class="mt-8">
            <h3 class="text-xs font-bold uppercase text-slate-500 tracking-tighter mb-3">Achievements</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($streak->achievements as $achievement)
                    <div title="Earned on {{ $achievement['earned_at'] }}" 
                        class="bg-amber-500/20 text-amber-500 border border-amber-500/30 px-2 py-1 rounded-lg text-[10px] font-bold flex items-center gap-1">
                        🏆 {{ $achievement['name'] }}
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-8">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-xs font-bold uppercase text-slate-500 tracking-tighter">Daily Quests</h3>
                <button onclick="openEditModal()" class="text-[10px] text-indigo-400 hover:text-indigo-300 font-bold uppercase">Edit</button>
            </div>
            <div class="space-y-2">
                @foreach($streak->daily_tasks as $index => $task)
                    <div class="flex items-center gap-3 bg-slate-700/30 p-3 rounded-xl border border-slate-700/50 group hover:border-indigo-500/30 transition-all">
                        <input type="checkbox" onchange="toggleTask({{ $index }}, this.checked)" 
                            {{ $task['completed'] ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-slate-600 bg-slate-800 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-800">
                        <span class="text-sm {{ $task['completed'] ? 'text-slate-500 line-through' : 'text-slate-300' }} transition-all">{{ $task['text'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-xs font-bold uppercase text-slate-500 tracking-tighter">Last 365 days</h3>
                <span class="text-[10px] text-slate-500">More activity = Brighter</span>
            </div>
            
            <div class="overflow-x-auto pb-2 custom-scrollbar">
                <div class="grid grid-rows-7 grid-flow-col gap-1 w-max">
                    @foreach ($history as $date => $count)
                        @php
                            // Determine color intensity
                            $color = 'bg-slate-700'; // Default empty
                            if ($count > 0 && $count <= 2) $color = 'bg-indigo-900';
                            if ($count > 2 && $count <= 5) $color = 'bg-indigo-600';
                            if ($count > 5) $color = 'bg-indigo-400';
                            
                            $isToday = $date == now()->format('Y-m-d');
                        @endphp
                        
                        <div title="{{ $date }}: {{ $count }} contributions" 
                            class="w-3 h-3 rounded-[2px] {{ $color }} {{ $isToday ? 'ring-[1px] ring-white/50' : '' }} transition-colors hover:ring-1 hover:ring-white">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Tasks Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-slate-800 border border-slate-700 w-full max-w-sm rounded-3xl p-6 shadow-2xl">
            <h3 class="text-lg font-bold mb-4">Edit Daily Quests</h3>
            <div class="space-y-4 mb-6">
                @foreach($streak->daily_tasks as $index => $task)
                <div>
                    <label class="text-[10px] font-bold uppercase text-slate-500 mb-1 block">Task {{ $index + 1 }}</label>
                    <input type="text" value="{{ $task['text'] }}" 
                        class="task-input w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>
                @endforeach
            </div>
            <div class="flex gap-3">
                <button onclick="closeEditModal()" class="flex-1 px-4 py-2 rounded-xl bg-slate-700 font-bold text-sm hover:bg-slate-600">Cancel</button>
                <button onclick="saveTasks()" class="flex-1 px-4 py-2 rounded-xl bg-indigo-600 font-bold text-sm hover:bg-indigo-500">Save</button>
            </div>
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1e293b; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748b; 
        }
    </style>

    <script>
        document.getElementById('markDone').onclick = async function() {
            const btn = this;
            if (btn.disabled) return;

            try {
                const response = await fetch('/streak/update', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const todaySquare = document.querySelector('.ring-white\\/30');
                    if (todaySquare) {
                        todaySquare.classList.remove('bg-slate-700');
                        todaySquare.classList.add('bg-indigo-900');
                    }

                    confetti({
                        particleCount: 150,
                        spread: 70,
                        origin: { y: 0.6 },
                        colors: ['#6366f1', '#10b981', '#f59e0b']
                    });

                    if (data.leveled_up) {
                        setTimeout(() => {
                            confetti({
                                particleCount: 200,
                                spread: 100,
                                origin: { y: 0.6 },
                                colors: ['#a855f7', '#ffffff']
                            });
                            alert(`⭐ LEVEL UP! You reached Level ${data.level}!`);
                            window.location.reload(); 
                        }, 500);
                    } else if (data.new_achievement) {
                        setTimeout(() => {
                            confetti({
                                particleCount: 200,
                                spread: 100,
                                origin: { y: 0.6 },
                                colors: ['#f59e0b', '#ffffff']
                            });
                            alert(`🎉 Achievement Unlocked: ${data.new_achievement}!`);
                            window.location.reload(); 
                        }, 500);
                    }

                    btn.innerText = "Contribution Pushed!";
                    btn.classList.replace('bg-indigo-600', 'bg-green-600');
                    btn.disabled = true;

                    document.querySelector('.text-7xl').innerText = data.count;
                    document.getElementById('freezeBadge').innerHTML = `❄️ ${data.freezes} Freezes`;
                    document.getElementById('levelBadge').innerHTML = `⭐ Level ${data.level}`;
                    document.getElementById('xpBar').style.width = `${data.xp_progress}%`;
                    document.getElementById('xpPercent').innerText = `${Math.round(data.xp_progress)}%`;
                    document.querySelectorAll('.text-lg.font-bold.text-slate-300')[1].innerText = data.xp;
                }
            } catch (error) {
                console.error("Failed to update streak", error);
            }
        };

        async function toggleTask(index, completed) {
            try {
                const response = await fetch('/streak/tasks/update', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ index, completed })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const span = event.target.nextElementSibling;
                    if (completed) {
                        span.classList.add('text-slate-500', 'line-through');
                        span.classList.remove('text-slate-300');
                    } else {
                        span.classList.remove('text-slate-500', 'line-through');
                        span.classList.add('text-slate-300');
                    }

                    if (data.all_completed) {
                        confetti({
                            particleCount: 50,
                            spread: 50,
                            origin: { y: 0.8 },
                            colors: ['#a855f7']
                        });
                    }
                }
            } catch (error) {
                console.error("Failed to update task", error);
            }
        }

        function openEditModal() {
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        async function saveTasks() {
            const inputs = document.querySelectorAll('.task-input');
            const tasks = Array.from(inputs).map(input => input.value);
            
            try {
                const response = await fetch('/streak/tasks/save', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ tasks })
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error("Failed to save tasks", error);
            }
        }
    </script>
</body>
</html>
