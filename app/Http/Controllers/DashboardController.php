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
        $totalTasks     = Task::count();
        $pendingTasks   = Task::where('status', 'pending')->count();
        $completedTasks = Task::where('status', 'completed')->count();
        $totalCustomers = Customer::count();
        $recentTasks    = Task::with(['customer', 'assignee'])->latest()->take(5)->get();

        return view('dashboard.sales', compact(
            'totalTasks', 'pendingTasks', 'completedTasks', 'totalCustomers', 'recentTasks'
        ));
    }

    public function teknisi()
    {
        $myTasks   = Task::where('assigned_to', Auth::id())->with('customer')->latest()->get();
        $myReports = Report::where('user_id', Auth::id())->with('task')->latest()->take(5)->get();

        return view('dashboard.teknisi', compact('myTasks', 'myReports'));
    }
}
