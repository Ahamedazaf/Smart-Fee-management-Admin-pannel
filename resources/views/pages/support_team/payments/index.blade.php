@extends('layouts.master')
@section('page_title', 'Manage Payments')
@section('content')

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title"><i class="icon-cash2 me-2"></i> Select Year</h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    {{-- Laravel Success Alert --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert" id="successAlert">
        <i class="icon-checkmark-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        setTimeout(() => {
                const alert = document.getElementById('successAlert');
                if (alert) $(alert).fadeOut('slow');
            }, 3000);
    </script>
    @endif

    <!--<div class="card-body">-->
    <!--    <form method="post" action="{{ route('payments.select_year') }}">-->
    <!--        @csrf-->
    <!--        <div class="row justify-content-center">-->
    <!--            <div class="col-md-6">-->
    <!--                <div class="row g-2">-->
    <!--                    <div class="col-9">-->
    <!--                        <label for="year" class="form-label fw-bold">Select Year <span-->
    <!--                                class="text-danger">*</span></label>-->
    <!--                        <select name="year" id="year" class="form-select select" required>-->
    <!--                            <option {{ $selected && $year==Qs::getCurrentSession() ? 'selected' : '' }}-->
    <!--                                value="{{ Qs::getCurrentSession() }}">{{ Qs::getCurrentSession() }}</option>-->
    <!--                            @foreach ($years as $yr)-->
    <!--                            <option {{ $selected && $year==$yr->year ? 'selected' : '' }}-->
    <!--                                value="{{ $yr->year }}">{{ $yr->year }}</option>-->
    <!--                            @endforeach-->
    <!--                        </select>-->
    <!--                    </div>-->
    <!--                    <div class="col-3 d-flex align-items-end">-->
    <!--                        <button type="submit" class="btn btn-primary w-100">Submit <i-->
    <!--                                class="icon-paperplane ms-1"></i></button>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </form>-->
    <!--</div>-->
</div>

@if ($selected)
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title">Manage Payments for {{ $year }} Session</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#all-payments">All Classes</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                    aria-expanded="false">Class Payments</a>
                <ul class="dropdown-menu dropdown-menu-end">
                    @foreach ($my_classes as $mc)
                    <li><a class="dropdown-item" data-bs-toggle="tab" href="#pc-{{ $mc->id }}">{{ $mc->name }}</a></li>
                    @endforeach
                </ul>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content">
            {{-- All Payments --}}
            <div class="tab-pane fade show active" id="all-payments">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped datatable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Yearly Amount</th>
                                <th>Additional Items</th>
                                <th>Additional Amount</th>
                                <th>Ref No</th>
                                <th>Class</th>
                                <th>Fee Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->title }}</td>
                                <td>{{ number_format($p->amount, 2) }}</td>
                                <td>
                                    @php $items = $p->additional_items ? json_decode($p->additional_items,true) : [];
                                    @endphp
                                    @if ($items)
                                    <ul class="ps-3 mb-0">
                                        @foreach ($items as $item)
                                        <li>{{ $item['name'] }}: LKR {{ number_format($item['amount'], 2) }}</li>
                                        @endforeach
                                    </ul>
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>{{ number_format($p->additional_amount, 2) }}</td>
                                <td>{{ $p->ref_no }}</td>
                                <td>{{ $p->my_class_id ? $p->my_class->name : 'N/A' }}</td>
                                <td>{{ $p->description }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item"
                                                    href="{{ route('payments.edit', $p->id) }}">Edit</a></li>
                                            <li>
                                                <form id="item-delete-{{ $p->id }}"
                                                    action="{{ route('payments.destroy', $p->id) }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <a class="dropdown-item text-danger" href="#"
                                                    onclick="confirmDelete({{ $p->id }});">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Class-wise payments --}}
            @foreach ($my_classes as $mc)
            <div class="tab-pane fade" id="pc-{{ $mc->id }}">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped datatable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Yearly Amount</th>
                                <th>Additional Items</th>
                                <th>Additional Amount</th>
                                <th>Ref No</th>
                                <th>Class</th>
                                <th>Fee Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments->where('my_class_id', $mc->id) as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->title }}</td>
                                <td>{{ number_format($p->amount, 2) }}</td>
                                <td>
                                    @php $items = $p->additional_items ? json_decode($p->additional_items,true) : [];
                                    @endphp
                                    @if ($items)
                                    <ul class="ps-3 mb-0">
                                        @foreach ($items as $item)
                                        <li>{{ $item['name'] }}: LKR {{ number_format($item['amount'], 2) }}</li>
                                        @endforeach
                                    </ul>
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>{{ number_format($p->additional_amount, 2) }}</td>
                                <td>{{ $p->ref_no }}</td>
                                <td>{{ $p->my_class_id ? $p->my_class->name : 'N/A' }}</td>
                                <td>{{ $p->description }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item"
                                                    href="{{ route('payments.edit', $p->id) }}">Edit</a></li>
                                            <li>
                                                <form id="item-delete-{{ $p->id }}"
                                                    action="{{ route('payments.destroy', $p->id) }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <a class="dropdown-item text-danger" href="#"
                                                    onclick="confirmDelete({{ $p->id }});">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
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
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    window.confirmDelete = function(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This payment record will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('item-delete-' + id);
                if (form) {
                    form.submit(); 
                }
            }
        });
    };
});
</script>

@endsection