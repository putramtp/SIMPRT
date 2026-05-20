<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

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

    public function teknisiAll(Request $request)
    {
        if ($request->ajax()) {
            $query = Task::with(['customer', 'assignee'])->latest();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', fn($t) => $t->customer?->name ?? '-')
                ->addColumn('assignee_name', fn($t) => $t->assignee
                    ? $t->assignee->name
                    : '<span class="text-muted fst-italic" style="font-size:.8rem;">Belum ditugaskan</span>')
                ->addColumn('status_badge', function ($t) {
                    $map = [
                        'pending'     => ['#FFA000', 'rgba(255,160,0,.12)',  'Pending'],
                        'in_progress' => ['#1565C0', 'rgba(21,101,192,.12)', 'Berjalan'],
                        'completed'   => ['#388E3C', 'rgba(56,142,60,.12)',  'Selesai'],
                    ];
                    [$color, $bg, $label] = $map[$t->status] ?? ['#6c757d', 'rgba(108,117,125,.12)', $t->status];
                    return "<span style=\"display:inline-flex;align-items:center;gap:4px;border-radius:20px;"
                        . "padding:2px 10px;font-size:.72rem;font-weight:600;"
                        . "background:{$bg};color:{$color};\">{$label}</span>";
                })
                ->addColumn('due_date_fmt', fn($t) => $t->due_date?->format('d/m/Y') ?? '-')
                ->addColumn('action', fn($t) =>
                    '<a href="' . route('tugas.show', $t) . '" class="btn btn-sm btn-outline-secondary py-0 px-2">'
                    . '<i class="ti ti-eye"></i></a>')
                ->rawColumns(['assignee_name', 'status_badge', 'action'])
                ->make(true);
        }

        $totalTasks      = Task::count();
        $pendingTasks    = Task::where('status', 'pending')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $completedTasks  = Task::where('status', 'completed')->count();

        $teknisiList = User::role('teknisi')
            ->withCount([
                'tasks as pending_count'   => fn($q) => $q->where('status', 'pending'),
                'tasks as progress_count'  => fn($q) => $q->where('status', 'in_progress'),
                'tasks as completed_count' => fn($q) => $q->where('status', 'completed'),
            ])
            ->orderByDesc('progress_count')
            ->get();

        return view('dashboard.teknisi-all', compact(
            'totalTasks', 'pendingTasks', 'inProgressTasks', 'completedTasks', 'teknisiList'
        ));
    }

    public function teknisiMy()
    {
        $myTasks   = Task::where('assigned_to', Auth::id())->with('customer')->latest()->get();
        $myReports = Report::where('user_id', Auth::id())->with('task.customer')->latest()->take(5)->get();

        return view('dashboard.teknisi-my', compact('myTasks', 'myReports'));
    }

    public function customer()
    {
        $customer = Auth::user()->customer;

        if (!$customer) {
            abort(403, 'Akun Anda belum terhubung ke data customer. Hubungi administrator.');
        }

        $reports = Report::with(['task', 'teknisi'])
            ->whereHas('task', fn($q) => $q->where('customer_id', $customer->id))
            ->latest()
            ->get();

        return view('dashboard.customer', compact('customer', 'reports'));
    }
}
