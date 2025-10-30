@extends('layouts.master')
@section('page_title', 'Student Full History')
@section('content')

<div class="container-fluid mt-4">

    <!--  Student Profile Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="icon-user mr-2"></i> {{ $student->name }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <div class="border rounded-3 p-3 h-100 bg-light">
                        <strong>Email:</strong><br>
                        <span class="text-muted">{{ $student->email ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="border rounded-3 p-3 h-100 bg-light">
                        <strong>Phone:</strong><br>
                        <span class="text-muted">{{ $student->phone ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="border rounded-3 p-3 h-100 bg-light">
                        <strong>Class:</strong><br>
                        <span class="text-muted">{{ $className }}</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="border rounded-3 p-3 h-100 bg-light">
                        <strong>Duration:</strong><br>
                        <span class="text-muted">{{ $classDuration }} months</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  Summary Analytics -->
    @php
        $totalPaid = $payments->sum('amt_paid');
        $totalBalance = $payments->sum('balance');
        $totalPayments = $payments->count();
    @endphp

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Amount Paid</h6>
                    <h3 class="text-success fw-bold">{{ number_format($totalPaid, 2) }} LKR</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Pending Balance</h6>
                    <h3 class="text-danger fw-bold">{{ number_format($totalBalance, 2) }} LKR</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Payments</h6>
                    <h3 class="text-primary fw-bold">{{ $totalPayments }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!--  Payment History Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="icon-coin-dollar mr-2"></i> Payment History</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Months</th>
                        <th>Year</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $p->title }}</td>
                            <td class="text-success fw-bold text-end">{{ number_format($p->amt_paid, 2) }}</td>
                            <td class="text-danger text-end">{{ number_format($p->balance, 2) }}</td>
                            <td>{{ $p->paid_months }}</td>
                            <td class="text-center">{{ $p->year }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No payment records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
