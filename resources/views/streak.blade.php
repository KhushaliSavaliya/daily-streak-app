<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center h-screen">
    <div class="bg-white p-10 rounded-3xl shadow-xl text-center border border-gray-200">
        <div class="text-indigo-600 font-bold uppercase tracking-widest text-sm mb-2">Day {{ $day }}</div>
        <h1 class="text-4xl font-black text-gray-900 mb-4">Daily Git Streak</h1>
        <p class="text-gray-500 mb-6">Current Status: 
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-medium">
                {{ $status }}
            </span>
        </p>
        <button onclick="alert('Day 1 Task Complete!')" class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-indigo-700 transition">
            Mark Done
        </button>
    </div>
</body>
</html>