<!DOCTYPE html>
<html>
<title>Daily Streak Managment</title>

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
</head>

<body class="bg-slate-900 flex items-center justify-center h-screen text-white">

    <div class="bg-slate-800 p-8 rounded-3xl shadow-2xl w-96 border border-slate-700 relative overflow-hidden">
        <div id="freezeBadge"
            class="absolute top-4 right-4 flex items-center gap-1 bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-xs font-bold border border-blue-500/30">
            ❄️ {{ $streak->freezes_available }} Freezes
        </div>

        <div class="text-center">
            <h2 class="text-slate-400 uppercase tracking-widest text-xs font-bold mb-1">Current Streak</h2>
            <div class="text-7xl font-black text-indigo-500 mb-2">{{ $streak->count }}</div>
            <p class="text-slate-400 text-sm mb-6">Days of consistency</p>

            @php
                $milestones = [7, 30, 100];
                $nextMilestone = collect($milestones)->first(fn($m) => $m > $streak->count) ?? 365;
                $prevMilestone = collect($milestones)->reverse()->first(fn($m) => $m <= $streak->count) ?? 0;
                $progress = (($streak->count - $prevMilestone) / ($nextMilestone - $prevMilestone)) * 100;
            @endphp

            <div class="mb-6">
                <div class="flex justify-between text-[10px] text-slate-500 font-bold uppercase mb-1">
                    <span>Next Milestone: {{ $nextMilestone }} Days</span>
                    <span>{{ round($progress) }}%</span>
                </div>
                <div class="w-full bg-slate-700 h-1.5 rounded-full overflow-hidden">
                    <div id="progressBar" class="bg-indigo-500 h-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 mb-8">
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
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-xs font-bold uppercase text-slate-500 tracking-tighter">Last 2 weeks</h3>
                <span class="text-[10px] text-slate-500">More activity = Brighter</span>
            </div>
            <div class="flex justify-between gap-1">
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
                        class="w-5 h-5 rounded-sm {{ $color }} {{ $isToday ? 'ring-2 ring-white/30' : '' }} transition-all hover:scale-110">
                    </div>
                @endforeach
            </div>
        </div>
    </div>

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

                    if (data.new_achievement) {
                        setTimeout(() => {
                            confetti({
                                particleCount: 200,
                                spread: 100,
                                origin: { y: 0.6 },
                                colors: ['#f59e0b', '#ffffff']
                            });
                            alert(`🎉 Achievement Unlocked: ${data.new_achievement}!`);
                            window.location.reload(); // Refresh to show the new badge
                        }, 500);
                    }

                    btn.innerText = "Contribution Pushed!";
                    btn.classList.replace('bg-indigo-600', 'bg-green-600');
                    btn.disabled = true;

                    document.querySelector('.text-7xl').innerText = data.count;
                    document.getElementById('freezeBadge').innerHTML = `❄️ ${data.freezes} Freezes`;
                }
            } catch (error) {
                console.error("Failed to update streak", error);
            }
        };
    </script>
</body>

</html>
