@extends('layouts.master')
@section('page_title', 'Edit Payment')
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

<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm-12">
                <div class="page-sub-header">
                    <h3 class="page-title"><i class="bi bi-pencil-square me-2"></i> Edit Payment</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Payments</a></li>
                        <li class="breadcrumb-item active">Edit Payment</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card comman-shadow">
                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form id="editPaymentForm" method="POST" action="{{ route('payments.update', $payment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12">
                                <h5 class="form-title student-info">Payment Information</h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Title <span class="login-danger">*</span></label>
                                    <input name="title" value="{{ old('title', $payment->title) }}" required type="text"
                                        class="form-control" placeholder="Eg. School Fees, Exam Fee">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Class</label>
                                    <select class="form-control select-search" name="my_class_id" id="my_class_id">
                                        <option value="">All Classes</option>
                                        @foreach ($my_classes as $c)
                                        <option value="{{ $c->id }}" {{ old('my_class_id', $payment->my_class_id) ==
                                            $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Yearly Amount (LKR) <span class="login-danger">*</span></label>
                                    <input name="amount" id="amount" type="number"
                                        value="{{ old('amount', number_format($payment->amount ?? 0, 2, '.', '')) }}"
                                        required class="form-control" placeholder="0.00" step="0.01" min="0">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group local-forms">
                                    <label>Monthly Amount</label>
                                    <input name="monthly_amount" id="monthly_amount" type="number" readonly
                                        class="form-control"
                                        value="{{ number_format(($payment->amount ?? 0) / 12, 2) }}"
                                        placeholder="Auto calculated">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group local-forms">
                                    <label>Description</label>
                                    <input name="description" type="text"
                                        value="{{ old('description', $payment->description) }}" class="form-control"
                                        placeholder="Eg. Includes Library, Lab & Sports Fees">
                                </div>
                            </div>

                            <!-- Additional Fee Items -->
                            <div class="col-12 mt-4">
                                <h6 class="mb-3">Additional Fee Items</h6>
                                <div id="feeItemsContainer" class="mb-3">
                                    @php
                                    $items = json_decode($payment->additional_items ?? '[]', true);
                                    $items = is_array($items) ? $items : [];
                                    @endphp

                                    @if (count($items) > 0)
                                    @foreach ($items as $it)
                                    <div class="row g-3 align-items-center fee-item p-3 mb-2 border rounded">
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control item-name" placeholder="Item Name"
                                                value="{{ $it['name'] ?? '' }}">
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-text">LKR</span>
                                                <input type="number" step="0.01" class="form-control item-amount"
                                                    placeholder="0.00" min="0"
                                                    value="{{ number_format($it['amount'] ?? 0, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 d-grid">
                                            <button type="button" class="btn btn-outline-danger remove-item"><i
                                                    class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="row g-3 align-items-center fee-item p-3 mb-2 border rounded">
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control item-name" placeholder="Item Name">
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-text">LKR</span>
                                                <input type="number" step="0.01" class="form-control item-amount"
                                                    placeholder="0.00" min="0" value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 d-grid">
                                            <button type="button" class="btn btn-outline-danger remove-item"><i
                                                    class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <button type="button" id="addItemBtn" class="btn btn-outline-primary w-100 py-2 mt-2">
                                    <i class="bi bi-plus-lg me-1"></i> Add Item
                                </button>
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
                                                    {{ number_format($payment->amount ?? 0, 2) }}</span>
                                            </p>
                                            <h5 class="fw-bold mt-3">
                                                Total Amount: <span id="totalAmount">LKR 0.00</span>
                                            </h5>
                                        </div>
                                        <div class="mt-3 mt-md-0">
                                            <input type="hidden" name="amount" id="amount_hidden"
                                                value="{{ number_format($payment->amount ?? 0, 2, '.', '') }}">
                                            <input type="hidden" name="additional_items" id="additional_items"
                                                value='@json($items)'>
                                            <input type="hidden" name="total_amount" id="total_amount_hidden"
                                                value="{{ number_format($payment->total_amount ?? 0, 2, '.', '') }}">
                                            <input type="hidden" name="additional_amount" id="additional_amount"
                                                value="{{ number_format($payment->additional_amount ?? 0, 2, '.', '') }}">
                                            <button type="submit" class="btn btn-primary">Update Payment</button>
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
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const feeItemsContainer = document.getElementById('feeItemsContainer');
            const addItemBtn = document.getElementById('addItemBtn');
            const amountInput = document.getElementById('amount');
            const monthlyInput = document.getElementById('monthly_amount');
            const totalAdditionalAmountEl = document.getElementById('totalAdditionalAmount');
            const totalYearlyAmountEl = document.getElementById('totalYearlyAmount');
            const totalAmountEl = document.getElementById('totalAmount');
            const hiddenAdditionalItems = document.getElementById('additional_items');
            const hiddenAmount = document.getElementById('amount_hidden');
            const hiddenTotalAmount = document.getElementById('total_amount_hidden');
            const hiddenAdditionalAmount = document.getElementById('additional_amount');
            const form = document.getElementById('editPaymentForm');

            function parseNumberSafe(val) {
                if (!val) return 0;
                return parseFloat(val.toString().replace(/,/g, '.')) || 0;
            }

            function getItemsArray() {
                const arr = [];
                feeItemsContainer.querySelectorAll('.fee-item').forEach(row => {
                    const name = row.querySelector('.item-name')?.value.trim() || '';
                    const amount = parseNumberSafe(row.querySelector('.item-amount')?.value);
                    if (name) arr.push({
                        name,
                        amount
                    });
                });
                return arr;
            }

            function recalcTotals() {
                const additional = getItemsArray().reduce((sum, item) => sum + item.amount, 0);
                const yearly = parseNumberSafe(amountInput.value);
                const total = yearly + additional;

                if (monthlyInput) monthlyInput.value = (yearly / 12).toFixed(2);
                totalAdditionalAmountEl.textContent = `LKR ${additional.toFixed(2)}`;
                totalYearlyAmountEl.textContent = `LKR ${yearly.toFixed(2)}`;
                totalAmountEl.textContent = `LKR ${total.toFixed(2)}`;

                hiddenAdditionalItems.value = JSON.stringify(getItemsArray());
                hiddenAmount.value = yearly.toFixed(2);
                hiddenAdditionalAmount.value = additional.toFixed(2);
                hiddenTotalAmount.value = total.toFixed(2);
            }

            function createFeeItemRow(name = '', amount = 0) {
                const row = document.createElement('div');
                row.className = 'row g-3 align-items-center fee-item p-3 mb-2 border rounded';
                row.innerHTML = `
            <div class="col-sm-5">
                <input type="text" class="form-control item-name" placeholder="Item Name" value="${name}">
            </div>
            <div class="col-sm-5">
                <div class="input-group">
                    <span class="input-group-text">LKR</span>
                    <input type="number" step="0.01" class="form-control item-amount" placeholder="0.00" min="0" value="${amount.toFixed(2)}">
                </div>
            </div>
            <div class="col-sm-2 d-grid">
                <button type="button" class="btn btn-outline-danger remove-item"><i class="bi bi-trash"></i></button>
            </div>`;
                attachRemoveListener(row.querySelector('.remove-item'));
                return row;
            }

            function attachRemoveListener(btn) {
                btn.addEventListener('click', () => {
                    const row = btn.closest('.fee-item');
                    const allRows = feeItemsContainer.querySelectorAll('.fee-item');
                    if (allRows.length > 1) {
                        row.remove();
                    } else {
                        row.querySelector('.item-name').value = '';
                        row.querySelector('.item-amount').value = '0.00';
                    }
                    recalcTotals();
                });
            }

            addItemBtn?.addEventListener('click', e => {
                e.preventDefault();
                const row = createFeeItemRow();
                feeItemsContainer.appendChild(row);
                recalcTotals();
            });

            // Attach remove listener to existing rows
            feeItemsContainer.querySelectorAll('.remove-item').forEach(btn => attachRemoveListener(btn));

            feeItemsContainer.addEventListener('input', recalcTotals);
            amountInput?.addEventListener('input', recalcTotals);
            form?.addEventListener('submit', recalcTotals);

            recalcTotals();
        });
</script>