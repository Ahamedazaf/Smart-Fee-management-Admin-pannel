@extends('layouts.master')
@section('page_title', 'Manage Classes')
@section('content')

@php use Illuminate\Support\Str; @endphp

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title"><i class="icon-home2 me-2"></i> Manage Classes</h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="classTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#all-classes">All Classes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#new-class"><i class="icon-plus2"></i> Create New
                    Class</a>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content">
            {{-- All Classes --}}
            <div class="tab-pane fade show active" id="all-classes">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped datatable align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Class Name</th>
                                <th>Monthly Duration</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($my_classes as $c)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $c->name }}</td>
                                <td>{{ $c->duration }} {{ Str::plural('month', $c->duration) }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if (Qs::userIsTeamSA())
                                            <li>
                                                <a class="dropdown-item" href="{{ route('classes.edit', $c->id) }}">
                                                    <i class="icon-pencil"></i> Edit
                                                </a>
                                            </li>
                                            @endif
                                            @if (Qs::userIsSuperAdmin())
                                            <li>
                                                <form id="item-delete-{{ $c->id }}"
                                                    action="{{ route('classes.destroy', $c->id) }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <a class="dropdown-item text-danger" href="#"
                                                    onclick="confirmDelete({{ $c->id }});">
                                                    <i class="icon-trash"></i> Delete
                                                </a>
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

            {{-- New Class --}}
            <div class="tab-pane fade" id="new-class">
                <div class="row mt-3 justify-content-center">
                    <div class="col-md-6">
                        <form id="createClassForm" method="post" action="{{ route('classes.store') }}">
                            @csrf
                            {{-- Class Name --}}
                            <div class="mb-3 row align-items-center">
                                <label class="col-lg-4 col-form-label font-weight-semibold">Name <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input name="name" value="{{ old('name') }}" required type="text"
                                        class="form-control" placeholder="Name of Class">
                                </div>
                            </div>

                            {{-- Monthly Duration --}}
                            <div class="mb-3 row align-items-center">
                                <label class="col-lg-4 col-form-label font-weight-semibold">Monthly Duration <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <select name="duration" class="form-control select" required>
                                        @for($i = 1; $i <= 24; $i++) <option value="{{ $i }}" {{ old('duration')==$i
                                            ? 'selected' : '' }}>
                                            {{ $i }} {{ Str::plural('month', $i) }}
                                            </option>
                                            @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="text-end mt-2">
                                <button type="submit" class="btn btn-primary">
                                    Submit form <i class="icon-paperplane ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- El PDF btn hide css  start--}}
<style>
div.dt-buttons {
    display: none !important;
}
</style>
{{-- El PDF btn hide css  end--}}

{{-- SweetAlert + AJAX --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() }
    });

    // Create class AJAX
    $('#createClassForm').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var actionUrl = form.attr('action');

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: form.serialize(),
            success: function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Created!',
                    text: 'Class has been created successfully.',
                    showConfirmButton: false,
                    timer: 2000
                });
                form[0].reset();
                $('#classTabs a[href="#all-classes"]').tab('show');
                location.reload(); // Refresh to show new row
            },
            error: function(err){
                let msg = 'Something went wrong. Please check your inputs.';
                if(err.responseJSON && err.responseJSON.errors){
                    msg = Object.values(err.responseJSON.errors).map(e => e.join(', ')).join('\n');
                }
                Swal.fire({ icon: 'error', title: 'Error!', text: msg });
            }
        });
    });

    // Delete confirmation
    window.confirmDelete = function(id){
        Swal.fire({
            title: 'Are you sure?',
            text: 'This class will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result)=>{
            if(result.isConfirmed){
                const form = document.getElementById('item-delete-' + id);
                const row = form.closest('tr');
                $.ajax({
                    url: form.action,
                    method: 'POST',
                    data: $(form).serialize(),
                    success: function(){
                        $(row).fadeOut(500, function(){ $(this).remove(); });
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Class has been deleted successfully.', showConfirmButton: false, timer: 2000 });
                    },
                    error: function(){ Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong!' }); }
                });
            }
        });
    };

});
</script>

@endsection
