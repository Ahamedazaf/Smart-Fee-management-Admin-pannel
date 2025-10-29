@extends('layouts.master')
@section('page_title', 'Student Payments')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="icon-cash2 me-2"></i> Student Payments
                </h5>
                {!! Qs::getPanelOptions() !!}
            </div>
        </div>

        <div class="card-body">
            <!-- Filters Row -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="class_selector" class="form-label fw-bold">Class:</label>
                        <select id="class_selector" class="form-select select-search">
                            <option value="0">All Classes</option>
                            @foreach($my_classes as $class)
                            <option value="{{ $class->id }}" {{ (isset($my_class_id) && $my_class_id==$class->id) ?
                                'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="student_search" class="form-label fw-bold">Search Name:</label>
                        <input type="text" id="student_search" class="form-control" placeholder="Enter student name">
                    </div>
                </div>
            </div>

            <!-- Results Info -->
            <div class="alert alert-info mb-3">
                Showing <strong id="record_count">0</strong> student(s)
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="students_table" class="table table-striped table-hover border">
                    <thead class="table-light">
                        <tr>
                            <th>S/N</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Admission No</th>
                            <th>Class/Section</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="table_body">
                        <!-- Data populated by JS -->
                    </tbody>
                </table>
                <div id="empty_state" class="text-center py-5" style="display: none;">
                    <p class="text-muted">No students found</p>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function () {
    const STUDENT_URL = "{{ route('payments.fetch_students') }}";
    let allStudents = [];
    let currentClassFilter = 0;
    let currentSearchFilter = '';

    function loadStudents(classId = 0) {
        $.ajax({
            url: `${STUDENT_URL}?class_id=${classId}`,
            type: 'GET',
            dataType: 'json',
            success: function (students) {
                allStudents = students || [];
                renderStudents();
            },
            error: function () {
                Swal.fire({
                    title: 'Error',
                    text: 'Error fetching students.',
                    icon: 'error',
                    confirmButtonColor: '#0d6efd'
                });
            }
        });
    }

    function filterStudents() {
        return allStudents.filter(student =>
            student.name.toLowerCase().includes(currentSearchFilter.toLowerCase())
        );
    }

    function renderStudents() {
        const filtered = filterStudents();
        const tbody = $('#table_body');
        const emptyState = $('#empty_state');
        tbody.empty();

        if (filtered.length === 0) {
            tbody.closest('.table-responsive').find('table').hide();
            emptyState.show();
            $('#record_count').text('0');
            return;
        }

        tbody.closest('.table-responsive').find('table').show();
        emptyState.hide();

        filtered.forEach((student, index) => {
            tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <img src="${student.photo}" style="height:40px;width:40px;border-radius:50%"/>
                    </td>
                    <td>${student.name}</td>
                    <td>${student.adm_no}</td>
                    <td>${student.class} </td>
                    <td class="text-center">
                        <a href="{{ url('payments/invoice') }}/${student.user_id_hashed}" class="btn btn-sm btn-primary">Manage Payment</a>

                    </td>
                </tr>
            `);
        });

        $('#record_count').text(filtered.length);
    }

    // Initial load
    loadStudents(currentClassFilter);

    // Filter by class
    $('#class_selector').on('change', function () {
        currentClassFilter = $(this).val() || 0;
        loadStudents(currentClassFilter);
    });

    // Filter by search
    $('#student_search').on('keyup', function() {
        currentSearchFilter = this.value;
        renderStudents();
    });

    // Delete button
    $(document).on('click', '.delete-btn', function() {
        let studentId = $(this).data('id');
        Swal.fire({
            title: 'Confirm Delete',
            text: 'Are you sure you want to delete this record?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Student record deleted successfully.',
                    icon: 'success',
                    confirmButtonColor: '#0d6efd'
                });
            }
        });
    });
});



</script>


@endsection
