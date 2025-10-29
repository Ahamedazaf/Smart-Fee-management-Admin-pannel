@extends('layouts.master')
@section('page_title', 'Edit Student')
@section('content')
<style>
    .form-control::placeholder {
        color: #b0b0b0 !important;
        font-weight: 200 !important;
        opacity: 1 !important;
    }

    .form-group label {
        color: #6c757d;
        font-weight: 600;
    }

    .card {
        border-radius: 1rem;
    }

    .form-title {
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }

    @media (max-width: 576px) {
        .form-group {
            margin-bottom: 1rem;
        }
    }
</style>

<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm-12">
                <div class="page-sub-header">
                    <h3 class="page-title"><i class="bi bi-person-lines-fill me-2"></i>Edit Student</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                        <li class="breadcrumb-item active">Edit Student</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card comman-shadow">
                <div class="card-body">

                    <form method="POST" enctype="multipart/form-data"
                        action="{{ route('students.update', Qs::hash($sr->id)) }}" class="ajax-update">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12">
                                <h5 class="form-title">Personal Information</h5>
                            </div>

                            <input type="hidden" name="my_parent_id" value="{{ $sr->my_parent_id ?? '' }}">

                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Full Name <span class="login-danger">*</span></label>
                                    <input type="text" name="name" value="{{ $sr->user->name }}" required
                                        class="form-control" placeholder="Enter full name">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Address <span class="login-danger">*</span></label>
                                    <input type="text" name="address" value="{{ $sr->user->address }}" required
                                        class="form-control" placeholder="Enter address">
                                </div>
                            </div>




                            <div class="col-md-4">
                                <div class="form-group local-forms">
                                    <label>Gender <span class="login-danger">*</span></label>
                                    <select name="gender" class="form-control select-search" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ $sr->user->gender == 'Male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="Female" {{ $sr->user->gender == 'Female' ? 'selected' : '' }}>
                                            Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group local-forms">
                                    <label>Phone</label>
                                    <input type="text" name="phone" value="{{ $sr->user->phone }}" class="form-control"
                                        placeholder="Enter phone number">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group local-forms">
                                    <label>Date of Birth</label>
                                    <input type="text" name="dob" value="{{ $sr->user->dob }}"
                                        class="form-control date-pick" placeholder="Select date">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Class <span class="login-danger">*</span></label>
                                    <select name="my_class_id" id="my_class_id" onchange="getClassSections(this.value)"
                                        class="form-control select-search" required>
                                        <option value="">Select Class</option>
                                        @foreach ($my_classes as $c)
                                        <option value="{{ $c->id }}" {{ $sr->my_class_id == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Upload Passport Photo</label>
                                    <input type="file" name="photo" accept="image/*" class="form-control">
                                    <small class="text-muted">Accepted formats: JPG, PNG. Max size: 2MB.</small>
                                </div>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-save me-1"></i> Update Student
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ajax-update').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = this.action;
            const btn = this.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {

                Swal.fire({
                    title: 'Success!',
                    text: data.message || 'Student information updated successfully!',
                    icon: 'success',
                    showConfirmButton: false, 
                    timer: 2000,             
                    timerProgressBar: true    
                });

                setTimeout(() => {
                    window.location.reload();
                }, 2100);
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong while updating the student.',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            })
            .finally(() => {
                if (btn) btn.disabled = false;
            });
        });
    });
});
</script>


@endsection