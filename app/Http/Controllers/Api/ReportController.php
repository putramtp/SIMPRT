<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Report::with(['task:id,title,customer_id', 'task.customer:id,name'])->latest();

        if ($user->hasRole('teknisi')) {
            $query->where('user_id', $user->id);
        }

        $reports = $query->paginate(20)->through(function ($r) {
            return [
                'id'            => $r->id,
                'task_title'    => $r->task?->title,
                'customer_name' => $r->task?->customer?->name,
                'status'        => $r->status,
                'created_at'    => $r->created_at->toDateTimeString(),
                'photo_url'     => !empty($r->photos) ? asset('storage/' . $r->photos[0]) : null,
            ];
        });

        return response()->json($reports);
    }

    public function show(Request $request, Report $report)
    {
        $user = $request->user();

        if ($user->hasRole('teknisi') && $report->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $report->load(['task.customer', 'teknisi:id,name']);

        return response()->json([
            'id'             => $report->id,
            'description'    => $report->description,
            'status'         => $report->status,
            'photo_url'          => !empty($report->photos) ? asset('storage/' . $report->photos[0]) : null,
            'signature_tech_url' => $report->teknisi?->signature ? asset('storage/' . $report->teknisi->signature) : null,
            'signature_cust_url' => $report->signature_cust ? asset('storage/' . $report->signature_cust) : null,
            'task'           => [
                'id'       => $report->task->id,
                'title'    => $report->task->title,
                'customer' => $report->task->customer?->name,
            ],
            'teknisi'    => $report->teknisi?->name,
            'created_at' => $report->created_at->toDateTimeString(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'task_id'        => 'required|exists:tasks,id',
            'description'    => 'required|string',
            'photo_base64'   => 'nullable|string',
            'signature_tech' => 'nullable|string',
            'signature_cust' => 'nullable|string',
        ]);

        $task = Task::findOrFail($request->task_id);
        if (!$task->assignees()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Tugas ini bukan milik Anda.'], 403);
        }

        $data = [
            'task_id'        => $request->task_id,
            'user_id'        => $user->id,
            'description'    => $request->description,
            'status'         => 'submitted',
            'signature_tech' => $request->signature_tech ?: null,
            'signature_cust' => $request->signature_cust ?: null,
        ];

        if ($request->filled('photo_base64')) {
            $imgData  = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $request->photo_base64));
            $filename = 'laporan/' . uniqid('rpt_', true) . '.jpg';
            Storage::disk('public')->put($filename, $imgData);
            $data['photos'] = [$filename];
        }

        if ($request->filled('signature_cust')) {
            $imgData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $request->signature_cust));
            $hash    = hash('sha256', $imgData);
            $path    = 'signatures/' . $hash . '.png';
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $imgData);
            }
            $data['signature_cust'] = $path;
        }

        $report = Report::create($data);

        return response()->json([
            'message' => 'Laporan berhasil dikirim.',
            'id'      => $report->id,
        ], 201);
    }
}
