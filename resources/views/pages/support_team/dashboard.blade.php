@extends('layouts.master')
@section('page_title', 'Analytics Dashboard')
@section('content')

<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Analytics <span class="text-muted fw-normal">Dashboard</span></h4>

    @if (Qs::userIsTeamSA())
    <div class="row g-4">

        <style>
            /* ======= CLEAN MINIMAL DASHBOARD ======= */
            .stat-card {
                background: #fff;
                border: 1px solid #e9ecef;
                border-radius: 14px;
                padding: 1.5rem;
                transition: all 0.25s ease;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
            }

            .stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            }

            .stat-title {
                color: #6c757d;
                font-size: 0.9rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .stat-value {
                font-size: 1.8rem;
                font-weight: 700;
                color: #212529;
            }

            .icon-circle {
                width: 45px;
                height: 45px;
                background-color: #f5f7fa;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.3rem;
                color: #007bff;
            }

            .stat-change {
                font-size: 0.85rem;
                font-weight: 500;
            }

            .text-success {
                color: #28a745 !important;
            }

            .text-danger {
                color: #dc3545 !important;
            }

            .chart-card {
                background: #fff;
                border: 1px solid #e9ecef;
                border-radius: 14px;
                padding: 1.5rem;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
            }
        </style>

        <!-- Students -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-title">Students</div>
                    <div class="stat-value">{{ $users->where('user_type', 'student')->count() }}</div>

                </div>
                <div class="icon-circle">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>

        <!-- Total Payments -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-title">Total Payments</div>
                    <div class="stat-value">LKR {{ number_format($total_amount, 2) }}</div>

                </div>
                <div class="icon-circle">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>

        <!-- Total Paid -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-title">Total Paid</div>
                    <div class="stat-value">LKR {{ number_format($total_paid_till_now, 2) }}</div>

                </div>
                <div class="icon-circle">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Pending Amount -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-title">Pending</div>
                    <div class="stat-value">LKR {{ number_format($pending_amount, 2) }}</div>

                </div>
                <div class="icon-circle">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
            </div>
        </div>

        <!-- Total Classes -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-title">Classes</div>
                    <div class="stat-value">{{ $total_classes }}</div>

                </div>
                <div class="icon-circle">
                    <i class="bi bi-journal-text"></i>
                </div>
            </div>
        </div>

        <!-- Payment Records -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-title">Payment Records</div>
                    <div class="stat-value">{{ $total_payment_records }}</div>

                </div>
                <div class="icon-circle">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
        </div>

    </div>
    @endif

    <!-- Charts -->
    <div class="row mt-5">
        <div class="col-md-8">
            <div class="chart-card">
                <h6 class="text-muted fw-bold mb-3">Monthly Payment Trend</h6>
                <canvas id="paymentsChart" height="120"></canvas>
            </div>
        </div>

        <div class="col-md-4">
            <div class="chart-card">
                <h6 class="text-muted fw-bold mb-3">Payment Overview</h6>
                <canvas id="overviewChart" height="120"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ===== Line Chart =====
    const paymentsCtx = document.getElementById('paymentsChart').getContext('2d');
    new Chart(paymentsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthly_payments->keys()) !!},
            datasets: [{
                label: 'Total Paid',
                data: {!! json_encode($monthly_payments->values()) !!},
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.08)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true },
                x: { ticks: { color: '#6c757d' } }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'LKR ' + context.parsed.y.toLocaleString();
                        }
                    }
                },
                legend: { position: 'bottom' }
            }
        }
    });

    // ===== Doughnut Chart =====
    const overviewCtx = document.getElementById('overviewChart').getContext('2d');
    new Chart(overviewCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Pending'],
            datasets: [{
                data: [{{ $total_paid_till_now }}, {{ $pending_amount }}],
                backgroundColor: ['#007bff', '#e9ecef'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'LKR ' + context.parsed.toLocaleString();
                        }
                    }
                },
                legend: { position: 'bottom' }
            }
        }
    });
</script>

@endsection