<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Report::with(['task.customer', 'teknisi'])->latest();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('task_title', fn($r) => $r->task?->title ?? '-')
                ->addColumn('customer_name', fn($r) => $r->task?->customer?->name ?? '-')
                ->addColumn('teknisi_name', fn($r) => $r->teknisi?->name ?? '-')
                ->addColumn('status_badge', fn($r) => '<span class="badge bg-info">' . ucfirst($r->status) . '</span>')
                ->addColumn('tanggal', fn($r) => $r->created_at->format('d/m/Y'))
                ->addColumn('action', function ($r) {
                    return '<a href="' . route('laporan.show', $r) . '" class="btn btn-sm btn-outline-secondary">Detail</a> '
                        . '<form action="' . route('laporan.destroy', $r) . '" method="POST" class="d-inline"'
                        . ' onsubmit="return confirm(\'Hapus laporan ini?\')">'
                        . csrf_field() . method_field('DELETE')
                        . '<button class="btn btn-sm btn-outline-danger">Hapus</button></form>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
        return view('laporan.index');
    }

    public function create()
    {
        $tasks = Task::where('assigned_to', Auth::id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->with('customer')
            ->get();
        return view('laporan.create', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id'     => 'required|exists:tasks,id',
            'description' => 'required|string',
            'photo'       => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status']  = 'submitted';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('laporan', 'public');
        }

        Report::create($validated);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dikirim.');
    }

    public function show(Report $laporan)
    {
        $laporan->load(['task.customer', 'teknisi']);
        return view('laporan.show', compact('laporan'));
    }

    public function edit(Report $laporan)
    {
        $tasks = Task::where('assigned_to', Auth::id())->with('customer')->get();
        return view('laporan.edit', compact('laporan', 'tasks'));
    }

    public function update(Request $request, Report $laporan)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'status'      => 'required|in:draft,submitted,approved',
            'photo'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('laporan', 'public');
        }

        $laporan->update($validated);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Report $laporan)
    {
        $laporan->delete();
        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }
}
