<?php

namespace App\Http\Controllers;

use App\Events\TaskAssigned;
use App\Models\Customer;
use App\Models\Task;
use App\Models\Template;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskStartedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Task::with(['customer', 'assignees'])->latest();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', fn($t) => $t->customer?->name ?? '-')
                ->addColumn('assignee_name', fn($t) => $t->assignees->pluck('name')->join(', ') ?: '-')
                ->addColumn('status_badge', function ($t) {
                    $color = match ($t->status) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        default       => 'secondary',
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
        $teknisi   = User::role('teknisi')
            ->withCount(['tasks as active_tasks' => fn($q) => $q->whereIn('status', ['pending', 'in_progress'])])
            ->orderBy('name')->get();
        $templates = Template::orderBy('name')->get();
        return view('tugas.create', compact('customers', 'teknisi', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'assignees'   => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
            'due_date'    => 'nullable|date',
            'priority'    => 'nullable|in:low,normal,high,urgent',
            'template_id' => 'nullable|exists:templates,id',
        ]);

        $task = Task::create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'customer_id' => $validated['customer_id'],
            'created_by'  => Auth::id(),
            'status'      => 'pending',
            'priority'    => $validated['priority'] ?? 'normal',
            'due_date'    => $validated['due_date'] ?? null,
            'template_id' => $validated['template_id'] ?? null,
        ]);

        $task->assignees()->sync($validated['assignees']);
        $task->load('assignees');

        foreach ($task->assignees as $assignee) {
            $assignee->notify(new TaskAssignedNotification($task));
        }

        if (config('broadcasting.connections.pusher.key')) {
            TaskAssigned::dispatch($task);
        }

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function start(Task $tugas)
    {
        if (!$tugas->assignees()->where('user_id', Auth::id())->exists()) abort(403);
        if ($tugas->status !== 'pending') {
            return back()->with('info', 'Status tugas sudah berubah.');
        }
        $tugas->update(['status' => 'in_progress']);

        $tugas->load(['assignees', 'customer']);
        User::role(['admin', 'sales'])->get()
            ->each(fn($u) => $u->notify(new TaskStartedNotification($tugas)));

        return back()->with('success', 'Tugas dimulai. Silakan isi laporan setelah selesai.');
    }

    public function show(Task $tugas)
    {
        $tugas->load(['customer', 'assignees', 'reports.teknisi']);
        return view('tugas.show', ['task' => $tugas]);
    }

    public function edit(Task $tugas)
    {
        $customers       = Customer::orderBy('name')->get();
        $teknisi         = User::role('teknisi')
            ->withCount(['tasks as active_tasks' => fn($q) => $q->whereIn('status', ['pending', 'in_progress'])])
            ->orderBy('name')->get();
        $templates       = Template::orderBy('name')->get();
        $selectedTeknisi = $tugas->assignees->pluck('id')->toArray();
        return view('tugas.edit', [
            'task'            => $tugas,
            'customers'       => $customers,
            'teknisi'         => $teknisi,
            'templates'       => $templates,
            'selectedTeknisi' => $selectedTeknisi,
        ]);
    }

    public function update(Request $request, Task $tugas)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'assignees'   => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
            'status'      => 'required|in:pending,in_progress,completed',
            'due_date'    => 'nullable|date',
            'priority'    => 'nullable|in:low,normal,high,urgent',
            'template_id' => 'nullable|exists:templates,id',
        ]);

        $oldAssigneeIds = $tugas->assignees->pluck('id')->toArray();

        $tugas->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'customer_id' => $validated['customer_id'],
            'status'      => $validated['status'],
            'due_date'    => $validated['due_date'] ?? null,
            'priority'    => $validated['priority'] ?? 'normal',
            'template_id' => $validated['template_id'] ?? null,
        ]);

        $tugas->assignees()->sync($validated['assignees']);

        // Notify only newly added assignees
        $newIds = array_diff($validated['assignees'], $oldAssigneeIds);
        if ($newIds) {
            User::whereIn('id', $newIds)->get()
                ->each(fn($u) => $u->notify(new TaskAssignedNotification($tugas)));
        }

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $tugas)
    {
        $tugas->delete();
        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
