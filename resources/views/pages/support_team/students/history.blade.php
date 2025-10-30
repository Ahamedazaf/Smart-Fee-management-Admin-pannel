@extends('layouts.master')
@section('page_title', 'Student Full History')
@section('content')

<div class="container-fluid mt-4">

    <!-- Student Profile Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="icon-user me-2"></i> {{ $student->name }}
            </h4>
        </div>
        {{-- <div class="card-body">
            <div class="row g-3">
                @foreach ([
                    'Email' => $student->email ?? 'N/A',
                    'Phone' => $student->phone ?? 'N/A',
                    'Class' => $className,
                    'Duration' => $classDuration . ' months'
                ] as $label => $value)
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded-3 p-3 h-100 bg-white shadow-sm">
                            <strong>{{ $label }}:</strong><br>
                            <span class="text-muted">{{ $value }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div> --}}
    </div>

    <!-- Summary Analytics -->
    @php
        $totalPaid = $payments->sum('amt_paid');
        $totalBalance = $payments->sum('balance');
        $totalPayments = $payments->count();
    @endphp

    <div class="row mb-4">
        @foreach ([
            'Total Amount Paid' => ['value' => number_format($totalPaid, 2) . ' LKR', 'color' => 'success'],
            'Pending Balance' => ['value' => number_format($totalBalance, 2) . ' LKR', 'color' => 'danger'],
            // 'Total Payments' => ['value' => $totalPayments, 'color' => 'primary'],
        ] as $title => $data)
            <div class="col-md-4">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">{{ $title }}</h6>
                        <h3 class="text-{{ $data['color'] }} fw-bold">{{ $data['value'] }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Payment History Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="icon-coin-dollar me-2 text-info"></i> Payment History</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle mb-0 bg-white">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width:5%">NO</th>
                        <th style="width:20%">Title</th>
                        <th style="width:15%">Amount Paid</th>
                        <th style="width:15%">Balance</th>
                        <th style="width:10%">Months</th>
                        <th style="width:10%">Year</th>
                        <th style="width:15%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $p->title }}</td>
                            <td class="text-success fw-bold text-end">{{ number_format($p->amt_paid, 2) }} LKR</td>
                            <td class="text-danger fw-bold text-end">{{ number_format($p->balance, 2) }} LKR</td>
                            <td class="text-center">{{ $p->paid_months }}</td>
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
