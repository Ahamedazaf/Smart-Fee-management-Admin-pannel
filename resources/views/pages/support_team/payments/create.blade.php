@extends('layouts.master')
@section('page_title', 'Create Payment')
@section('content')
<style>
    /* Light gray placeholders */
    .form-control::placeholder {
        color: #b0b0b0 !important;
        font-weight: 200 !important;
        font-style: normal !important;
        opacity: 1 !important;
    }

    .form-group label {
        color: #7f7f7f !important;
        font-weight: 600;
    }

    /* Totals colors */
    #totalAdditionalAmount {
        color: #dc3545;
    }

    #totalYearlyAmount {
        color: #0d6efd;
    }

    #totalAmount {
        color: #198754;
    }
</style>

{{-- Success Toast --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
    <div id="successToast" class="toast bg-success text-white border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Payment created successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm-12">
                <div class="page-sub-header">
                    <h3 class="page-title"><i class="bi bi-cash-coin me-2"></i> Create Payment</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Payments</a></li>
                        <li class="breadcrumb-item active">Create Payment</li>
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
                    <form class="ajax-store" method="POST" action="{{ route('payments.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <h5 class="form-title student-info">Payment Information</h5>
                            </div>

                            <!-- Title -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Payment Title <span class="login-danger">*</span></label>
                                    <input name="title" value="{{ old('title') }}" required type="text"
                                        class="form-control" placeholder="Eg. School Fees, Exam Fee">
                                    @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Class -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Select the Courses</label>
                                    <select class="form-control select-search" name="my_class_id" id="my_class_id">
                                        <option value="">All Courses</option>
                                        @foreach ($my_classes as $c)
                                        <option value="{{ $c->id }}" data-duration="{{ $c->duration ?? 12 }}" {{
                                            old('my_class_id')==$c->id ? 'selected' : '' }}>
                                            {{ $c->name }} - {{ $c->duration ?? 12 }} months
                                        </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="duration" value="12">
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Course Total Fees <span class="login-danger">*</span></label>
                                    <input name="amount" id="amount" type="number" value="{{ old('amount') }}" required
                                        class="form-control" placeholder="0.00">
                                </div>
                            </div>

                            <!-- Monthly Amount -->
                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Monthly Amount</label>
                                    <input name="monthly_amount" id="monthly_amount" type="number" readonly
                                        class="form-control" placeholder="Auto calculated">
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="form-group local-forms">
                                    <label>Fees Description</label>
                                    <input name="description" id="description" type="text"
                                        value="{{ old('description') }}" class="form-control"
                                        placeholder="Eg. Includes Library, Lab & Sports Fees">
                                </div>
                            </div>

                            <!-- Toggle Additional Fee Items -->
                            <div class="col-12 mt-4">
                                <button type="button" class="btn btn-outline-primary w-100 py-2 mb-3"
                                    id="toggleAdditionalBtn">
                                    <i class="bi bi-plus-lg me-1"></i> Show Additional Fee Items
                                </button>

                                <div id="feeItemsContainer" class="mb-3" style="display: none;">
                                    <button type="button" class="btn btn-outline-primary w-100 py-2 mt-2 m-3 mb-3"
                                        onclick="addFeeItem()">
                                        <i class="bi bi-plus-lg me-1"></i> Add Another Item
                                    </button>

                                    <!-- First row -->
                                    <div class="row g-3 align-items-center fee-item p-3 mb-2 border rounded mt-3">
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control"
                                                placeholder="Item Name (e.g. Lab Fee)" id="itemName_1">
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-text">LKR</span>
                                                <input type="number" step="0.01" class="form-control" placeholder="0.00"
                                                    min="0" oninput="calculateTotal()" id="itemAmount_1">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 d-grid">
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="removeFeeItem(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Totals -->
                            <div class="col-12 mt-4">
                                <div class="bg-white shadow-sm rounded-4 p-4">
                                    <div
                                        class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-2 fw-semibold text-dark">
                                                Total Additional Amount: <span id="totalAdditionalAmount"
                                                    class="fw-bold">LKR 0.00</span>
                                            </p>
                                            <p class="mb-2 fw-semibold text-dark">
                                                Total Yearly Amount: <span id="totalYearlyAmount" class="fw-bold">LKR
                                                    0.00</span>
                                            </p>
                                            <h5 class="fw-bold mt-3">
                                                Total Amount: <span id="totalAmount">LKR 0.00</span>
                                            </h5>
                                        </div>
                                        <div class="mt-3 mt-md-0">
                                            <input type="hidden" name="amount" id="amount_hidden" value="0">
                                            <input type="hidden" name="additional_items" id="additional_items">
                                            <input type="hidden" name="total_amount" id="total_amount_hidden" value="0">
                                            <input type="hidden" name="additional_amount" id="additional_amount"
                                                value="0">
                                            <button type="submit" class="btn btn-primary">Submit Payment</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

            // AJAX form submission
            $('.ajax-store').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    success: function(response) {
                        var toastEl = document.getElementById('successToast');
                        var toast = new bootstrap.Toast(toastEl);
                        toast.show();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Payment created successfully!',
                            timer: 3000,
                            showConfirmButton: false
                        });

                        form[0].reset();
                        feeItemCounter = 1;
                        calculateYearlyTotal();
                        calculateTotal();
                        calculateFinalTotal();

                        $('#feeItemsContainer').hide();
                        $('#toggleAdditionalBtn')
                            .show()
                            .html('<i class="bi bi-plus-lg me-1"></i> Show Additional Fee Items');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Something went wrong!');
                    }
                });
            });

            // Toggle Additional Fee Items
            $('#toggleAdditionalBtn').click(function() {
                const container = $('#feeItemsContainer');
                if (container.is(':visible')) {
                    container.hide();
                    $(this).html('<i class="bi bi-plus-lg me-1"></i> Show Additional Fee Items');
                    container.find('input').val('');
                    calculateTotal();
                } else {
                    container.show();
                    $(this).html('<i class="bi bi-dash-lg me-1"></i> Hide Additional Fee Items');
                }
            });

            // Calculate monthly when amount changes
            $('#amount').on('input', function() {
                let yearly = parseFloat($(this).val()) || 0;
                let duration = parseInt($('#duration').val()) || 12;
                $('#monthly_amount').val(yearly > 0 ? (yearly / duration).toFixed(2) : '');
                calculateYearlyTotal();
                calculateFinalTotal();
            });

            // On class change
            $('#my_class_id').change(function() {
                const selectedOption = $(this).find('option:selected');
                const duration = parseInt(selectedOption.data('duration')) || 12;
                $('#duration').val(duration);
                $('#amount').trigger('input'); // recalc monthly
            });
        });

        let feeItemCounter = 1;

        function addFeeItem() {
            feeItemCounter++;
            const container = document.getElementById('feeItemsContainer');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'align-items-center', 'fee-item', 'bg-gray-50', 'p-3', 'rounded-lg', 'border');
            newRow.innerHTML = `
        <div class="col-sm-5">
            <input type="text" class="form-control" id="itemName_${feeItemCounter}" placeholder="Miscellaneous Fee">
        </div>
        <div class="col-sm-5">
            <div class="input-group">
                <span class="input-group-text">LKR</span>
                <input type="number" step="0.01" class="form-control" placeholder="0.00" min="0" id="itemAmount_${feeItemCounter}" oninput="calculateTotal()">
            </div>
        </div>
        <div class="col-sm-2 d-grid">
            <button type="button" class="btn btn-outline-danger" onclick="removeFeeItem(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
            container.appendChild(newRow);
        }

        function removeFeeItem(button) {
            const allItems = document.querySelectorAll('.fee-item');
            if (allItems.length > 1) {
                button.closest('.fee-item').remove();
                calculateTotal();
            }
        }

        function calculateYearlyTotal() {
            let yearly = parseFloat($('#amount').val()) || 0;
            $('#totalYearlyAmount').text(`LKR ${yearly.toFixed(2)}`);
        }

        function calculateTotal() {
            let total = 0;
            $('input[id^="itemAmount_"]').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#totalAdditionalAmount').text(`LKR ${total.toFixed(2)}`);
            calculateFinalTotal();
        }

        function calculateFinalTotal() {
            let yearly = parseFloat($('#amount').val()) || 0;
            let additional = 0;
            let items = [];

            $('.fee-item').each(function() {
                let name = $(this).find('[id^="itemName_"]').val() || "";
                let amount = parseFloat($(this).find('[id^="itemAmount_"]').val()) || 0;
                items.push({ name, amount });
                additional += amount;
            });

            let finalTotal = yearly + additional;
            $('#totalAdditionalAmount').text(`LKR ${additional.toFixed(2)}`);
            $('#totalAmount').text(`LKR ${finalTotal.toFixed(2)}`);
            $('#amount_hidden').val(yearly.toFixed(2));
            $('#additional_items').val(JSON.stringify(items));
            $('#total_amount_hidden').val(finalTotal.toFixed(2));
            $('#additional_amount').val(additional.toFixed(2));
        }

        // Initial calculation on load
        window.onload = function() {
            calculateYearlyTotal();
            calculateTotal();
            calculateFinalTotal();
        };
</script>
@endsection