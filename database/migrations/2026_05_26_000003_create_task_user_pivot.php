<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('task_user')) {
            Schema::create('task_user', function (Blueprint $table) {
                $table->foreignId('task_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->primary(['task_id', 'user_id']);
                $table->timestamps();
            });
        }

        // Migrate existing single-assignee data to pivot (safe to re-run)
        if (Schema::hasColumn('tasks', 'assigned_to')) {
            DB::table('tasks')->whereNotNull('assigned_to')->orderBy('id')->each(function ($task) {
                DB::table('task_user')->insertOrIgnore([
                    'task_id'    => $task->id,
                    'user_id'    => $task->assigned_to,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        }

        if (Schema::hasColumn('tasks', 'assigned_to')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable()->after('customer_id');
        });

        // Restore first assignee per task from pivot
        DB::table('task_user')->orderBy('task_id')->get()
            ->groupBy('task_id')
            ->each(function ($rows, $taskId) {
                DB::table('tasks')->where('id', $taskId)->update([
                    'assigned_to' => $rows->first()->user_id,
                ]);
            });

        Schema::dropIfExists('task_user');
    }
};
