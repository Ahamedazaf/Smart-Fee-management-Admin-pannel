@extends('layouts.master')
@section('page_title', 'My Account')
@section('content')

<style>
    /* Input box style */
    .form-control {
        border-radius: 10px !important;
        border: 1px solid #d0d0d0 !important;
        box-shadow: none !important;
        padding: 10px 14px !important;
        font-size: 15px !important;
        transition: 0.2s ease-in-out;
    }

    .form-control:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 6px rgba(13, 110, 253, 0.2) !important;
    }

    /* Placeholder style */
    .form-control::placeholder {
        color: #b0b0b0 !important;
        font-weight: 300 !important;
    }

    /* Label style */
    .form-group label,
    .col-form-label {
        color: #7f7f7f !important;
        font-weight: 600 !important;
    }

    /* Card styling */
    .card {
        border-radius: 15px !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08) !important;
    }

    /* Button styling */
    .btn-danger {
        background-color: #dc3545 !important;
        border: none !important;
        padding: 10px 22px !important;
        font-weight: 600 !important;
        border-radius: 10px !important;
        transition: 0.3s;
    }

    .btn-danger:hover {
        background-color: #bb2d3b !important;
    }

    /* Tab styling */
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        color: #555;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd !important;
        border-bottom-color: #0d6efd !important;
        background: transparent !important;
    }
</style>

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title"><i class="bi bi-person-circle me-2"></i> My Account</h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight mb-4">
            <li class="nav-item">
                <a href="#change-pass" class="nav-link active" data-toggle="tab">
                    <i class="bi bi-lock-fill me-1"></i> Change Password
                </a>
            </li>
            @if(Qs::userIsPTA())
            <li class="nav-item">
                <a href="#edit-profile" class="nav-link" data-toggle="tab">
                    <i class="bi bi-person-lines-fill me-1"></i> Manage Profile
                </a>
            </li>
            @endif
        </ul>

        <div class="tab-content">
            <!-- Change Password -->
            <div class="tab-pane fade show active" id="change-pass">
                <div class="row">
                    <div class="col-md-8">
                        <form method="post" action="{{ route('my_account.change_pass') }}">
                            @csrf @method('put')

                            <div class="form-group row mb-3">
                                <label for="current_password" class="col-lg-4 col-form-label">Current Password <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input id="current_password" name="current_password" required type="password"
                                        class="form-control" placeholder="Enter current password">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="password" class="col-lg-4 col-form-label">New Password <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input id="password" name="password" required type="password" class="form-control"
                                        placeholder="Enter new password">
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="password_confirmation" class="col-lg-4 col-form-label">Confirm Password
                                    <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input id="password_confirmation" name="password_confirmation" required
                                        type="password" class="form-control" placeholder="Confirm new password">
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-send-fill me-1"></i> Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Profile -->
            @if(Qs::userIsPTA())
            <div class="tab-pane fade" id="edit-profile">
                <div class="row">
                    <div class="col-md-8">
                        <form enctype="multipart/form-data" method="post" action="{{ route('my_account.update') }}">
                            @csrf @method('put')

                            <div class="form-group row mb-3">
                                <label for="name" class="col-lg-4 col-form-label">Name</label>
                                <div class="col-lg-8">
                                    <input disabled id="name" class="form-control" type="text" value="{{ $my->name }}">
                                </div>
                            </div>

                            @if($my->username)
                            <div class="form-group row mb-3">
                                <label for="username" class="col-lg-4 col-form-label">Username</label>
                                <div class="col-lg-8">
                                    <input disabled id="username" class="form-control" type="text"
                                        value="{{ $my->username }}">
                                </div>
                            </div>
                            @else
                            <div class="form-group row mb-3">
                                <label for="username" class="col-lg-4 col-form-label">Username</label>
                                <div class="col-lg-8">
                                    <input id="username" name="username" type="text" class="form-control"
                                        placeholder="Enter username">
                                </div>
                            </div>
                            @endif

                            <div class="form-group row mb-3">
                                <label for="email" class="col-lg-4 col-form-label">Email</label>
                                <div class="col-lg-8">
                                    <input id="email" value="{{ $my->email }}" name="email" type="email"
                                        class="form-control" placeholder="Enter email address">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="phone" class="col-lg-4 col-form-label">Phone</label>
                                <div class="col-lg-8">
                                    <input id="phone" value="{{ $my->phone }}" name="phone" type="text"
                                        class="form-control" placeholder="Enter phone number">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="phone2" class="col-lg-4 col-form-label">Telephone</label>
                                <div class="col-lg-8">
                                    <input id="phone2" value="{{ $my->phone2 }}" name="phone2" type="text"
                                        class="form-control" placeholder="Enter telephone number">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="address" class="col-lg-4 col-form-label">Address</label>
                                <div class="col-lg-8">
                                    <input id="address" value="{{ $my->address }}" name="address" type="text"
                                        class="form-control" placeholder="Enter your address">
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="photo" class="col-lg-4 col-form-label">Change Photo</label>
                                <div class="col-lg-8">
                                    <input accept="image/*" type="file" name="photo" class="form-control">
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-upload me-1"></i> Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection