<?php

namespace App\Http\Controllers\SupportTeam;

use PDF;
use Throwable;
use App\Helpers\Qs;
use App\Helpers\Pay;
use App\Models\Fine;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\PaymentRecord;
use App\Repositories\MyClassRepo;
use App\Repositories\PaymentRepo;
use App\Repositories\StudentRepo;
use Faker\Provider\fr_BE\Payment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MyClass;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Payment\PaymentCreate;
use App\Http\Requests\Payment\PaymentUpdate;
use App\Models\Payment as ModelsPayment;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    protected $my_class, $pay, $student, $year;

    public function __construct(MyClassRepo $my_class, PaymentRepo $pay, StudentRepo $student)
    {
        $this->my_class = $my_class;
        $this->pay = $pay;
        $this->year = Qs::getCurrentSession();
        $this->student = $student;

        $this->middleware('teamAccount')->except(['receipts', 'pdf_receipts']);
    }

    /* -------------------- Index / Show -------------------- */

    public function index()
    {
        $d['selected']   = true;
        $d['years']      = $this->pay->getPaymentYears();
        $d['payments']   = $this->pay->getPayment(['year' => $this->year])->get();
        $d['my_classes'] = $this->my_class->all();
        $d['year']       = $this->year;

        return view('pages.support_team.payments.index', $d);
    }

    public function show($year)
    {
        $d['payments'] = $p = $this->pay->getPayment(['year' => $year])->get();
        if ($p->count() < 1) {
            return Qs::goWithDanger('payments.index');
        }

        $d['selected']   = true;
        $d['my_classes'] = $this->my_class->all();
        $d['years']      = $this->pay->getPaymentYears();
        $d['year']       = $year;

        return view('pages.support_team.payments.index', $d);
    }


    // public function fetchStudentRecords($student_id)
    // {
    //     $records = PaymentRecord::where('student_id', $student_id)
    //         ->select('id', 'student_id', 'payment_id', 'amt_paid', 'balance', 'year', 'ref_no', 'today_paid', 'paid_months')
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     if ($records->isEmpty()) {
    //         return response()->json(['ok' => true, 'data' => []]);
    //     }

    //     return response()->json(['ok' => true, 'data' => $records]);
    // }

    public function updateInline(Request $request, $id)
    {
        $record = PaymentRecord::find($id);

        if (!$record) {
            return response()->json(['ok' => false, 'msg' => 'Record not found id: ' . $id]);
        }

        //  Decode DB months
        $existingMonths = json_decode($record->paid_months ?? '[]', true);
        if (!is_array($existingMonths)) {
            $existingMonths = [];
        }

        //  Get selected (ticked) months from frontend
        $selectedMonths = $request->paid_months ?? [];

        //  Remove only those months from DB list
        $remainingMonths = array_values(array_diff($existingMonths, $selectedMonths));

        $record->update([
            'amt_paid' => $request->amt_paid,
            'balance' => $request->balance,
            'year' => $request->year,
            'paid_months' => json_encode($remainingMonths),
        ]);

        return response()->json([
            'ok' => true,
            'msg' => 'Record updated successfully',
            'data' => $remainingMonths
        ]);
    }

    public function select_year(Request $req)
    {
        return Qs::goToRoute(['payments.show', $req->year]);
    }

    public function create()
    {
        $d['my_classes'] = $this->my_class->all();
        $d['students']   = $this->student->getRecord([])->get()->sortBy('user.name');
        return view('pages.support_team.payments.create', $d);
    }

    // Fine And Invoice

    // public function invoice($st_id, $year = null)
    // {


    //     if (!$st_id) {
    //         return Qs::goWithDanger();
    //     }

    //     $inv = $year
    //         ? $this->pay->getAllMyPR($st_id, $year)
    //         : $this->pay->getAllMyPR($st_id);

    //     $d['sr']        = $this->student->findByUserId($st_id)->first();
    //     $pr             = $inv->get();
    //     $d['uncleared'] = $pr->where('paid', 0);
    //     $d['cleared']   = $pr->where('paid', 1);

    //     $userId = $d['sr']->user_id;
    //     $d['fines'] = Fine::where('user_id', $userId)->latest()->get();

    //     // ✅ Instead of querying by student_id, just get the first payment record
    //     $d['additional_payment'] = $pr->first();
    //     $stud_pay_rec = PaymentRecord::where('student_id', $st_id)->first();

    //     if (!$stud_pay_rec) {
    //         // Auto-create missing payment record for the student
    //         $payment = ModelsPayment::where('my_class_id', $d['sr']->my_class_id)
    //             ->where('year', $this->year)
    //             ->first();

    //         if ($payment) {
    //             $stud_pay_rec = PaymentRecord::create([
    //                 'student_id' => $st_id,
    //                 'payment_id' => $payment->id,
    //                 'year'       => $this->year,
    //                 'ref_no'     => mt_rand(100000, 99999999),
    //             ]);
    //         }
    //     }

    //     // Fetch class duration

    //     $class = MyClass::find($d['sr']->my_class_id);
    //     $duration = $class->duration ?? 12; // correct column name with fallback


    //     $d['class_duration'] = $duration;



    //     // Avoid null access if still missing
    //     $d['additional_payment_paid'] = $stud_pay_rec->additional_amount_paid ?? 0;




    //     return view('pages.support_team.payments.invoice', $d);
    // }



    public function invoice($st_id, $year = null)
    {
        if (!$st_id) {
            return Qs::goWithDanger();
        }



        $inv = $year
            ? $this->pay->getAllMyPR($st_id, $year)
            : $this->pay->getAllMyPR($st_id);

        $d['sr'] = $this->student->findByUserId($st_id)->first();
        $pr = $inv->get();

        $d['cleared'] = $pr->filter(function ($record) {
            $duration = $record->class_duration ?? 12;
            $per_month = $record->payment->amount / $duration;

            $regular_paid = $record->amt_paid ?? 0;

            // Additional payment
            $additional_due = $record->payment->additional_amount ?? 0;
            $additional_paid = $record->additional_amount_paid ?? 0;

            $total_due = $record->payment->amount + $additional_due;
            $total_paid = $regular_paid + $additional_paid;

            return $total_paid >= $total_due;
        });

        $d['uncleared'] = $pr->filter(function ($record) {
            $duration = $record->class_duration ?? 12;
            $per_month = $record->payment->amount / $duration;

            $regular_paid = $record->amt_paid ?? 0;

            // Additional payment
            $additional_due = $record->payment->additional_amount ?? 0;
            $additional_paid = $record->additional_amount_paid ?? 0;

            $total_due = $record->payment->amount + $additional_due;
            $total_paid = $regular_paid + $additional_paid;

            return $total_paid < $total_due;
        });


        $userId = $d['sr']->user_id;
        $d['fines'] = Fine::where('user_id', $userId)->latest()->get();

        $d['additional_payment'] = $pr->first();
        $stud_pay_rec = PaymentRecord::where('student_id', $st_id)->first();

        if (!$stud_pay_rec) {
            $payment = ModelsPayment::where('my_class_id', $d['sr']->my_class_id)
                ->where('year', $this->year)
                ->first();

            if ($payment) {
                $stud_pay_rec = PaymentRecord::create([
                    'student_id' => $st_id,
                    'payment_id' => $payment->id,
                    'year'       => $this->year,
                    'ref_no'     => mt_rand(100000, 99999999),
                ]);
            }
        }

        $class = MyClass::find($d['sr']->my_class_id);
        $duration = $class->duration ?? 12;
        $d['class_duration'] = $duration;

        $d['additional_payment_paid'] = $stud_pay_rec->additional_amount_paid ?? 0;
        $pr = $inv->get();

        $d['payment_records'] = $pr;

        return view('pages.support_team.payments.invoice', $d);
    }


    /* -------------------- Receipts -------------------- */

    public function receipts($pr_id)
    {
        $pr = PaymentRecord::with(['receipt', 'payment'])->find($pr_id);
        if (!$pr) {
            return back()->with('flash_danger', 'Payment record not found.');
        }

        $d['pr']       = $pr;
        $d['receipts'] = $pr->receipt;
        $d['payment']  = $pr->payment;
        $d['sr']       = $this->student->findByUserId($pr->student_id)->first();
        // return $d['payment'];
        $d['s'] = Setting::all()->flatMap(function ($s) {
            return [$s->type => $s->description ?? ''];
        });

        return view('pages.support_team.payments.receipt', $d);
    }


    public function pdf_receipts($pr_id)
    {
        $pr = PaymentRecord::with(['receipt', 'payment'])->find($pr_id);
        if (!$pr) {
            return back()->with('flash_danger', 'Payment record not found.');
        }

        $d['pr']      = $pr;
        $d['receipts'] = $pr->receipt;
        $d['payment'] = $pr->payment;
        $d['sr']      = $this->student->findByUserId($pr->student_id)->first();
        $d['s']       = Setting::all()->flatMap(fn($s) => [$s->type => $s->description]);

        $pdf_name = 'Receipt_' . $pr->ref_no . '.pdf';
        return PDF::loadView('pages.support_team.payments.receipt', $d)->download($pdf_name);
    }

    protected function downloadReceipt($page, $data, $name = null)
    {
        $path = 'receipts/file.html';
        $disk = Storage::disk('local');
        $disk->put($path, view($page, $data));
        $html = $disk->get($path);
        return PDF::loadHTML($html)->download($name);
    }

    public function pay_now(Request $req, $id)
    {
        try {
            // Decode ID if hashed
            if (!is_numeric($id)) {
                $id = Qs::decodeHash($id);
            }

            if ($req->additional_amount) {
                PaymentRecord::where('student_id', $req->student_id)
                    ->increment('additional_amount_paid', $req->additional_amount);

                return response()->json([
                    'ok'  => true,
                    'msg' => 'Record Updated Successfully',
                ], 200);
            }

            // Get payment record
            $pr = $this->pay->findRecord($id);
            if (!$pr) {
                return response()->json(['ok' => false, 'msg' => 'Payment record not found.'], 404);
            }

            $payment = $this->pay->find($pr->payment_id);
            if (!$payment) {
                return response()->json(['ok' => false, 'msg' => 'Payment details not found.'], 404);
            }

            // Handle additional payment (no months selected)
            if (!$req->filled('months') || count($req->months ?? []) === 0) {
                $additional = (float) $req->total_value;
                if ($additional <= 0) {
                    return response()->json(['ok' => false, 'msg' => 'Invalid payment amount.'], 400);
                }

                $total_paid = (float) ($pr->amt_paid + $additional);
                $balance = round(max(0, $payment->amount - $total_paid), 2);
                $fullyPaid = $balance <= 0 ? 1 : 0;

                $this->pay->updateRecord($id, [
                    'amt_paid'   => $total_paid,
                    'today_paid' => $additional,
                    'balance'    => $balance,
                    'paid'       => $fullyPaid,
                    'updated_at' => now(),
                ]);

                $this->pay->createReceipt([
                    'amt_paid' => $additional,
                    'balance'  => $balance,
                    'pr_id'    => $id,
                    'year'     => $this->year,
                ]);

                return response()->json([
                    'ok'  => true,
                    'msg' => 'Additional payment recorded successfully.',
                    'data' => [
                        'amt_paid_now' => $additional,
                        'total_paid'   => $total_paid,
                        'balance'      => $balance,
                        'fully_paid'   => (bool) $fullyPaid,
                    ],
                ], 200);
            }

            // Otherwise handle month-based payment
            $validated = $req->validate([
                'months'   => ['required', 'array', 'min:1'],
                'months.*' => ['string']
            ]);

            $alreadyPaidMonths = $pr->paid_months
                ? (is_array($pr->paid_months) ? $pr->paid_months : json_decode($pr->paid_months, true))
                : [];

            $selectedMonths = collect($validated['months'])
                ->map(fn($m) => trim((string)$m))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $newMonths = array_values(array_diff($selectedMonths, $alreadyPaidMonths));
            if (count($newMonths) === 0) {
                return response()->json(['ok' => false, 'msg' => 'No new months selected.'], 400);
            }

            $updatedMonths = array_values(array_unique(array_merge($alreadyPaidMonths, $newMonths)));
            $monthlyAmount = round($payment->amount / 12, 2);
            $newPaymentAmount = round($monthlyAmount * count($newMonths), 2);

            $total_paid = (float) ($pr->amt_paid + $req->total_value ?? 0);
            $balance = round(max(0, $payment->amount - $total_paid), 2);
            $fullyPaid = count($updatedMonths) >= 12 || $balance <= 0 ? 1 : 0;

            $this->pay->updateRecord($id, [
                'amt_paid'    => $total_paid,
                'today_paid'  => $req->total_value,
                'balance'     => $balance,
                'paid'        => $fullyPaid,
                'paid_months' => json_encode($updatedMonths),
                'updated_at'  => now(),
            ]);

            $this->pay->createReceipt([
                'amt_paid' => $req->total_value,
                'balance'  => $balance,
                'pr_id'    => $id,
                'year'     => $this->year,
            ]);

            return response()->json([
                'ok'  => true,
                'msg' => 'Monthly payment recorded successfully.',
                'data' => [
                    'amt_paid_now' => $req->total_value,
                    'total_paid'   => $total_paid,
                    'balance'      => $balance,
                    'paid_months'  => $updatedMonths,
                    'fully_paid'   => (bool) $fullyPaid,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'ok'     => false,
                'msg'    => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'ok'    => false,
                'msg'   => 'Unexpected server error while processing payment.',
                'debug' => [
                    'message' => $e->getMessage(),
                    'line'    => $e->getLine(),
                    'file'    => $e->getFile(),
                ]
            ], 500);
        }
    }


    public function manage($class_id = null)
    {
        $d['my_classes'] = $this->my_class->all();
        $d['selected']   = false;

        if ($class_id) {
            $d['students'] = $st = $this->student
                ->getRecord(['my_class_id' => $class_id])
                ->get()
                ->sortBy('user.name');

            if ($st->count() < 1) {
                return Qs::goWithDanger('payments.manage');
            }
            $d['selected']   = true;
            $d['my_class_id'] = $class_id;
        }

        return view('pages.support_team.payments.manage', $d);
    }

    public function select_class(Request $req)
    {
        $req->validate([
            'my_class_id' => 'required|exists:my_classes,id'
        ], [], ['my_class_id' => 'Class']);

        $wh['my_class_id'] = $class_id = $req->my_class_id;
        $pay1 = $this->pay->getPayment(['my_class_id' => $class_id, 'year' => $this->year])->get();
        $pay2 = $this->pay->getGeneralPayment(['year' => $this->year])->get();
        $payments = $pay2->count() ? $pay1->merge($pay2) : $pay1;
        $students = $this->student->getRecord($wh)->get();

        if ($payments->count() && $students->count()) {
            foreach ($payments as $p) {
                foreach ($students as $st) {
                    $pr = [
                        'student_id' => $st->user_id,
                        'payment_id' => $p->id,
                        'year'       => $this->year,
                    ];
                    $rec = $this->pay->createRecord($pr);
                    if (!$rec->ref_no) {
                        $rec->update(['ref_no' => mt_rand(100000, 99999999)]);
                    }
                }
            }
        }

        return Qs::goToRoute(['payments.manage', $class_id]);
    }

    public function store(PaymentCreate $req)
    {
        $data = $req->all();
        $data['year']   = $this->year;
        $data['ref_no'] = Pay::genRefCode();

        // Create payment record
        $payment = $this->pay->create($data);

        // If class selected, create payment records only for that class
        if (!empty($req->my_class_id)) {
            $students = $this->student->getRecord(['my_class_id' => $req->my_class_id])->get();

            foreach ($students as $st) {
                $pr = [
                    'student_id' => $st->user_id,
                    'payment_id' => $payment->id,
                    'year'       => $this->year,
                ];

                $record = $this->pay->createRecord($pr);

                // Generate a reference number if not exists
                if (!$record->ref_no) {
                    $record->update(['ref_no' => mt_rand(100000, 99999999)]);
                }
            }
        }

        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['payment'] = $pay = $this->pay->find($id);
        $d['my_classes'] = $this->my_class->all();

        return is_null($pay)
            ? Qs::goWithDanger('payments.index')
            : view('pages.support_team.payments.edit', $d);
    }

    public function update(PaymentUpdate $req, $id)
    {
        $data = $req->all();



        // Always set additional_items JSON (even if empty)
        $data['additional_items'] = $req->filled('additional_items')
            ? $req->additional_items
            : json_encode([]);

        // Decode additional items
        $additional = json_decode($data['additional_items'], true);

        // Calculate additional amount total
        $additionalAmount = 0;
        if (is_array($additional)) {
            foreach ($additional as $item) {
                $additionalAmount += isset($item['amount']) ? floatval($item['amount']) : 0;
            }
        }

        // Store additional amount
        $data['additional_amount'] = $additionalAmount;

        // Calculate total amount (base + additional)
        $data['total_amount'] = floatval($req->amount) + $additionalAmount;

        // Update DB record
        $this->pay->update($id, $data);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully!');
    }

    public function destroy($id)
    {
        $this->pay->find($id)->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully');
    }

    public function reset_record($id)
    {
        $this->pay->updateRecord($id, [
            'amt_paid'    => 0,
            'paid'        => 0,
            'balance'     => 0,
            'paid_months' => json_encode([]),
        ]);

        $this->pay->deleteReceipts(['pr_id' => $id]);
        return back()->with('flash_success', __('msg.update_ok'));
    }


    public function summary(Request $request)
    {
        $studentsQuery = DB::table('users')
            ->join('student_records', 'users.id', '=', 'student_records.user_id')
            ->where('users.user_type', 'student')
            ->select('users.id', 'users.name', 'student_records.my_class_id');

        // Filter by class if selected
        if ($request->filled('class_id')) {
            $studentsQuery->where('student_records.my_class_id', $request->class_id);
        }

        $students = $studentsQuery->get();

        $studentsWithPayments = $students->map(function ($student) {
            // Get class details
            $class = \App\Models\MyClass::find($student->my_class_id);

            $className = $class->name ?? 'N/A';
            $classId   = $class->id ?? 0;

            // Here assuming each class has one fixed fee record in payment_records
            $monthlyFee = DB::table('payments')
                ->where('my_class_id', $classId)
                ->sum('amount'); // if 'amount' holds class fee, you can use ->first() if it’s per record








            //  paid month show code start
            $paymentRecords = $this->pay->getAllMyPR($student->id)->get();

            //  Collect all paid months and total amount
            $paidMonths = collect();
            $totalPaidForMonths = 0;

            foreach ($paymentRecords as $record) {
                $decoded = json_decode($record->paid_months, true);
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }

                if (is_array($decoded)) {
                    $paidMonths = $paidMonths->merge($decoded);
                    $totalPaidForMonths += $record->amt_paid;
                }
            }

            // Remove duplicate months
            $uniqueMonths = $paidMonths->unique()->values()->toArray();

            // Convert to readable text
            $paidMonthsText = implode(', ', $uniqueMonths);

            //  paid month show code end








            // Calculate paid this month dynamically
            $amountPaidThisMonth = DB::table('payment_records')
                ->where('student_id', $student->id)
                ->get()
                ->sum(function ($record) {
                    // Step 1: First decode outer string once
                    $decodedOnce = json_decode($record->paid_months, true);

                    // Step 2: If still string (escaped JSON), decode again
                    if (is_string($decodedOnce)) {
                        $months = json_decode($decodedOnce, true);
                    } else {
                        $months = $decodedOnce;
                    }

                    // Debug check
                    // dd($record->paid_months, $decodedOnce, $months);

                    if (!is_array($months) || empty($months)) {
                        return 0;
                    }

                    $currentMonth = now()->format('F Y'); // Example: October 2025

                    if (in_array($currentMonth, $months)) {
                        return $record->amt_paid / count($months);
                    }

                    return 0;
                });





            // Total paid 
            $totalPaid = DB::table('payment_records')
                ->where('student_id', $student->id)
                ->sum('amt_paid');

            // Pending amount
            $pending = max($monthlyFee - $amountPaidThisMonth, 0);

            // Assign computed values
            $student->class_name = $className;
            $student->class_fee = $monthlyFee;
            $student->paid_this_month = $amountPaidThisMonth;
            $student->pending = $pending;
            $student->total_paid = $totalPaid;
            $student->paid_months_text = $paidMonthsText;


            return $student;
        });

        // Totals
        $totalMonthlyFee = $studentsWithPayments->sum('class_fee');
        $totalPaidThisMonth = $studentsWithPayments->sum('paid_this_month');
        $totalPendingAmount = $studentsWithPayments->sum('pending');

        return view('pages.support_team.payments.summary', [
            'total_fee'          => $totalMonthlyFee,
            'current_month_paid' => $totalPaidThisMonth,
            'pending_amount'     => $totalPendingAmount,
            'students'           => $studentsWithPayments,
            'students_count'     => $studentsWithPayments->count(),
        ]);
    }



    public function fetchStudents(Request $request)
    {
        try {
            $classId = (int) $request->query('class_id', 0);

            // Include 'my_class' relation
            $studentsQuery = $this->student->getRecord([])->with(['user', 'my_class']);

            if ($classId > 0) {
                $studentsQuery->where('my_class_id', $classId);
            }

            $students = $studentsQuery->get();

            return response()->json(
                $students->map(function ($student) {
                    return [
                        'id'             => $student->id,
                        'name'           => $student->user->name ?? 'N/A',
                        'adm_no'         => $student->adm_no ?? '',
                        'photo'          => $student->user->photo ?? 'https://via.placeholder.com/40',
                        'class'          => $student->my_class->name ?? 'N/A',
                        'section'        => $student->section ?? '',
                        'user_id_hashed' => Qs::hash($student->user_id),
                    ];
                })->values()
            );
        } catch (Throwable $exception) {
            return response()->json([
                'error'   => true,
                'message' => 'Server error while fetching students.',
            ], 500);
        }
    }

    public function payAdditional($id, Request $request)
    {
        $paymentId = Qs::decodeHash($id);

        $request->validate([
            'additional_amount' => 'required|numeric|min:0.01',
        ]);


        $payment = DB::table('payments')->where('id', $paymentId)->first();

        if (!$payment) {
            return back()->with('error', 'Payment record not found.');
        }

        $enteredAmount = $request->input('additional_amount');

        if ($enteredAmount > $payment->additional_amount) {
            return back()->with('error', 'Entered amount cannot exceed the total additional payment.');
        }


        DB::table('payments')->where('id', $paymentId)->update([
            'additional_amount' => $payment->additional_amount - $enteredAmount
        ]);

        return back()->with('success', 'Additional payment of ' . number_format($enteredAmount, 2) . ' LKR completed successfully!');
    }
}
