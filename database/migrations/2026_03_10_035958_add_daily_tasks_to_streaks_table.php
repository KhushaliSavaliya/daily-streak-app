<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('streaks', function (Blueprint $table) {
            $table->json('daily_tasks')->nullable();
            $table->date('last_task_reset')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streaks', function (Blueprint $table) {
            $table->dropColumn(['daily_tasks', 'last_task_reset']);
        });
    }
};
