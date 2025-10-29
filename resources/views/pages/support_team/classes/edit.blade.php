@extends('layouts.master')
@section('page_title', 'Edit Class - '.$c->name)
@section('content')

@php
use Illuminate\Support\Str;
@endphp

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Edit Class</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="{{ route('classes.update', $c->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Class Name --}}
                    <div class="form-group row mb-3">
                        <label class="col-lg-3 col-form-label font-weight-semibold">Class Name <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input name="name" value="{{ old('name', $c->name) }}" required type="text"
                                class="form-control" placeholder="Name of Class">
                        </div>
                    </div>

                    {{-- Monthly Duration --}}
                    <div class="form-group row mb-3">
                        <label class="col-lg-3 col-form-label font-weight-semibold">Monthly Duration <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <select name="duration" class="form-control select" required>
                                @for($i = 1; $i <= 24; $i++) <option value="{{ $i }}" {{ old('duration', $c->duration)
                                    == $i ? 'selected' : '' }}>
                                    {{ $i }} {{ Str::plural('month', $i) }}
                                    </option>
                                    @endfor
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Submit form <i
                                class="icon-paperplane ms-2"></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- Success alert --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3">
    <i class="icon-checkmark-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@endsection