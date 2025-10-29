@extends('layouts.master')
@section('page_title', 'Manage Payments')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title font-weight-bold">Manage Payment Records for {{ $sr->user->name }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-uc" class="nav-link active" data-toggle="tab">Incomplete Payments</a>
                </li>
                <li class="nav-item"><a href="#all-cl" class="nav-link" data-toggle="tab">Completed Payments</a></li>
                <li class="nav-item"><a href="#additional-fine" class="nav-link" data-toggle="tab">Additional Fine
                        Payments</a></li>

                <li class="nav-item">
                    <a href="#edit-payments" class="nav-link" data-toggle="tab">
                        Edit Payments
                    </a>
                </li>
            </ul>

            <div class="tab-content">



                <!--  Edit Payments Tab -->
                <div class="tab-pane fade" id="edit-payments">
                    <h5 class="mb-3 font-weight-bold">Edit Payment Records</h5>

                    <table class="table table-bordered table-hover table-striped">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>ID</th>
                                <th>Student ID</th>
                                <th>Payment ID</th>
                                <th>Amount Paid</th>
                                <th>Balance</th>
                                <th>Year</th>
                                <th>Month</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody id="editPaymentTableBody">
                            @forelse($payment_records as $p)
                                @php
                                    $paid = is_array($p->paid_months)
                                        ? $p->paid_months
                                        : json_decode($p->paid_months ?? '[]', true);
                                @endphp
                                <tr class="text-center" data-id="{{ $p->id }}">
                                    <td>{{ $p->id }}</td>
                                    <td>{{ $p->student_id }}</td>
                                    <td>{{ $p->payment_id }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm amt-paid"
                                            value="{{ $p->amt_paid ?? 0 }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm balance"
                                            value="{{ $p->balance ?? 0 }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm year"
                                            value="{{ $p->year ?? '' }}">
                                    </td>
                                    <td>
                                        <select multiple class="form-control form-control-sm paid-months"
                                            style="height: 200px;">
                                            @if (!empty($paid))
                                                @foreach ($paid as $m)
                                                    <option value="{{ $m }}"
                                                        style="background:#007bff;color:white;">{{ $m }}</option>
                                                @endforeach
                                            @else
                                                <option disabled>No months found</option>
                                            @endif
                                        </select>
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-success save-btn">
                                            <i class="icon-floppy-disk"></i> Save
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No payment records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>





                <script>
                    const baseUrl = "{{ url('payments') }}";
                    const studentId = "{{ $sr->user_id }}";




                    //  Save button click
                    $('#editPaymentTableBody').on('click', '.save-btn', function() {





                        const row = $(this).closest('tr');
                        const id = row.data('id');

                        //  Only ticked (selected) months will go to backend
                        const selectedMonths = Array.from(
                            row.find('.paid-months')[0].selectedOptions
                        ).map(o => o.value);


                        //  Read numeric values
                        const amtPaid = parseFloat(row.find('.amt-paid').val()) || 0;
                        const balance = parseFloat(row.find('.balance').val()) || 0;


                        const currentOptions = Array.from(row.find('.paid-months option')).map(o => o.value);

                        //  Divide amtPaid by total months (from DB)
                        const perMonthValue = currentOptions.length > 0 ? amtPaid / currentOptions.length : 0;

                        //  Multiply by selected (ticked) months
                        const removedAmount = perMonthValue * selectedMonths.length;

                        //  Update new values
                        const newAmtPaid = amtPaid - removedAmount;
                        const newBalance = balance + removedAmount;




                        //  Build payload to send to backend
                        const payload = {
                            amt_paid: newAmtPaid.toFixed(2),
                            balance: newBalance.toFixed(2),
                            year: row.find('.year').val(),
                            paid_months: selectedMonths
                        };

                        const button = $(this);
                        button.prop('disabled', true).html('<i class="icon-spinner2 spinner"></i> Saving...');

                        fetch(`${baseUrl}/record/${id}/update-inline`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(payload)
                            })
                            .then(res => res.json())
                            .then(data => {
                                button.prop('disabled', false).html('<i class="icon-floppy-disk"></i> Save');

                                if (data.ok) {
                                    //  Remove only selected (ticked) months visually
                                    row.find('.paid-months option').each(function() {
                                        if (selectedMonths.includes(this.value)) {
                                            $(this).remove();
                                        }
                                    });

                                    row.addClass('table-success');
                                    setTimeout(() => {
                                        row.removeClass('table-success');
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Updated!',
                                            text: 'Record updated successfully!',
                                            showConfirmButton: false,
                                            timer: 1500
                                        }).then(() => {
                                            location.reload();
                                        });

                                    }, 1500);
                                } else {
                                    alert(data.msg || 'Update failed.');
                                }
                            })
                            .catch(() => {
                                button.prop('disabled', false).html('<i class="icon-floppy-disk"></i> Save');
                                alert('Network error. Please try again.');
                            });
                    });
                </script>



                <div class="tab-pane fade show active" id="all-uc">
                    <table class="table datatable-button-html5-columns table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Pay Ref</th>
                                <th>Yearly Amount</th>
                                <th>Paid</th>
                                <th>Pre payment</th>
                                <th>Months</th>
                                <th>Pay Now</th>
                                <th>Receipt No</th>
                                <th>Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($uncleared as $uc)
                                @php
                                    $uc->class_duration = $class_duration ?? 12;
                                    $duration = $uc->class_duration ?? 12;
                                    $per_month_amount = $uc->payment->amount / $duration;

                                    // All months list
                                    $monthsList = [
                                        'January',
                                        'February',
                                        'March',
                                        'April',
                                        'May',
                                        'June',
                                        'July',
                                        'August',
                                        'September',
                                        'October',
                                        'November',
                                        'December',
                                    ];

                                    // Start from payment created date
                                    $startDate = \Carbon\Carbon::parse($uc->payment->created_at);
                                    $startMonthNum = $startDate->month; // 1–12
                                    $startYear = $startDate->year;

                                    // Build month-year list
                                    $totalMonths = $uc->class_duration ?? 12;
                                    $dynamicMonths = [];
                                    for ($i = 0; $i < $totalMonths; $i++) {
                                        $monthIndex = ($startMonthNum - 1 + $i) % 12;
                                        $yearOffset = intdiv($startMonthNum - 1 + $i, 12);
                                        $dynamicMonths[] = [
                                            'month' => $monthsList[$monthIndex],
                                            'year' => $startYear + $yearOffset,
                                        ];
                                    }

                                    // Decode paid months from DB (JSON)
                                    $paidMonths = $uc->paid_months
                                        ? (is_array($uc->paid_months)
                                            ? $uc->paid_months
                                            : json_decode($uc->paid_months, true))
                                        : [];

                                    $hash = Qs::hash($uc->id);

                                    $amt = $uc->amt_paid; // total amount
                                    $unit = $per_month_amount;

                                    // echo $amt;

                                    // Remaining balance
                                    $balance = $amt % $unit;
                                    // echo $balance;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $uc->payment->title }}</td>
                                    <td>{{ $uc->payment->ref_no }}</td>
                                    <td class="font-weight-bold">{{ number_format($uc->payment->amount, 2) }}</td>
                                    <td class="text-blue font-weight-bold">{{ number_format($amt - $balance ?: 0, 2) }}
                                    </td>
                                    <td class=" text-danger font-weight-bold">
                                        {{ number_format($balance ?: 0, 2) }}
                                    </td>
                                    <td style="min-width:220px;">
                                        <div class="mb-2">
                                            <label class="text-muted small">Enter Total Amount (LKR)</label>
                                            <input type="number" class="form-control total-amount-input"
                                                id="total-input-{{ $hash }}" placeholder="e.g. 10,000"
                                                min="0" data-hash="{{ $hash }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted small">Select Months</label>
                                            <select id="months-select-{{ $hash }}"
                                                class="form-control months-select w-100" multiple
                                                data-year-amount="{{ $uc->payment->amount }}"
                                                data-hash="{{ $hash }}" style="height: 220px; overflow-y: auto;">

                                                @php $currentYear = null; @endphp

                                                @foreach ($dynamicMonths as $index => $m)
                                                    @if ($currentYear !== $m['year'])
                                                        @if ($currentYear !== null)
                                                            </optgroup>
                                                        @endif
                                                        <optgroup label="{{ $m['year'] }}">
                                                            @php $currentYear = $m['year']; @endphp
                                                    @endif

                                                    @php
                                                        $monthLabel = $m['month'] . ' ' . $m['year'];
                                                        $isPaid = in_array($monthLabel, $paidMonths);
                                                    @endphp

                                                    <option value="{{ $monthLabel }}"
                                                        @if ($isPaid) disabled
                                                    style="background:#eee;color:#666;" @endif>
                                                        {{ $m['month'] }}
                                                        @if ($isPaid)
                                                            (Paid)
                                                        @endif
                                                    </option>

                                                    @if ($loop->last)
                                                        </optgroup>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>



                                    </td>
                                    <td style="min-width:210px;">
                                        {{-- ✅ Regular monthly payment form --}}
                                        <form id="form-{{ $hash }}" method="post" class="ajax-pay"
                                            action="{{ route('payments.pay_now', Qs::hash($uc->id)) }}">
                                            @csrf
                                            <div class="d-flex flex-column">
                                                <div class="mb-2">
                                                    <span
                                                        class="badge badge-primary pay-amount-display-{{ $hash }}">0.00</span>
                                                    <small class="text-muted" id="pay-months-count-{{ $hash }}">(0
                                                        months)</small>
                                                </div>
                                                <div id="hidden-months-{{ $hash }}"></div>
                                                <button class="btn btn-danger" type="submit"
                                                    id="pay-btn-{{ $hash }}" disabled>
                                                    Pay <i class="icon-paperplane ml-2"></i>
                                                </button>
                                            </div>
                                        </form>

                                        {{-- ✅ Additional Payment Form (separate & independent) --}}
                                        @if (isset($uc->payment->additional_amount) && $uc->payment->additional_amount > 0)
                                            <div class="alert alert-info mt-2 p-2 text-center">
                                                <strong>Additional Payment Due:</strong><br>
                                                {{ number_format($uc->payment->additional_amount - $additional_payment_paid, 2) }}
                                                LKR
                                            </div>

                                            <form action="{{ route('payments.pay_now', Qs::hash($uc->payment->id)) }}"
                                                method="POST" class="mt-2 ajax-pay">
                                                @csrf

                                                <div class="form-group">
                                                    <label for="custom_amount" class="font-weight-bold">Enter Amount to Pay
                                                        (LKR)</label>
                                                    <input type="number" step="0.01" min="0"
                                                        max="{{ $uc->payment->additional_amount }}" class="form-control"
                                                        name="additional_amount" id="custom_amount" placeholder="e.g. 500"
                                                        required>
                                                    <input type="hidden" name="student_id" value="{{ $sr->user_id }}">
                                                    {{-- <input type="hidden" name="additional_amoun"> --}}
                                                    <small class="text-muted">You can pay any amount up to
                                                        {{ number_format($uc->payment->additional_amount - $additional_payment_paid, 2) }}
                                                        LKR.</small>
                                                </div>

                                                <button type="submit" class="btn btn-success w-100"
                                                    onclick="return confirm('Are you sure you want to pay this amount?')">
                                                    Pay Additional <i class="icon-paperplane ml-2"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>


                                    <td>{{ $uc->ref_no }}</td>
                                    <td>{{ $uc->year }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown"><i
                                                        class="icon-menu9"></i></a>
                                                <div class="dropdown-menu dropdown-menu-left">
                                                    {{-- <a id="{{ $hash }}" onclick="confirmReset(this.id)" href="#"
                                                    class="dropdown-item">
                                                    <i class="icon-reset"></i> Reset Payment
                                                </a> --}}
                                                    <form method="post" id="item-reset-{{ $hash }}"
                                                        action="{{ route('payments.reset_record', $uc->id) }}"
                                                        class="hidden">
                                                        @csrf @method('delete')
                                                    </form>
                                                    <a target="_blank"
                                                        href="{{ route('payments.receipts', Qs::hash($uc->id)) }}"
                                                        class="dropdown-item">
                                                        <i class="icon-printer"></i> Print Receipt
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="all-cl">
                    <h5 class="mb-3 font-weight-bold">Completed Regular Payments</h5>
                    <table class="table datatable-button-html5-columns table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Pay Ref</th>
                                <th>Amount</th>
                                <th>Receipt No</th>
                                <th>Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cleared as $cl)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $cl->payment->title }}</td>
                                    <td>{{ $cl->payment->ref_no }}</td>
                                    <td class="font-weight-bold">{{ number_format($cl->payment->amount, 2) }}</td>
                                    <td>{{ $cl->ref_no }}</td>
                                    <td>{{ $cl->year }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown"><i
                                                        class="icon-menu9"></i></a>
                                                <div class="dropdown-menu dropdown-menu-left">
                                                    <a id="{{ Qs::hash($cl->id) }}" onclick="confirmReset(this.id)"
                                                        href="#" class="dropdown-item">
                                                        <i class="icon-reset"></i> Reset Payment
                                                    </a>
                                                    <form method="post" id="item-reset-{{ Qs::hash($cl->id) }}"
                                                        action="{{ route('payments.reset_record', $cl->id) }}"
                                                        class="hidden">
                                                        @csrf @method('delete')
                                                    </form>
                                                    <a target="_blank" href="{{ route('payments.receipts', $cl->id) }}"
                                                        class="dropdown-item">
                                                        <i class="icon-printer"></i> Print Receipt
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr class="my-4">
                    <h5 class="mb-3 font-weight-bold">Completed Fine Payments</h5>
                    <table class="table table-bordered table-hover table-sm" style="border: 1px solid #ddd;">
                        <thead class="thead-light" style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                            <tr class="text-center">
                                <th style="width: 20%;">User ID</th>
                                <th style="width: 45%;">Details</th>
                                <th style="width: 20%;">Total Amount (LKR)</th>
                                <th style="width: 20%;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fines as $fine)
                                <tr>
                                    <td class="text-center">{{ $fine->user_id }}</td>
                                    <td>
                                        @php $details = json_decode($fine->details_json, true); @endphp
                                        @if ($details)
                                            <ul class="mb-0 pl-3" style="list-style-type: disc;">
                                                @foreach ($details as $item)
                                                    <li>{{ $item['item_name'] ?? '' }} - <span
                                                            class="font-weight-bold">{{ number_format($item['amount'] ?? 0, 2) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <em class="text-muted">No details available</em>
                                        @endif
                                    </td>
                                    <td class="text-center font-weight-bold">{{ number_format($fine->total_amount, 2) }}
                                    </td>
                                    <td class="text-center">{{ $fine->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No fine payments found for this
                                        student.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="additional-fine">
                    <form id="fineForm" action="{{ route('fines.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $sr->user->id ?? '' }}">
                        <div class="row g-5">
                            <div class="col-lg-6 border-end" style="border-color: #e5e7eb;">
                                <h6 class="fw-bold text-secondary mb-3" style="font-size: 1.1rem;">
                                    <i class="icon-list2 mr-1"></i> Fine Breakdown
                                </h6>
                                <div id="fineItemsContainer" class="mb-4">
                                    <div class="row g-3 align-items-end fine-item mb-3 p-3 rounded-3"
                                        style="background: #f8f9fa; border: 1px solid #e5e7eb;">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Item Name</label>
                                            <input type="text" name="itemName[]" class="form-control rounded-3"
                                                placeholder="e.g., What Lost !" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-muted">Amount (LKR)</label>
                                            <input type="number" step="0.01" class="form-control rounded-3"
                                                name="itemAmount[]" placeholder="Amount" min="0" required
                                                oninput="calculateTotal()">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-danger w-100 rounded-3"
                                                onclick="removeFineItem(this)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid mb-5">
                                    <button type="button" class="btn btn-outline-secondary py-2 fw-semibold rounded-3"
                                        onclick="addFineItem()">
                                        <i class="icon-plus-circle2"></i> Add Another Item
                                    </button>
                                </div>
                                <div class="p-4 rounded-3 border text-center"
                                    style="background: #fdfdfd; border-color: #e5e7eb;">
                                    <div class="fs-5 fw-bold mb-0">
                                        Total Fine: <span id="totalAmount" class="text-success">0.00</span>
                                    </div>
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-success btn-lg px-5 rounded-3">
                                        <i class="icon-paperplane mr-2"></i> Generate Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="toast"
                    style="position: fixed; bottom: 30px; right: 30px; background: #28a745; color: #fff; padding: 14px 24px; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 10px rgba(0,0,0,0.15); opacity: 0; transform: translateY(20px); transition: opacity 0.4s ease, transform 0.4s ease; z-index: 9999;">
                    Payment Successful
                </div>


                <script>
                    (function() {
                        function showToast(message) {
                            const toast = document.getElementById('toast');
                            toast.textContent = message;
                            toast.style.opacity = '1';
                            toast.style.transform = 'translateY(0)';
                            setTimeout(() => {
                                toast.style.opacity = '0';
                                toast.style.transform = 'translateY(20px)';
                            }, 2000);
                        }

                        function showError(message) {
                            alert(message || 'Payment failed. Please try again.');
                        }

                        function calculateRow(hash) {
                            const select = document.getElementById('months-select-' + hash);
                            const totalInput = parseFloat(document.getElementById('total-input-' + hash)?.value) || 0;
                            const yearlyAmount = parseFloat(select.getAttribute('data-year-amount')) || 0;
                            const duration = {{ $duration ?? 12 }};

                            const monthlyFee = yearlyAmount / duration;

                            let paidAmount = 0;
                            let remainder = 0;

                            if (totalInput > 0) {
                                paidAmount = totalInput;
                                remainder = totalInput % monthlyFee;
                            } else {
                                const selectedMonths = Array.from(select.selectedOptions).filter(o => !o.disabled).length;
                                paidAmount = selectedMonths * monthlyFee;
                                remainder = paidAmount % monthlyFee;
                            }

                            paidAmount = parseFloat(paidAmount.toFixed(2));
                            remainder = parseFloat(remainder.toFixed(2));

                            document.querySelector('.pay-amount-display-' + hash).textContent = paidAmount.toFixed(0);

                            document.querySelector('#total-input-' + hash).value = paidAmount.toFixed(0);

                            document.getElementById('pay-months-count-' + hash).textContent =
                                '(' + Array.from(select.selectedOptions).filter(o => !o.disabled).length + ' month' +
                                (Array.from(select.selectedOptions).length !== 1 ? 's' : '') + ')';

                            const pendingCell = select.closest('tr').querySelector('.pending-amount');
                            if (pendingCell) pendingCell.textContent = remainder.toFixed(0);

                            const hiddenWrap = document.getElementById('hidden-months-' + hash);
                            hiddenWrap.innerHTML = '';
                            Array.from(select.selectedOptions).forEach(opt => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'months[]';
                                input.value = opt.value;
                                hiddenWrap.appendChild(input);
                            });

                            document.getElementById('pay-btn-' + hash).disabled = paidAmount <= 0;
                        }

                        // Calculate total amount input box eanter
                        document.querySelectorAll('.total-amount-input').forEach(input => {
                            input.addEventListener('input', function() {
                                const hash = this.getAttribute('data-hash');
                                const select = document.getElementById('months-select-' + hash);


                                const durations = Number({{ $duration ?? 12 }});


                                const yearlyAmount = parseFloat(select.getAttribute('data-year-amount')) || 0;


                                const monthlyFee = yearlyAmount / durations;


                                let totalEntered = parseFloat(this.value) || 0;


                                if (!monthlyFee || monthlyFee <= 0) {
                                    console.warn('Invalid monthly fee for', hash, monthlyFee);
                                    return;
                                }


                                const safeTotal = Math.min(totalEntered, yearlyAmount);


                                Array.from(select.options).forEach(opt => {
                                    if (!opt.disabled) opt.selected = false;
                                });


                                const monthsFloat = safeTotal / monthlyFee;
                                const fullMonths = Math.floor(monthsFloat);


                                let selectedCount = 0;
                                Array.from(select.options).forEach(opt => {
                                    if (!opt.disabled && selectedCount < fullMonths) {
                                        opt.selected = true;
                                        selectedCount++;
                                    }
                                });


                                const displayBadge = document.querySelector('.pay-amount-display-' + hash);
                                if (displayBadge) displayBadge.textContent = safeTotal.toFixed(0);

                                const monthLabel = document.getElementById('pay-months-count-' + hash);
                                if (monthLabel)
                                    monthLabel.textContent =
                                    `(${monthsFloat.toFixed(2)} month${monthsFloat >= 2 ? 's' : ''})`;


                                const payBtn = document.getElementById('pay-btn-' + hash);
                                if (payBtn) payBtn.disabled = safeTotal <= 0;


                                const hiddenWrap = document.getElementById('hidden-months-' + hash);
                                hiddenWrap.innerHTML = '';
                                Array.from(select.selectedOptions).forEach(opt => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'months[]';
                                    input.value = opt.value;
                                    hiddenWrap.appendChild(input);
                                });
                            });
                        });



                        document.querySelectorAll('.months-select').forEach(sel => {
                            const hash = sel.getAttribute('data-hash');
                            sel.addEventListener('change', function() {
                                const totalInput = document.getElementById('total-input-' + hash);
                                if (totalInput) totalInput.value = '';
                                calculateRow(hash);
                            });
                            calculateRow(hash);
                        });

                        document.querySelectorAll('.ajax-pay').forEach(form => {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const hash = form.id.replace('form-', '');
                                // calculateRow(hash);

                                const totalInput = document.getElementById(`total-input-${hash}`);
                                const totalValue = parseFloat(totalInput?.value || 0);
                                const formData = new FormData(form);
                                formData.append('total_value', totalValue);

                                fetch(form.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                                .getAttribute('content'),
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        },
                                        credentials: 'same-origin'
                                    })
                                    .then(async res => {
                                        let data = {};
                                        try {
                                            data = await res.json();
                                        } catch (_) {}

                                        if (!res.ok) {
                                            if (res.status === 419) return showError(
                                                'Session expired. Refresh and try again.');
                                            return showError(data.msg || data.message || 'Server error.');
                                        }

                                        // New Add Code
                                        if (data.ok) {
                                            showToast('Payment Successful!');


                                            location.reload();

                                        }
                                    })
                                    .catch(err => {
                                        console.error('Payment error:', err);
                                        showError('Network error. Please try again.');
                                    });
                            });
                        });
                    })();
                    // Fine Culculate Code importand
                    function calculateTotal() {
                        let total = 0;
                        document.querySelectorAll('input[name="itemAmount[]"]').forEach(input => {
                            total += parseFloat(input.value) || 0;
                        });
                        document.getElementById('totalAmount').textContent = Math.round(total);
                    }

                    function addFineItem() {
                        const container = document.getElementById('fineItemsContainer');
                        const div = document.createElement('div');
                        div.classList.add('row', 'g-3', 'align-items-end', 'fine-item', 'mb-3', 'p-3', 'rounded-3');
                        div.style.background = '#f8f9fa';
                        div.style.border = '1px solid #e5e7eb';

                        div.innerHTML = `
        <div class="col-md-6">
            <label class="form-label text-muted">Item Name</label>
            <input type="text" name="itemName[]" class="form-control rounded-3" placeholder="e.g., What Lost !" required>
        </div>
        <div class="col-md-4">
            <label class="form-label text-muted">Amount (LKR)</label>
            <input type="number" step="0.01" class="form-control rounded-3" name="itemAmount[]" placeholder="Amount" min="0" required oninput="calculateTotal()">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-outline-danger w-100 rounded-3" onclick="removeFineItem(this)">
                <i class="icon-trash"></i>
            </button>
        </div>
    `;
                        container.appendChild(div);
                    }

                    function removeFineItem(btn) {
                        btn.closest('.fine-item').remove();
                        calculateTotal();
                    }


                    // Fine Culculate Code importand
                    document.getElementById('fineForm').addEventListener('submit', function(e) {
                        e.preventDefault(); // Prevent normal form submit

                        const form = this;

                        fetch(form.action, {
                                method: 'POST',
                                body: new FormData(form),
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                credentials: 'same-origin'
                            })
                            .then(async res => {
                                let data = {};
                                try {
                                    data = await res.json();
                                } catch (_) {}

                                if (!res.ok) {
                                    alert(data.message || 'Something went wrong.');
                                    return;
                                }

                                // Show Bootstrap success alert on page
                                const alertDiv = document.createElement('div');
                                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                                alertDiv.role = 'alert';
                                alertDiv.innerHTML = `
            ${data.message || 'Fine invoice created successfully!'}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
                                form.prepend(alertDiv);

                                // Optional: clear form fields
                                form.reset();
                                document.getElementById('totalAmount').textContent = '0.00';
                                document.getElementById('fineItemsContainer').innerHTML = `
            <div class="row g-3 align-items-end fine-item mb-3 p-3 rounded-3"
                style="background: #f8f9fa; border: 1px solid #e5e7eb;">
                <div class="col-md-6">
                    <label class="form-label text-muted">Item Name</label>
                    <input type="text" name="itemName[]" class="form-control rounded-3" placeholder="e.g., What Lost !" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted">Amount (LKR)</label>
                    <input type="number" step="0.01" class="form-control rounded-3" name="itemAmount[]" placeholder="Amount" min="0" required oninput="calculateTotal()">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100 rounded-3" onclick="removeFineItem(this)">
                        <i class="icon-trash"></i>
                    </button>
                </div>
            </div>
        `;
                            })
                            .catch(() => alert('Network error. Please try again.'));
                    });
                </script>



            @endsection
