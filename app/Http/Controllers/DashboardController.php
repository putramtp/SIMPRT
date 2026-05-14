<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Report;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function sales()
    {
        $totalTasks      = Task::count();
        $pendingTasks    = Task::where('status', 'pending')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $completedTasks  = Task::where('status', 'completed')->count();
        $totalCustomers  = Customer::count();

        $teknisiList = User::role('teknisi')
            ->withCount(['tasks as tasks_count' => fn($q) => $q->whereIn('status', ['pending', 'in_progress'])])
            ->orderByDesc('tasks_count')
            ->get();

        return view('dashboard.sales', compact(
            'totalTasks', 'pendingTasks', 'inProgressTasks', 'completedTasks',
            'totalCustomers', 'teknisiList'
        ));
    }

    public function teknisi()
    {
        $myTasks   = Task::where('assigned_to', Auth::id())->with('customer')->latest()->get();
        $myReports = Report::where('user_id', Auth::id())->with('task')->latest()->take(5)->get();

        return view('dashboard.teknisi', compact('myTasks', 'myReports'));
    }
}
