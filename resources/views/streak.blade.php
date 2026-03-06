<!DOCTYPE html>
<html>
<title>Daily Streak Managment</title>

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
</head>

<body class="bg-slate-900 flex items-center justify-center h-screen text-white">

    <div class="bg-slate-800 p-8 rounded-3xl shadow-2xl w-96 border border-slate-700 relative overflow-hidden">
        <div
            class="absolute top-4 right-4 flex items-center gap-1 bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-xs font-bold border border-blue-500/30">
            ❄️ {{ $streak->freezes_available }} Freezes
        </div>

        <div class="text-center">
            <h2 class="text-slate-400 uppercase tracking-widest text-xs font-bold mb-1">Current Streak</h2>
            <div class="text-7xl font-black text-indigo-500 mb-2">{{ $streak->count }}</div>
            <p class="text-slate-400 text-sm mb-6">Days of consistency</p>

            <div class="flex items-center justify-center gap-2 mb-8">
                <span
                    class="w-3 h-3 rounded-full {{ $status == 'Completed' ? 'bg-green-500 animate-pulse' : 'bg-yellow-500' }}"></span>
                <span class="text-sm font-medium">{{ $status }}</span>
            </div>

            <button id="markDone"
                class="w-full bg-indigo-600 hover:bg-indigo-500 py-4 rounded-2xl font-bold transition-all transform active:scale-95 shadow-lg shadow-indigo-500/20">
                Deploy Contribution
            </button>
        </div>

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
                        // Change color to indicate progress immediately
                        todaySquare.classList.remove('bg-slate-700');
                        todaySquare.classList.add('bg-indigo-900');
                    }

                    confetti({
                        particleCount: 150,
                        spread: 70,
                        origin: {
                            y: 0.6
                        },
                        colors: ['#6366f1', '#10b981', '#ffffff']
                    });

                    btn.innerText = "Contribution Pushed!";
                    btn.classList.replace('bg-indigo-600', 'bg-green-600');
                    btn.disabled = true;

                    document.querySelector('.text-7xl').innerText = data.count;
                }
            } catch (error) {
                console.error("Failed to update streak", error);
            }
        };
    </script>
</body>

</html>
