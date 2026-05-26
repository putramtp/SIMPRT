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

        $query = Task::with(['customer:id,name', 'assignees:id,name'])->latest();

        if ($user->hasRole('teknisi')) {
            $query->whereHas('assignees', fn($q) => $q->where('users.id', $user->id));
        }

        $tasks = $query->paginate(20)->through(function ($t) {
            return [
                'id'            => $t->id,
                'title'         => $t->title,
                'description'   => $t->description,
                'status'        => $t->status,
                'due_date'      => $t->due_date?->toDateString(),
                'customer_name' => $t->customer?->name,
                'assignee_name' => $t->assignees->pluck('name')->join(', '),
            ];
        });

        return response()->json($tasks);
    }

    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        if ($user->hasRole('teknisi') && !$task->assignees()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $task->load(['customer:id,name,email,phone', 'assignees:id,name', 'reports:id,task_id,user_id,status,created_at']);

        return response()->json([
            'id'            => $task->id,
            'title'         => $task->title,
            'description'   => $task->description,
            'status'        => $task->status,
            'due_date'      => $task->due_date?->toDateString(),
            'customer'      => $task->customer,
            'assignees'     => $task->assignees,
            'reports_count' => $task->reports->count(),
        ]);
    }
}
