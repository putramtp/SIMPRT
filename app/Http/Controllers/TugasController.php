<?php

namespace App\Http\Controllers;

use App\Events\TaskAssigned;
use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Task::with(['customer', 'assignee'])->latest();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', fn($t) => $t->customer?->name ?? '-')
                ->addColumn('assignee_name', fn($t) => $t->assignee?->name ?? '-')
                ->addColumn('status_badge', function ($t) {
                    $color = match ($t->status) {
                        'completed'  => 'success',
                        'in_progress' => 'warning',
                        default      => 'secondary',
                    };
                    $label = ucfirst(str_replace('_', ' ', $t->status));
                    return "<span class=\"badge bg-{$color}\">{$label}</span>";
                })
                ->addColumn('due_date_fmt', fn($t) => $t->due_date?->format('d/m/Y') ?? '-')
                ->addColumn('action', function ($t) {
                    return '<a href="' . route('tugas.show', $t) . '" class="btn btn-sm btn-outline-secondary">Detail</a> '
                        . '<a href="' . route('tugas.edit', $t) . '" class="btn btn-sm btn-outline-primary">Edit</a> '
                        . '<form action="' . route('tugas.destroy', $t) . '" method="POST" class="d-inline"'
                        . ' onsubmit="return confirm(\'Hapus tugas ini?\')">'
                        . csrf_field() . method_field('DELETE')
                        . '<button class="btn btn-sm btn-outline-danger">Hapus</button></form>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
        return view('tugas.index');
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $teknisi   = User::role('teknisi')->orderBy('name')->get();
        return view('tugas.create', compact('customers', 'teknisi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'assigned_to' => 'required|exists:users,id',
            'due_date'    => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status']     = 'pending';

        $task = Task::create($validated);

        // Only broadcast if Pusher is configured
        if (config('broadcasting.connections.pusher.key')) {
            TaskAssigned::dispatch($task);
        }

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function show(Task $tugas)
    {
        $tugas->load(['customer', 'assignee', 'reports.teknisi']);
        return view('tugas.show', ['task' => $tugas]);
    }

    public function edit(Task $tugas)
    {
        $customers = Customer::orderBy('name')->get();
        $teknisi   = User::role('teknisi')->orderBy('name')->get();
        return view('tugas.edit', ['task' => $tugas, 'customers' => $customers, 'teknisi' => $teknisi]);
    }

    public function update(Request $request, Task $tugas)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'assigned_to' => 'required|exists:users,id',
            'status'      => 'required|in:pending,in_progress,completed',
            'due_date'    => 'nullable|date',
        ]);

        $tugas->update($validated);

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $tugas)
    {
        $tugas->delete();
        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
