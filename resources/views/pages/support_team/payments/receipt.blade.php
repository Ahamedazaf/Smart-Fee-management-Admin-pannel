<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Receipt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .paid-stamp {
            border: 4px solid #ef4444;
            color: #ef4444;
            font-weight: 700;
            font-size: 1.5rem;
            transform: rotate(-15deg);
            padding: 0.25rem 1.5rem;
            opacity: 0.85;
            display: inline-block;
            border-radius: 0.25rem;
        }

        .receipt-container {
            width: 210mm;
            /* A5 landscape width */
            height: 148mm;
            /* A5 landscape height */
            margin: auto;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
            padding: 1rem 1.5rem;
            background-color: #fff;
            border-radius: 0.5rem;
            box-sizing: border-box;
            overflow: hidden;
            /* Prevent spilling */
            page-break-inside: avoid;
        }

        @page {
            size: A5 landscape;
            margin: 0;
            /* Remove all margins */
        }

        @media print {
            body {
                background: none !important;
                margin: 0;
            }

            .receipt-container {
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>

<body>

    <div class="receipt-container">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start mb-4 border-b pb-2 gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-14 h-14 bg-gray-200 flex items-center justify-center rounded-full shadow-inner">
        
                    <div class="w-16 h-16 flex items-center justify-center rounded-full overflow-hidden shadow">
                        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="School Logo"
                            class="object-cover w-full h-full transition-transform duration-300 hover:scale-105">
                    </div>

                </div>
                <div>

                    <p class="text-sm font-semibold text-gray-600">SIP Abacus Puttalam </p>
                    <div class="text-xs text-gray-500 mt-1 space-y-0.5">
                        <p>📍 No.25, Poles Road, Puttalam</p>
                        <p>📞 076 656 2213</p>
                        <p>📧 sipabacusputtalam@gmail.com</p>
                    </div>
                </div>
            </div>
            <div class="text-right w-full md:w-auto">
                <div class="flex justify-end text-sm"><b>Date:</b>&nbsp;{{ date('d/m/Y') }}</div>
                <div class="flex justify-end text-sm"><b>Admission No:</b>&nbsp;{{ $sr->adm_no }}</div>
                <div class="flex justify-end text-sm"><b>Receipt No:</b>&nbsp;{{ $pr->ref_no }}</div>
            </div>
        </header>

        <!-- Title -->
        <div class="text-center my-3">
            <h2 class="bg-black text-white inline-block px-6 py-1 text-lg font-bold tracking-widest rounded-md shadow">
                RECEIPT
            </h2>
        </div>

        <!-- Body -->
        <main>
            <!-- Received From (label left, name left-aligned) -->
            <div class="mb-2 flex items-center text-sm">
                <span class="w-36 font-medium text-gray-700">Received From:</span>
                <span class="font-semibold text-gray-800">{{ $sr->user->name }}</span>
            </div>
            <div class="border-b mb-2"></div>

            <?php
            $sum_of_tot = ($pr->balance ?? 0) + ($payment->additional_amount ?? 0);
            ?>
            <!-- Sum of Rs (label left, amount left-aligned) -->
            <div class="mb-2 flex items-center text-sm">
                <span class="w-36 font-medium text-gray-700">Sum of Rs:</span>
                <span class="font-semibold text-gray-800">
                    {{ $sum_of_tot }}

                </span>
            </div>
            <div class="border-b mb-2"></div>

            <!-- Month of -->
            <div class="mb-2 flex items-center text-sm">
                <span class="w-36 font-medium text-gray-700">Month of:</span>
                <span class="font-semibold text-gray-800">
                    {{ date('F') }}
                </span>
            </div>
            <div class="border-b mb-2"></div>



            <!-- Payment Details -->
            <div class="grid grid-cols-3 gap-6 text-center text-sm mb-6">
                <div>
                    <span class="font-medium">Total Paid :</span>
                    <div class="border-b border-black mt-1 font-semibold">
                        {{ $pr->amt_paid }}
                    </div>
                </div>
                <div>
                    <span class="font-medium">Paid Today :</span>
                    <div class="border-b border-black mt-1 font-semibold">
                        {{ optional($receipts->last())->amt_paid }}
                       
                    </div>
                </div>
                <div>
                    <span class="font-bold">Balance :</span>
                    <div class="border-b border-black mt-1 font-semibold">
                        {{ optional($receipts->last())->balance }}
                        {{ $payment->additional_amount - $pr->additional_amount_paid > 0? '+ ' .
                        ($payment->additional_amount - $pr->additional_amount_paid) . ' (Addition)': '' }}
                    </div>
                </div>
            </div>


            <!-- Signature -->
            <div class="flex justify-between items-end">
                <div class="text-sm">
                    <span><b>Year:</b> {{ date('Y') }}</span>
                    <div class="border-b w-20 mt-1"></div>
                </div>
                <div class="relative text-center">
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2">
                        <div class="paid-stamp">PAID</div>
                    </div>
                    <div class="border-b w-32 mt-6"></div>
                    <p class="text-sm font-medium mt-1">Accountant</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="text-center mt-3 text-xs text-gray-400">
            SIP Abacus Puttalam
        </footer>
    </div>

</body>

</html>