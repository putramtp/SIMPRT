<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    private function getCustomer()
    {
        $customerUser = Auth::guard('customer')->user();
        $customer = $customerUser->customer;

        if (!$customer) {
            abort(403, 'Akun Anda belum terhubung ke data customer. Hubungi administrator.');
        }

        return $customer;
    }

    public function index()
    {
        $customer = $this->getCustomer();

        $reports = $customer->report_access
            ? Report::with(['task', 'teknisi'])
                ->whereHas('task', fn($q) => $q->where('customer_id', $customer->id))
                ->latest()
                ->get()
            : collect();

        return view('customer.dashboard', compact('customer', 'reports'));
    }

    public function laporan()
    {
        $customer = $this->getCustomer();

        $reports = $customer->report_access
            ? Report::with(['task', 'teknisi'])
                ->whereHas('task', fn($q) => $q->where('customer_id', $customer->id))
                ->latest()
                ->get()
            : collect();

        return view('customer.laporan.index', compact('customer', 'reports'));
    }

    public function show(Report $laporan)
    {
        $customer = $this->getCustomer();

        // Report must belong to this customer, and the customer must have report access
        if ($laporan->task?->customer_id !== $customer->id || !$customer->report_access) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        $laporan->load(['task.customer', 'teknisi']);

        return view('customer.laporan.show', compact('laporan', 'customer'));
    }
}
