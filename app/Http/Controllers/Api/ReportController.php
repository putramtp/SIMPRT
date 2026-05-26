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
                'photo_url'     => $r->photo ? asset('storage/' . $r->photo) : null,
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
            'photo_url'      => $report->photo ? asset('storage/' . $report->photo) : null,
            'signature_tech' => $report->signature_tech,
            'signature_cust' => $report->signature_cust,
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
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo_base64);
            $imageData = base64_decode($imageData);
            $filename  = 'laporan/' . uniqid('rpt_', true) . '.jpg';
            Storage::disk('public')->put($filename, $imageData);
            $data['photo'] = $filename;
        }

        $report = Report::create($data);

        return response()->json([
            'message' => 'Laporan berhasil dikirim.',
            'id'      => $report->id,
        ], 201);
    }
}
