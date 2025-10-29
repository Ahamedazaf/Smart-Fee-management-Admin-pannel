@extends('layouts.master')
@section('page_title', 'Add Student')
@section('content')

<style>
    .form-control::placeholder {
        color: #b0b0b0 !important;
        font-weight: 200 !important;
        opacity: 1 !important;
    }

    .form-group label {
        color: #7f7f7f !important;
        font-weight: 600;
    }
</style>

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm-12">
                <div class="page-sub-header">
                    <h3 class="page-title"><i class="bi bi-person-plus me-2"></i> Add New Student</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                        <li class="breadcrumb-item active">Add Student</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card comman-shadow">
                <div class="card-body">

                    {{-- ✅ Success Message --}}
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form id="ajax-reg" method="POST" enctype="multipart/form-data"
                        action="{{ route('students.store') }}" class="ajax-store">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <h5 class="form-title student-info">Student Information</h5>
                            </div>

                            <!-- Full Name -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Full Name <span class="login-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name"
                                        class="form-control" required>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Address</label>
                                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Address"
                                        class="form-control">
                                </div>
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Gender <span class="login-danger">*</span></label>
                                    <select name="gender" class="form-control select-search" required>
                                        <option value="">Choose...</option>
                                        <option value="Male" {{ old('gender')=='Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender')=='Female' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                        placeholder="Phone Number" class="form-control">
                                </div>
                            </div>

                            <!-- Date of Birth -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Date of Birth</label>
                                    <input type="text" name="dob" value="{{ old('dob') }}"
                                        class="form-control date-pick" placeholder="Select Date...">
                                </div>
                            </div>

                            <!-- Class -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Class <span class="login-danger">*</span></label>
                                    <select name="my_class_id" id="my_class_id" class="form-control select-search"
                                        required onchange="getClassSections(this.value)">
                                        <option value="">Choose...</option>
                                        @foreach($my_classes as $c)
                                        <option value="{{ $c->id }}" {{ old('my_class_id')==$c->id ? 'selected' : ''
                                            }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Admission Number -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Admission Number</label>
                                    <input type="text" name="adm_no" value="{{ old('adm_no') }}"
                                        placeholder="Admission Number" class="form-control">
                                </div>
                            </div>

                            <!-- Upload Photo -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Upload Passport Photo</label>
                                    <input type="file" name="photo" class="form-control" accept="image/*">
                                    <small class="text-muted">Accepted Images: jpeg, png. Max file size 2Mb</small>
                                </div>
                            </div>

                            <!-- Advanced Section -->
                            <div class="col-12 mt-4">
                                <button type="button" class="btn btn-outline-primary w-100 py-2 mb-3"
                                    data-bs-toggle="collapse" data-bs-target="#advancedFields">
                                    <i class="bi bi-gear me-1"></i> Show Advanced Fields
                                </button>

                                <div id="advancedFields" class="collapse">
                                    <div class="row">
                                        <!-- Email -->
                                        <div class="col-md-6">
                                            <div class="form-group local-forms">
                                                <label>Email</label>
                                                <input type="email" name="email" value="{{ old('email') }}"
                                                    placeholder="Email" class="form-control">
                                            </div>
                                        </div>

                                        <!-- Section -->
                                        <div class="col-md-6">
                                            <div class="form-group local-forms">
                                                <label>Section</label>
                                                <select name="section_id" id="section_id" class="form-control select">
                                                    <option value="{{ old('section_id') }}">{{ old('section_id') ?
                                                        'Selected' : '' }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Parent -->
                                        <div class="col-md-6">
                                            <div class="form-group local-forms">
                                                <label>Parent</label>
                                                <select name="my_parent_id" id="my_parent_id"
                                                    class="form-control select-search">
                                                    <option value="">Choose...</option>
                                                    @foreach($parents as $p)
                                                    <option value="{{ Qs::hash($p->id) }}" {{
                                                        old('my_parent_id')==Qs::hash($p->id) ? 'selected' : '' }}>{{
                                                        $p->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Year Admitted -->
                                        <div class="col-md-6">
                                            <div class="form-group local-forms">
                                                <label>Year Admitted</label>
                                                <select name="year_admitted" id="year_admitted"
                                                    class="form-control select-search">
                                                    <option value="">Choose...</option>
                                                    @for($y = date('Y', strtotime('-10 years')); $y <= date('Y'); $y++)
                                                        <option value="{{ $y }}" {{ old('year_admitted')==$y
                                                        ? 'selected' : '' }}>{{ $y }}</option>
                                                        @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 mt-4 text-center">
                                <button type="submit" class="btn btn-primary px-5 py-2">Submit Student</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#ajax-reg').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let form = $(this);

    $.ajax({
        url: form.attr('action'),
        method: form.attr('method'),
        data: formData,
        contentType: false,
        processData: false,

        success: function(response) {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: response.message,
                timer: 2000,
                showConfirmButton: false
            });

            // Redirect if provided
            if(response.redirect){
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 2000);
            }
        },
        error: function(xhr) {
            Swal.close();
            let message = xhr.responseJSON?.message || 'Something went wrong!';
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        }
    });
});

</script>

@endsection