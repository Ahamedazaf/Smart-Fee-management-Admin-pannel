<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Helpers\Mk;
use App\Http\Requests\Student\StudentRecordCreate;
use App\Http\Requests\Student\StudentRecordUpdate;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentRecordController extends Controller
{
    protected $loc, $my_class, $user, $student;

    public function __construct(LocationRepo $loc, MyClassRepo $my_class, UserRepo $user, StudentRepo $student)
    {
        $this->middleware('teamSA', ['only' => ['edit', 'update', 'reset_pass', 'create', 'store', 'graduated']]);
        $this->middleware('super_admin', ['only' => ['destroy',]]);

        $this->loc = $loc;
        $this->my_class = $my_class;
        $this->user = $user;
        $this->student = $student;
    }

    public function reset_pass($st_id)
    {
        $st_id = Qs::decodeHash($st_id);
        $data['password'] = Hash::make('student');
        $this->user->update($st_id, $data);
        return back()->with('flash_success', __('msg.p_reset'));
    }

    public function create()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
        return view('pages.support_team.students.add', $data);
    }

    public function store(StudentRecordCreate $req)
    {

        $data = $req->only(Qs::getUserRecord());
        $sr = $req->only(Qs::getStudentData());

        $ct = null;

        $data['user_type'] = 'student';
        $data['name'] = ucwords($req->name);
        $data['code'] = strtoupper(Str::random(10));
        $data['password'] = Hash::make('student');
        $data['photo'] = Qs::getDefaultUserImage();

        $adm_no = $req->adm_no;
        $adm_no = $req->adm_no ?: mt_rand(1000, 99999);
        $data['username'] = strtoupper(Qs::getAppCode() . '/' . ($ct ?? 'STD') . '/' . $sr['year_admitted'] . '/' . $adm_no);
  
        if ($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath('student') . $data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        $user = $this->user->create($data);
        $sr['adm_no'] = $data['username'];
        $sr['user_id'] = $user->id;
        $sr['session'] = Qs::getSetting('current_session');

        $this->student->createRecord($sr);
        if ($req->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Student added successfully!',
                'redirect' => route('students.create')
            ]);
        }
        return redirect()
            ->route('students.create')
            ->with('success', 'Student added successfully!');
    }

    public function listByClass($class_id)
    {
        $data['my_class'] = $mc = $this->my_class->getMC(['id' => $class_id])->first();
        $data['students'] = $this->student->findStudentsByClass($class_id);
        $data['sections'] = $this->my_class->getClassSections($class_id);

        return is_null($mc) ? Qs::goWithDanger() : view('pages.support_team.students.list', $data);
    }

    public function graduated()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['students'] = $this->student->allGradStudents();

        return view('pages.support_team.students.graduated', $data);
    }

    public function not_graduated($sr_id)
    {
        $d['grad'] = 0;
        $d['grad_date'] = NULL;
        $d['session'] = Qs::getSetting('current_session');
        $this->student->updateRecord($sr_id, $d);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function show($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if (!$sr_id) {
            return Qs::goWithDanger();
        }

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
        if (Auth::user()->id != $data['sr']->user_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild($data['sr']->user_id, Auth::user()->id)) {
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return view('pages.support_team.students.show', $data);
    }

    public function edit($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if (!$sr_id) {
            return Qs::goWithDanger();
        }

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
        return view('pages.support_team.students.edit', $data);
    }

    public function update(StudentRecordUpdate $req, $sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if (!$sr_id) {
            return Qs::goWithDanger();
        }

        $sr = $this->student->getRecord(['id' => $sr_id])->first();
        if (!$sr) {
            return response()->json(['message' => 'Student record not found.'], 404);
        }

        $req->merge([
            'my_parent_id' => $req->input('my_parent_id', $sr->my_parent_id),
            'my_class_id'  => $req->input('my_class_id',  $sr->my_class_id),
            'section_id'   => $req->input('section_id',   $sr->section_id),
            'grad_date'    => $req->input('grad_date',    $sr->grad_date),
            'year_admitted' => $req->input('year_admitted', $sr->year_admitted ?? date('Y')),
        ]);

        $d = $req->only(Qs::getUserRecord());
        $d['name'] = ucwords($req->name);
        if ($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(
                Qs::getUploadPath('student') . $sr->user->code,
                $f['name']
            );
            $d['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($sr->user->id, $d);
        $srec = $req->only(Qs::getStudentData());

        // Extra protection: ensure no undefined keys
        $srec['my_parent_id'] = $srec['my_parent_id'] ?? $sr->my_parent_id ?? null;
        $srec['my_class_id']  = $srec['my_class_id']  ?? $sr->my_class_id  ?? null;
        $srec['section_id']   = $srec['section_id']   ?? $sr->section_id   ?? null;
        $srec['grad_date']    = $srec['grad_date']    ?? $sr->grad_date    ?? null;
        $srec['year_admitted'] = $srec['year_admitted'] ?? $sr->year_admitted ?? date('Y');

        $this->student->updateRecord($sr_id, $srec);
        if (!empty($srec['my_class_id'])) {
            Mk::deleteOldRecord($sr->user->id, $srec['my_class_id']);
        }
        return Qs::jsonUpdateOk();
    }




    public function destroy($st_id)
    {
        $st_id = Qs::decodeHash($st_id);
        if (!$st_id) {
            return Qs::goWithDanger();
        }

        $sr = $this->student->find($st_id);
        if (!$sr) {
            return back()->with('flash_danger', 'Student record not found!');
        }

        $path = Qs::getUploadPath('student') . $sr->user->code;
        if (Storage::exists($path)) {
            Storage::deleteDirectory($path);
        }

        $this->user->delete($sr->user->id);

        return back()->with('success', __('msg.del_ok'));
    }


    /** Student Info (All Students + Classes) */

    public function studentinfo()
    {
        $students = $this->student->all();
        foreach ($students as $student) {
            $student->adm_no = Str::after($student->adm_no, '//');
        }

        $data['students'] = $students;
        $data['my_classes'] = $this->my_class->all();

        return view('pages.support_team.students.studentinfo', $data);
    }
}

    /** End Student Info (All Students + Classes) */