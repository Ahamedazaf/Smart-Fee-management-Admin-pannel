@extends('layouts.master')
@section('page_title', 'Fees Summary')
@section('content')


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

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title"><i class="icon-cash3 me-2"></i> Fees Summary</h5>
            {!! Qs::getPanelOptions() !!}
        </div>


        {{-- Dashboard-Style Summary Cards --}}
        <div class="row mb-4 g-3 p-4">

            <!-- Total Paid This Month -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div
                    class="card border-0 shadow-sm rounded-3 py-3 px-4 d-flex flex-row align-items-center justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small text-muted fw-semibold">Total Paid This Month</div>
                        <div class="fs-5 fw-bold text-primary">LKR {{ number_format($current_month_paid, 2) }}</div>
                    </div>
                             <div class="icon-circle">

                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                </div>
            </div>

            <!-- Total Fee Demand -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div
                    class="card border-0 shadow-sm rounded-3 py-3 px-4 d-flex flex-row align-items-center justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small text-muted fw-semibold">Total Fee Demand</div>
                        <div class="fs-5 fw-bold text-warning">LKR {{ number_format($total_fee, 2) }}</div>
                    </div>
                                <div class="icon-circle">

                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Amount -->
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div
                    class="card border-0 shadow-sm rounded-3 py-3 px-4 d-flex flex-row align-items-center justify-content-between h-100">
                    <div>
                        <div class="text-uppercase small text-muted fw-semibold">Pending Amount</div>
                        <div class="fs-5 fw-bold text-danger">LKR {{ number_format($pending_amount, 2) }}</div>
                    </div>
                                   <div class="icon-circle">

                        <i class="bi bi-exclamation-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>




        <div class="card-body">

            {{-- Tabs --}}
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#all-students">All Students</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                        aria-expanded="false">
                        Select Class
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach (App\Models\MyClass::orderBy('name')->get() as $c)
                            <li><a class="dropdown-item" data-bs-toggle="tab"
                                    href="#c{{ $c->id }}">{{ $c->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
            </ul>

            <div class="tab-content">

                {{-- All Students --}}
                <div class="tab-pane fade show active" id="all-students">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                      <th>Class</th>
        <th>Class Fee (Rs)</th>

        <th>Paid To This Month (Rs)</th>
        <th>Total (Rs)</th>
        <th>Pending (Rs)</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $student->name }}</td>
                                           <td>{{ $student->class_name }}</td>
            <td>{{ number_format($student->class_fee, 2) }}</td>
            <td>{{ number_format($student->paid_this_month, 2) }}</td>
            <td>{{ number_format($student->total_paid, 2) }}</td>
            <td>{{ number_format($student->pending, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Class-wise Tabs --}}
                @foreach (App\Models\MyClass::orderBy('name')->get() as $c)
                    <div class="tab-pane fade" id="c{{ $c->id }}">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped datatable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Paid To This Month (Rs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students->where('my_class_id', $c->id) as $s)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $s->name }}</td>
                                            <td>{{ number_format($s->paid_this_month, 2) }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @push('scripts')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

        <script>
            $(document).ready(function() {
                $('.datatable').each(function() {
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable({
                            pageLength: 10,
                            ordering: true,
                            language: {
                                search: "_INPUT_",
                                searchPlaceholder: "Search students..."
                            }
                        });
                    }
                });
            });
        </script>

        {{-- // El --}}



        {{--  Buttons plugin --}}
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    @endpush

@endsection
