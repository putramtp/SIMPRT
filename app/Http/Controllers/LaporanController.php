<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCompletedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $userSignature = Auth::user()->signature
            ? asset('storage/' . Auth::user()->signature)
            : null;
        return view('laporan.create', compact('tasks', 'userSignature'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id'        => 'required|exists:tasks,id',
            'description'    => 'required|string',
            'photo'          => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'signature_tech' => 'nullable|string',
            'signature_cust' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status']  = 'submitted';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('laporan', 'public');
        }

        // Store non-empty signatures only
        if (empty($validated['signature_tech'])) unset($validated['signature_tech']);
        if (empty($validated['signature_cust'])) unset($validated['signature_cust']);

        $report = Report::create($validated);

        $report->load(['task.customer', 'teknisi']);
        User::role(['admin', 'sales'])->get()
            ->each(fn($u) => $u->notify(new TaskCompletedNotification($report)));

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dikirim.');
    }

    public function show(Report $laporan)
    {
        $laporan->load(['task.customer', 'teknisi']);
        return view('laporan.show', compact('laporan'));
    }

    public function edit(Report $laporan)
    {
        abort_if(
            Auth::id() !== $laporan->user_id && !Auth::user()->hasAnyRole(['admin', 'sales']),
            403
        );
        $tasks = Auth::user()->hasAnyRole(['admin', 'sales'])
            ? Task::with('customer')->get()
            : Task::where('assigned_to', Auth::id())->with('customer')->get();
        return view('laporan.edit', compact('laporan', 'tasks'));
    }

    public function update(Request $request, Report $laporan)
    {
        abort_if(
            Auth::id() !== $laporan->user_id && !Auth::user()->hasAnyRole(['admin', 'sales']),
            403
        );

        $validated = $request->validate([
            'description' => 'required|string',
            'status'      => 'required|in:draft,submitted,approved',
            'photo'       => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('laporan', 'public');
        }

        $laporan->update($validated);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Report $laporan)
    {
        abort_if(
            Auth::id() !== $laporan->user_id && !Auth::user()->hasAnyRole(['admin', 'sales']),
            403
        );
        $laporan->delete();
        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }

    public function pdf(Report $laporan)
    {
        $laporan->load(['task.customer', 'teknisi']);
        $pdf = Pdf::loadView('laporan.pdf', compact('laporan'))->setPaper('A4', 'portrait');
        $filename = 'laporan-' . $laporan->id . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }
}
