<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center h-screen text-white">
    
    <div class="bg-slate-800 p-8 rounded-3xl shadow-2xl w-96 border border-slate-700 relative overflow-hidden">
        <div class="absolute top-4 right-4 flex items-center gap-1 bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-xs font-bold border border-blue-500/30">
            ❄️ {{ $streak->freezes_available }} Freezes
        </div>

        <div class="text-center">
            <h2 class="text-slate-400 uppercase tracking-widest text-xs font-bold mb-1">Current Streak</h2>
            <div class="text-7xl font-black text-indigo-500 mb-2">{{ $streak->count }}</div>
            <p class="text-slate-400 text-sm mb-6">Days of consistency</p>

            <div class="flex items-center justify-center gap-2 mb-8">
                <span class="w-3 h-3 rounded-full {{ $status == 'Completed' ? 'bg-green-500 animate-pulse' : 'bg-yellow-500' }}"></span>
                <span class="text-sm font-medium">{{ $status }}</span>
            </div>

            <button id="markDone" class="w-full bg-indigo-600 hover:bg-indigo-500 py-4 rounded-2xl font-bold transition-all transform active:scale-95 shadow-lg shadow-indigo-500/20">
                Deploy Contribution
            </button>
        </div>

        <div class="mt-8 flex justify-between gap-1 opacity-50">
            @foreach(range(1, 14) as $day)
                <div class="w-4 h-4 rounded-sm {{ $day > 10 ? 'bg-indigo-500' : 'bg-slate-700' }}"></div>
            @endforeach
        </div>
    </div>

    <script>
        document.getElementById('markDone').onclick = function() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#6366f1', '#10b981', '#ffffff']
            });
            this.innerText = "Contribution Pushed!";
            this.classList.replace('bg-indigo-600', 'bg-green-600');
        };
    </script>
</body>
</html>