@extends('layouts.master')
@section('page_title', 'Student Information')
@section('content')

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title"><i class="icon-users me-2"></i> Manage Students</h5>
        {!! Qs::getPanelOptions() !!}
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
                    @foreach ($my_classes as $c)
                    <li><a class="dropdown-item" data-bs-toggle="tab" href="#c{{ $c->id }}">{{ $c->name }}</a></li>
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
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Admission No</th>
                                <th>Class</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ $student->user->photo ?? asset('images/default-avatar.png') }}"
                                        class="rounded-circle" width="40" height="40" alt="Photo">
                                </td>
                                <td>{{ $student->user->name ?? '-' }}</td>
                                <td>{{ $student->adm_no ?? '-' }}</td>
                                <td>{{ optional($student->my_class)->name ?? '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('students.edit', Qs::hash($student->id)) }}">
                                                    <i class="icon-pencil"></i> Edit
                                                </a>
                                            </li>

                                            @if(Qs::userIsSuperAdmin())
                                            <li>
                                                <a href="#" class="dropdown-item text-danger"
                                                    onclick="event.preventDefault(); confirmDelete('{{ Qs::hash($student->id) }}');">
                                                    <i class="icon-trash"></i> Delete
                                                </a>
                                                <form id="item-delete-{{ Qs::hash($student->id) }}"
                                                    action="{{ route('students.destroy', Qs::hash($student->id)) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No students found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Class-wise Tabs --}}
            @foreach ($my_classes as $mc)
            <div class="tab-pane fade" id="c{{ $mc->id }}">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped datatable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Admission No</th>
                                <th>Section</th>
                                <th>Grad Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students->where('my_class_id', $mc->id) as $s)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><img src="{{ $s->user->photo ?? asset('images/default-avatar.png') }}"
                                        class="rounded-circle" width="40" height="40" alt="Photo"></td>
                                <td>{{ $s->user->name }}</td>
                                <td>{{ $s->adm_no }}</td>
                                <td>{{ $s->my_class->name.' '.$s->section->name }}</td>
                                <td>{{ $s->grad_date ?? '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('students.show', Qs::hash($s->id)) }}">
                                                    <i class="icon-eye"></i> View Profile
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('students.edit', Qs::hash($s->id)) }}">
                                                    <i class="icon-pencil"></i> Edit
                                                </a>
                                            </li>
                                            @if(Qs::userIsSuperAdmin())
                                            <li>
                                                <a href="#" class="dropdown-item text-danger"
                                                    onclick="event.preventDefault(); confirmDelete('{{ Qs::hash($s->id) }}');">
                                                    <i class="icon-trash"></i> Delete
                                                </a>
                                                <form id="item-delete-{{ Qs::hash($s->id) }}"
                                                    action="{{ route('students.destroy', Qs::hash($s->id)) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                            @endif
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

{{-- Confirm Delete --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    window.confirmDelete = function(id) {
        if (confirm('Are you sure you want to delete this student?')) {
            const form = document.getElementById('item-delete-' + id);
            if (form) form.submit();
        }
    }
});
</script>



<script>
    document.addEventListener('DOMContentLoaded', function() {
    window.confirmDelete = function(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action will permanently delete the student record!",
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
    }
});




</script>



@endsection
