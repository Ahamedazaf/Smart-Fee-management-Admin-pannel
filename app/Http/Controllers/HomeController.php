<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Qs;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Repositories\UserRepo;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected $user;

    public function __construct(UserRepo $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function privacy_policy()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.privacy_policy', $data);
    }

    public function terms_of_use()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.terms_of_use', $data);
    }

    public function dashboard()
    {
        $d = [];

    if (Qs::userIsTeamSAT()) {
    $d['users'] = $this->user->getAll()
        ->where('user_type', 'student'); // only students
}


        $currentYear = Qs::getSetting('current_session');
        $d['total_amount'] = DB::table('student_records as sr')
            ->join('payments as p', function ($join) use ($currentYear) {
                $join->on('sr.my_class_id', '=', 'p.my_class_id')
                    ->where('p.year', '=', $currentYear);
            })
            ->where('sr.session', $currentYear)
            ->sum('p.amount');

        $d['total_paid_till_now'] = DB::table('payment_records')
            ->where('year', $currentYear)
            ->whereNotNull('amt_paid')
            ->sum('amt_paid');

        $d['pending_amount'] = $d['total_amount'] - $d['total_paid_till_now'];

        $d['current_month'] = Carbon::now()->format('F');

        $d['total_classes'] = DB::table('my_classes')->count();

        $d['total_class_types'] = DB::table('class_types')->count();

        $d['total_payment_records'] = DB::table('payment_records')->count();



        // Monthly payment data for chart
        $d['monthly_payments'] = DB::table('payment_records')
            ->select(
                DB::raw('MONTH(created_at) as month_number'),
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('SUM(amt_paid) as total')
            )
            ->where('year', $currentYear)
            ->groupBy('month_number', 'month_name')
            ->orderBy('month_number')
            ->pluck('total', 'month_name');


        return view('pages.support_team.dashboard', $d);
    }
}
