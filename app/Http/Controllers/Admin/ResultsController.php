<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Result;
use App\ResultSemester;
use App\ResultSubject;
use App\ReportCard;
use App\University;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

/**
 * Admin moderation + entry for student results.
 * Route gating via adminpermission:results.* ; master admin bypasses.
 */
class ResultsController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Moderation queue with filters + stat counts. */
    public function list()
    {
        $query = Result::query();

        if (!empty($_GET['s'])) {
            $kw = $_GET['s'];
            $query->where(function ($q) use ($kw) {
                $q->where('hall_ticket_no', 'LIKE', "%$kw%")
                  ->orWhere('student_name', 'LIKE', "%$kw%");
            });
        }
        if (isset($_GET['verified']) && $_GET['verified'] !== '') {
            $query->where('verified', (int) $_GET['verified']);
        }
        if (!empty($_GET['regulation'])) {
            $query->where('regulation', $_GET['regulation']);
        }
        if (!empty($_GET['university_id'])) {
            $query->where('university_id', (int) $_GET['university_id']);
        }

        $list = $query->orderBy('id', 'DESC')->paginate(15);
        $list->appends(\Request::except('page'));

        $universities = University::orderBy('university_name')->get();
        $page_title = 'Results';

        $stats = array(
            'total'      => Result::count(),
            'verified'   => Result::where('verified', 1)->count(),
            'unverified' => Result::where('verified', 0)->count(),
            'last7'      => Result::where('created_at', '>=', now()->subDays(7))->count(),
        );

        return view('admin.pages.results.list', compact('page_title', 'list', 'universities', 'stats'));
    }

    /** Full semester-wise view + edit (add/remove semesters & subjects). */
    public function view($id)
    {
        $result = Result::findOrFail($id);
        $result->load('semesters');
        $sems = array();
        foreach ($result->semesters as $sem) {
            $sem->loaded_subjects = $sem->subjects()->get();
            $sems[] = $sem;
        }
        $cards = ReportCard::where('result_id', $id)->orderBy('id', 'DESC')->get();
        $universities = University::orderBy('university_name')->get();
        $users = User::where('usertype', 'User')->orderBy('id', 'DESC')->limit(2000)->get();
        $page_title = 'View Result';
        return view('admin.pages.results.view', compact('page_title', 'result', 'sems', 'cards', 'universities', 'users'));
    }

    /** Add form. */
    public function add()
    {
        $universities = University::orderBy('university_name')->get();
        $users = User::where('usertype', 'User')->orderBy('id', 'DESC')->limit(2000)->get();
        // Details keyed by user id so the form can auto-fill on user selection.
        $userDetails = array();
        foreach ($users as $u) {
            $dept = '';
            if (isset($u->department_id) && $u->department_id) {
                $dept = (string) \App\Department::getDepartmentInfo($u->department_id, 'department_name');
            }
            $userDetails[$u->id] = array(
                'roll'       => (string) (isset($u->rollnumber) ? $u->rollnumber : ''),
                'name'       => (string) $u->name,
                'email'      => (string) $u->email,
                'branch'     => $dept,
                'regulation' => (string) (isset($u->regulation) ? $u->regulation : ''),
                'degree'     => (string) (isset($u->degree) ? $u->degree : ''),
            );
        }
        $page_title = 'Add Result';
        return view('admin.pages.results.add', compact('page_title', 'universities', 'users', 'userDetails'));
    }

    /** Persist a new result + its tree. */
    public function store(Request $request)
    {
        $hall = trim($request->input('hall_ticket_no', ''));
        if ($hall === '') {
            \Session::flash('flash_message', 'Hall ticket number is required');
            return redirect('admin/results/add');
        }
        if (Result::where('hall_ticket_no', $hall)->exists()) {
            \Session::flash('flash_message', 'A result with this hall ticket already exists');
            return redirect('admin/results/add');
        }

        \DB::beginTransaction();
        try {
            $result = new Result();
            $result->hall_ticket_no = $hall;
            $result->user_id        = $request->input('user_id') ?: null;
            $result->university_id  = $request->input('university_id') ?: null;
            $result->student_name   = $request->input('student_name');
            $result->regulation     = $request->input('regulation');
            $result->degree         = $request->input('degree');
            $result->branch         = $request->input('branch');
            $result->backlogs_count = (int) $request->input('backlogs_count', 0);
            $result->source         = 'manual';
            $result->verified       = 0;
            $result->locked         = 0;
            $result->is_public      = 0;
            $result->share_token    = Str::random(24);
            $result->save();

            $totals = $this->saveSemesterTree($request, $result->id);

            // CGPA/credits: use admin override if provided, else computed totals.
            $result->current_cgpa  = ($request->input('current_cgpa') !== null && $request->input('current_cgpa') !== '')
                                        ? $request->input('current_cgpa') : $totals['cgpa'];
            $result->total_credits = ($request->input('total_credits') !== null && $request->input('total_credits') !== '')
                                        ? $request->input('total_credits') : $totals['credits'];
            $result->save();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Session::flash('flash_message', 'Could not save result');
            return redirect('admin/results/add');
        }

        generate_result_report_card($result);
        \Session::flash('flash_message', 'Result added');
        return redirect('admin/results/view/' . $result->id);
    }

    /** Update header + REPLACE the full semester/subject tree. */
    public function update(Request $request, $id)
    {
        $result = Result::findOrFail($id);

        \DB::beginTransaction();
        try {
            $result->student_name   = $request->input('student_name');
            $result->hall_ticket_no = $request->input('hall_ticket_no', $result->hall_ticket_no);
            $result->user_id        = $request->input('user_id') ?: $result->user_id;
            $result->university_id  = $request->input('university_id') ?: $result->university_id;
            $result->regulation     = $request->input('regulation');
            $result->degree         = $request->input('degree');
            $result->branch         = $request->input('branch');
            $result->backlogs_count = (int) $request->input('backlogs_count', 0);
            $result->save();

            // Preserve LOCKED semesters; wipe & rebuild only unlocked ones.
            $unlockedSemIds = ResultSemester::where('result_id', $result->id)
                ->where('locked', 0)->pluck('id')->toArray();
            if (!empty($unlockedSemIds)) {
                ResultSubject::whereIn('result_semester_id', $unlockedSemIds)->delete();
                ResultSemester::whereIn('id', $unlockedSemIds)->delete();
            }

            $lockedCodes = ResultSemester::where('result_id', $result->id)
                ->where('locked', 1)->pluck('sem_code')->map(function ($c) {
                    return strtolower(trim($c));
                })->toArray();

            $totals = $this->saveSemesterTree($request, $result->id, $lockedCodes);

            $result->current_cgpa  = ($request->input('current_cgpa') !== null && $request->input('current_cgpa') !== '')
                                        ? $request->input('current_cgpa') : $totals['cgpa'];
            $result->total_credits = ($request->input('total_credits') !== null && $request->input('total_credits') !== '')
                                        ? $request->input('total_credits') : $totals['credits'];
            $result->save();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Session::flash('flash_message', 'Could not update result');
            return redirect('admin/results/view/' . $id);
        }

        if ((int) $result->verified === 1) {
            generate_result_report_card($result);
        }

        \Session::flash('flash_message', 'Result updated');
        return redirect('admin/results/view/' . $id);
    }

    /**
     * Shared writer for the semester/subject tree from indexed request arrays.
     * Auto-fills grade_points (credits x grade value) when the admin left it
     * blank, and auto-fills each semester's SGPA/credits when left blank.
     * Returns computed overall ['cgpa','credits'].
     */
    private function saveSemesterTree(Request $request, $resultId, $lockedCodes = array())
    {
        $semesters = $request->input('semesters', array());
        $subjects  = $request->input('subjects', array());

        $grandGp = 0.0; $grandCr = 0.0;

        // Seed totals with already-locked semesters so overall CGPA stays correct.
        // grade_points stores Gi (grade value); weight by credits here.
        foreach (ResultSemester::where('result_id', $resultId)->where('locked', 1)->get() as $lsem) {
            foreach ($lsem->subjects()->get() as $lsub) {
                if ($lsub->credits !== null && (float) $lsub->credits > 0) {
                    $lcr = (float) $lsub->credits;
                    $grandCr += $lcr;
                    $grandGp += ($lsub->grade_points !== null ? (float) $lsub->grade_points : 0) * $lcr;
                }
            }
        }

        foreach ($semesters as $i => $s) {
            if (empty($s['sem_code'])) { continue; }
            // Never recreate a locked semester (it was preserved above).
            if (in_array(strtolower(trim($s['sem_code'])), $lockedCodes, true)) { continue; }

            $sem = new ResultSemester();
            $sem->result_id       = $resultId;
            $sem->sem_code        = $s['sem_code'];
            $sem->exam_month_year = isset($s['exam_month_year']) ? $s['exam_month_year'] : '';
            $sem->verified        = 0;
            $sem->locked          = 0;
            $sem->save();

            $rows = isset($subjects[$i]) && is_array($subjects[$i]) ? $subjects[$i] : array();
            $semGp = 0.0; $semCr = 0.0;

            foreach ($rows as $sub) {
                if (empty($sub['subject_code']) && empty($sub['subject_name'])) { continue; }

                $credits = (isset($sub['credits']) && $sub['credits'] !== '') ? (float) $sub['credits'] : null;
                $grade   = isset($sub['grade']) ? $sub['grade'] : '';

                // grade_points now stores Gi (grade value only). Admin override
                // wins, else derive from the grade.
                if (isset($sub['grade_points']) && $sub['grade_points'] !== '') {
                    $gp = (float) $sub['grade_points'];
                } else {
                    $gp = jntuh_subject_grade_points($grade, $credits); // returns Gi
                }

                $row = new ResultSubject();
                $row->result_semester_id = $sem->id;
                $row->subject_code = isset($sub['subject_code']) ? $sub['subject_code'] : '';
                $row->subject_name = isset($sub['subject_name']) ? $sub['subject_name'] : '';
                $row->credits      = $credits;
                $row->grade        = $grade;
                $row->grade_points = $gp;
                $row->internal     = (isset($sub['internal']) && $sub['internal'] !== '') ? $sub['internal'] : null;
                $row->external     = (isset($sub['external']) && $sub['external'] !== '') ? $sub['external'] : null;
                $row->total        = (isset($sub['total']) && $sub['total'] !== '') ? $sub['total'] : null;
                $row->is_backlog   = !empty($sub['is_backlog']) ? 1 : 0;
                $row->save();

                if ($credits !== null && $credits > 0) {
                    $semCr += $credits;
                    $semGp += ($gp !== null ? $gp : 0) * $credits;
                }
            }

            // SGPA/credits: admin override wins, else computed.
            $sem->sgpa = (isset($s['sgpa']) && $s['sgpa'] !== '')
                            ? $s['sgpa']
                            : ($semCr > 0 ? round($semGp / $semCr, 2) : null);
            $sem->credits_earned = (isset($s['credits_earned']) && $s['credits_earned'] !== '')
                            ? $s['credits_earned']
                            : ($semCr > 0 ? $semCr : null);
            $sem->save();

            $grandGp += $semGp;
            $grandCr += $semCr;
        }

        return array(
            'cgpa'    => $grandCr > 0 ? round($grandGp / $grandCr, 2) : null,
            'credits' => $grandCr > 0 ? $grandCr : null,
        );
    }

    public function verify($id)
    {
        $result = Result::findOrFail($id);
        $result->verified = 1;
        $result->locked   = 1;
        $result->save();
        // Cascade: lock every semester.
        ResultSemester::where('result_id', $result->id)->update(['verified' => 1, 'locked' => 1]);
        generate_result_report_card($result);
        \Session::flash('flash_message', 'Result verified and report card regenerated');
        return redirect('admin/results/view/' . $id);
    }

    public function unverify($id)
    {
        $result = Result::findOrFail($id);
        $result->verified = 0;
        $result->locked   = 0;
        $result->save();
        ResultSemester::where('result_id', $result->id)->update(['verified' => 0, 'locked' => 0]);
        generate_result_report_card($result);
        \Session::flash('flash_message', 'Result un-verified and unlocked');
        return redirect('admin/results/view/' . $id);
    }

    /** Verify + lock a SINGLE semester. */
    public function verifySemester($id, $semId)
    {
        $result = Result::findOrFail($id);
        $sem = ResultSemester::where('result_id', $result->id)->where('id', $semId)->firstOrFail();
        $sem->verified = 1;
        $sem->locked   = 1;
        $sem->save();
        // Whole result counts as verified once ANY semester is verified.
        if (!$result->verified) { $result->verified = 1; $result->save(); }
        // Whole-result "locked" is true only when every semester is locked.
        $this->syncResultLock($result);
        generate_result_report_card($result);
        \Session::flash('flash_message', 'Semester ' . $sem->sem_code . ' verified and locked');
        return redirect('admin/results/view/' . $id);
    }

    /** Un-verify + unlock a SINGLE semester. */
    public function unverifySemester($id, $semId)
    {
        $result = Result::findOrFail($id);
        $sem = ResultSemester::where('result_id', $result->id)->where('id', $semId)->firstOrFail();
        $sem->verified = 0;
        $sem->locked   = 0;
        $sem->save();
        // If no semester remains verified, the whole result is unverified.
        $anyVerified = ResultSemester::where('result_id', $result->id)->where('verified', 1)->exists();
        $result->verified = $anyVerified ? 1 : 0;
        $result->save();
        $this->syncResultLock($result);
        generate_result_report_card($result);
        \Session::flash('flash_message', 'Semester ' . $sem->sem_code . ' unlocked');
        return redirect('admin/results/view/' . $id);
    }

    /** results.locked = 1 only when every semester is locked (and >=1 exists). */
    /**
     * One-time recompute for ALL stored results after the grade_points meaning
     * changed to Gi (grade value). For every subject, grade_points is reset to
     * the grade value; then each semester's SGPA/credits and the overall
     * CGPA/credits/backlogs are recomputed with SGPA = sum(Gi*Ci)/sum(Ci).
     */
    public function recomputeAll()
    {
        $count = 0;
        foreach (Result::all() as $result) {
            $grandGp = 0.0; $grandCr = 0.0; $backlogs = 0;
            foreach (ResultSemester::where('result_id', $result->id)->get() as $sem) {
                $semGp = 0.0; $semCr = 0.0;
                foreach ($sem->subjects()->get() as $sub) {
                    // Reset grade_points to Gi (grade value only).
                    $gi = jntuh_grade_value($sub->grade);
                    $sub->grade_points = $gi;   // may be null for unknown grade
                    $sub->save();

                    $cr = ($sub->credits !== null) ? (float) $sub->credits : 0.0;
                    if ($cr > 0) {
                        $g = ($gi !== null) ? (float) $gi : 0.0;
                        $semGp += $g * $cr; $semCr += $cr;
                        $grandGp += $g * $cr; $grandCr += $cr;
                    }
                    if ((int) $sub->is_backlog === 1) { $backlogs++; }
                }
                $sem->sgpa = ($semCr > 0) ? round($semGp / $semCr, 2) : null;
                $sem->credits_earned = ($semCr > 0) ? $semCr : null;
                $sem->save();
            }
            $result->current_cgpa   = ($grandCr > 0) ? round($grandGp / $grandCr, 2) : null;
            $result->total_credits  = ($grandCr > 0) ? $grandCr : null;
            $result->backlogs_count = $backlogs;
            $result->save();
            $count++;
        }
        \Session::flash('flash_message', "Recomputed {$count} result(s) with the corrected grade-point formula.");
        return redirect('admin/results');
    }

    private function syncResultLock($result)
    {
        $total  = ResultSemester::where('result_id', $result->id)->count();
        $locked = ResultSemester::where('result_id', $result->id)->where('locked', 1)->count();
        $result->locked = ($total > 0 && $locked === $total) ? 1 : 0;
        $result->save();
    }

    public function regenerate($id)
    {
        $result = Result::findOrFail($id);
        $url = generate_result_report_card($result);
        \Session::flash('flash_message', $url ? 'Report card regenerated' : 'Could not regenerate');
        return redirect('admin/results/view/' . $id);
    }

    public function delete($id)
    {
        $result = Result::findOrFail($id);
        $semIds = ResultSemester::where('result_id', $id)->pluck('id')->toArray();
        if (!empty($semIds)) {
            ResultSubject::whereIn('result_semester_id', $semIds)->delete();
        }
        ResultSemester::where('result_id', $id)->delete();
        ReportCard::where('result_id', $id)->delete();
        $result->delete();
        \Session::flash('flash_message', 'Result deleted');
        return redirect('admin/results');
    }

    public function cards()
    {
        $list = ReportCard::orderBy('id', 'DESC')->paginate(20);
        $page_title = 'Report Cards';
        return view('admin.pages.results.cards', compact('page_title', 'list'));
    }
}
