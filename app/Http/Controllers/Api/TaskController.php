<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Task::with(['customer:id,name', 'assignee:id,name'])->latest();

        if ($user->hasRole('teknisi')) {
            $query->where('assigned_to', $user->id);
        }

        $tasks = $query->paginate(20)->through(function ($t) {
            return [
                'id'            => $t->id,
                'title'         => $t->title,
                'description'   => $t->description,
                'status'        => $t->status,
                'due_date'      => $t->due_date?->toDateString(),
                'customer_name' => $t->customer?->name,
                'assignee_name' => $t->assignee?->name,
            ];
        });

        return response()->json($tasks);
    }

    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        if ($user->hasRole('teknisi') && $task->assigned_to !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $task->load(['customer:id,name,email,phone', 'assignee:id,name', 'reports:id,task_id,user_id,status,created_at']);

        return response()->json([
            'id'            => $task->id,
            'title'         => $task->title,
            'description'   => $task->description,
            'status'        => $task->status,
            'due_date'      => $task->due_date?->toDateString(),
            'customer'      => $task->customer,
            'assignee'      => $task->assignee,
            'reports_count' => $task->reports->count(),
        ]);
    }
}
