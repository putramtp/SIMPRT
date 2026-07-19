<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
                    $html = '<a href="' . route('customers.show', $c) . '" class="btn btn-sm btn-outline-secondary">Detail</a> ';
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
        $customer->load('portalUser');
        return view('customers.show', compact('customer'));
    }

    public function storePortalUser(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customer_users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        CustomerUser::create([
            'customer_id' => $customer->id,
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
        ]);

        return redirect()->route('customers.show', $customer)->with('success', 'Akses portal customer berhasil dibuat.');
    }

    public function resetPortalPassword(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $portalUser = $customer->portalUser;

        if (!$portalUser) {
            return redirect()->route('customers.show', $customer)->with('error', 'Akun portal tidak ditemukan.');
        }

        $portalUser->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('customers.show', $customer)->with('success', 'Password portal customer berhasil direset.');
    }

    public function reportAccess(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::withCount('tasks')->with('portalUser')->latest();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('portal_badge', function ($c) {
                    return $c->portalUser
                        ? '<span class="badge bg-success">Ada</span>'
                        : '<span class="badge bg-secondary">Belum ada</span>';
                })
                ->addColumn('access_toggle', function ($c) {
                    $checked = $c->report_access ? ' checked' : '';
                    return '<div class="form-check form-switch d-flex justify-content-center mb-0">'
                        . '<input class="form-check-input access-switch" type="checkbox" role="switch"'
                        . ' data-id="' . $c->id . '"' . $checked . '>'
                        . '</div>';
                })
                ->rawColumns(['portal_badge', 'access_toggle'])
                ->make(true);
        }

        $total   = Customer::count();
        $granted = Customer::where('report_access', true)->count();

        return view('customers.report-access', compact('total', 'granted'));
    }

    public function toggleReportAccess(Request $request, Customer $customer)
    {
        $customer->update(['report_access' => $request->boolean('access')]);

        return response()->json([
            'success' => true,
            'access'  => $customer->report_access,
            'granted' => Customer::where('report_access', true)->count(),
        ]);
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
