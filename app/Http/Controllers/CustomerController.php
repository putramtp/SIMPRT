<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::latest();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($c) {
                    $html = '';
                    if (auth()->user()->can('view customer reports')) {
                        $html .= '<a href="' . route('customers.laporan', $c) . '" class="btn btn-sm btn-outline-info">Laporan</a> ';
                    }
                    if (auth()->user()->can('edit customers')) {
                        $html .= '<a href="' . route('customers.edit', $c) . '" class="btn btn-sm btn-outline-primary">Edit</a> ';
                    }
                    if (auth()->user()->can('delete customers')) {
                        $html .= '<form action="' . route('customers.destroy', $c) . '" method="POST" class="d-inline"'
                            . ' onsubmit="return confirm(\'Hapus customer ini?\')">'
                            . csrf_field() . method_field('DELETE')
                            . '<button class="btn btn-sm btn-outline-danger">Hapus</button></form>';
                    }
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('customers.index');
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }

    public function laporan(Customer $customer, Request $request)
    {
        if ($request->ajax()) {
            $query = Report::with(['task', 'teknisi'])
                ->whereHas('task', fn($q) => $q->where('customer_id', $customer->id))
                ->latest();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('task_title', fn($r) => $r->task?->title ?? '-')
                ->addColumn('teknisi_name', fn($r) => $r->teknisi?->name ?? '-')
                ->addColumn('status_badge', fn($r) => '<span class="badge bg-info">' . ucfirst($r->status) . '</span>')
                ->addColumn('tanggal', fn($r) => $r->created_at->format('d/m/Y H:i'))
                ->rawColumns(['status_badge'])
                ->make(true);
        }

        $reports = Report::with(['task', 'teknisi'])
            ->whereHas('task', fn($q) => $q->where('customer_id', $customer->id))
            ->latest()
            ->get();

        $signedUrl = URL::temporarySignedRoute(
            'customers.public-laporan',
            now()->addDays(30),
            ['customer' => $customer->id]
        );

        return view('laporan.customer', compact('customer', 'reports', 'signedUrl'));
    }

    public function publicLaporan(Request $request, Customer $customer)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link tidak valid atau sudah kadaluarsa.');
        }

        $reports = Report::with(['task', 'teknisi'])
            ->whereHas('task', fn($q) => $q->where('customer_id', $customer->id))
            ->latest()
            ->get();

        return view('laporan.customer_public', compact('customer', 'reports'));
    }
}
