<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCompletedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        // Redirect to edit if a draft already exists for the requested task
        if ($taskId = request('task_id')) {
            $draft = Report::where('user_id', Auth::id())
                ->where('task_id', $taskId)
                ->where('status', 'draft')
                ->first();
            if ($draft) {
                return redirect()->route('laporan.edit', $draft)
                    ->with('info', 'Laporan draft ditemukan. Tambahkan tanda tangan customer untuk mengirim.');
            }
        }

        $submittedTaskIds = Report::where('user_id', Auth::id())
            ->where('status', 'submitted')
            ->pluck('task_id');
        $tasks = Task::whereHas('assignees', fn($q) => $q->where('users.id', Auth::id()))
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotIn('id', $submittedTaskIds)
            ->with(['customer', 'template'])
            ->get();
        $taskTemplates = $tasks->mapWithKeys(function ($task) {
            if (!$task->template_id || !$task->template) return [$task->id => null];
            return [$task->id => [
                'name'     => $task->template->name,
                'sections' => $task->template->fields ?? [],
            ]];
        })->toArray();
        return view('laporan.create', compact('tasks', 'taskTemplates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_id'        => 'required|exists:tasks,id',
            'description'    => 'required|string',
            'photos'         => 'nullable|array|max:10',
            'photos.*'       => 'image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'signature_tech' => 'nullable|string',
            'signature_cust' => 'nullable|string',
            'template_data'  => 'nullable|array',
        ]);

        $hasCustSig = $request->filled('signature_cust');

        $data = [
            'task_id'     => $request->task_id,
            'description' => $request->description,
            'user_id'     => Auth::id(),
            'status'      => $hasCustSig ? 'submitted' : 'draft',
        ];

        if ($hasCustSig) {
            $data['signature_cust'] = $this->saveSig($request->signature_cust);
        }
        if ($request->has('template_data'))     $data['template_data']  = $request->template_data;

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $file) {
                $paths[] = $file->store('laporan', 'public');
            }
            $data['photos'] = $paths;
        }

        $report = Report::create($data);

        if ($hasCustSig) {
            Task::where('id', $data['task_id'])->update(['status' => 'completed']);
            $report->load(['task.customer', 'teknisi']);
            User::role(['admin', 'sales'])->get()
                ->each(fn($u) => $u->notify(new TaskCompletedNotification($report)));
            return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dikirim.');
        }

        return redirect()->route('laporan.index')
            ->with('info', 'Laporan tersimpan sebagai draft. Tambahkan tanda tangan customer untuk mengirim.');
    }

    public function show(Report $laporan)
    {
        $laporan->load(['task.customer', 'task.template', 'teknisi']);
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
            : Task::whereHas('assignees', fn($q) => $q->where('users.id', Auth::id()))->with('customer')->get();
        return view('laporan.edit', compact('laporan', 'tasks'));
    }

    public function update(Request $request, Report $laporan)
    {
        abort_if(
            Auth::id() !== $laporan->user_id && !Auth::user()->hasAnyRole(['admin', 'sales']),
            403
        );

        $request->validate([
            'description'    => 'required|string',
            'status'         => 'nullable|in:draft,submitted,approved',
            'photos'         => 'nullable|array|max:10',
            'photos.*'       => 'image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'signature_cust' => 'nullable|string',
        ]);

        $data = [
            'description' => $request->description,
            'status'      => $request->input('status', $laporan->status),
        ];

        if ($request->filled('signature_cust')) {
            $data['signature_cust'] = $this->saveSig($request->signature_cust);
            if ($laporan->status === 'draft') {
                $data['status'] = 'submitted';
            }
        }

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $file) {
                $paths[] = $file->store('laporan', 'public');
            }
            $data['photos'] = $paths;
        }

        $justSubmitted = $laporan->status !== 'submitted' && ($data['status'] === 'submitted');

        $laporan->update($data);

        if ($justSubmitted) {
            Task::where('id', $laporan->task_id)->update(['status' => 'completed']);
            $laporan->load(['task.customer', 'teknisi']);
            User::role(['admin', 'sales'])->get()
                ->each(fn($u) => $u->notify(new TaskCompletedNotification($laporan)));
        }

        $msg = $justSubmitted ? 'Laporan berhasil dikirim.' : 'Laporan berhasil diperbarui.';
        return redirect()->route('laporan.index')->with('success', $msg);
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
        $laporan->load(['task.customer', 'task.template', 'task.assignees', 'teknisi']);
        $pdf = Pdf::loadView('laporan.pdf', compact('laporan'))->setPaper('A4', 'portrait');
        $filename = 'laporan-' . $laporan->id . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    private function saveSig(string $base64): string
    {
        $imgData = base64_decode(
            preg_replace('/^data:image\/\w+;base64,/', '', $base64)
        );
        $hash = hash('sha256', $imgData);
        $path = 'signatures/' . $hash . '.png';
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $imgData);
        }
        return $path;
    }
}
