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
 * Admin moderation for student-entered results.
 * Route gating is done in routes/web.php via adminpermission:results.*
 * Master admin (usertype='Admin') bypasses in AdminPermission middleware.
 */
class ResultsController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Moderation queue: all result records with filters. */
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

        // Dashboard-style stat counts.
        $stats = array(
            'total'      => Result::count(),
            'verified'   => Result::where('verified', 1)->count(),
            'unverified' => Result::where('verified', 0)->count(),
            'last7'      => Result::where('created_at', '>=', now()->subDays(7))->count(),
        );

        return view('admin.pages.results.list', compact('page_title', 'list', 'universities', 'stats'));
    }

    /** Full semester-wise view + inline edit form. */
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
        $page_title = 'View Result';
        return view('admin.pages.results.view', compact('page_title', 'result', 'sems', 'cards'));
    }

    /** Admin edits to header fields + subject grades (corrections). */
    public function update(Request $request, $id)
    {
        $result = Result::findOrFail($id);

        $result->student_name   = $request->input('student_name');
        $result->hall_ticket_no = $request->input('hall_ticket_no', $result->hall_ticket_no);
        $result->regulation     = $request->input('regulation');
        $result->degree         = $request->input('degree');
        $result->branch         = $request->input('branch');
        $result->current_cgpa   = $request->input('current_cgpa');
        $result->total_credits  = $request->input('total_credits');
        $result->backlogs_count = (int) $request->input('backlogs_count', 0);
        $result->save();

        // Optional per-subject grade corrections: subjects[subject_id][grade|is_backlog]
        $subjects = $request->input('subjects', array());
        foreach ($subjects as $sid => $vals) {
            $sub = ResultSubject::find($sid);
            if ($sub) {
                if (isset($vals['grade']))      { $sub->grade = $vals['grade']; }
                if (isset($vals['is_backlog'])) { $sub->is_backlog = (int) $vals['is_backlog']; }
                $sub->save();
            }
        }

        // If already verified, regenerate the card so the artifact stays correct.
        if ((int) $result->verified === 1) {
            generate_result_report_card($result);
        }

        \Session::flash('flash_message', 'Result updated');
        return redirect('admin/results/view/' . $id);
    }

    /** Verify -> lock the record + regenerate a badged card. */
    public function verify($id)
    {
        $result = Result::findOrFail($id);
        $result->verified = 1;
        $result->locked   = 1;   // blocks student edits (also enforced in API)
        $result->save();

        generate_result_report_card($result);

        \Session::flash('flash_message', 'Result verified and report card regenerated');
        return redirect('admin/results/view/' . $id);
    }

    /** Un-verify -> unlock + regenerate a plain card (correction escape hatch). */
    public function unverify($id)
    {
        $result = Result::findOrFail($id);
        $result->verified = 0;
        $result->locked   = 0;
        $result->save();

        generate_result_report_card($result);

        \Session::flash('flash_message', 'Result un-verified and unlocked');
        return redirect('admin/results/view/' . $id);
    }

    /** Manual regenerate button. */
    public function regenerate($id)
    {
        $result = Result::findOrFail($id);
        $url = generate_result_report_card($result);
        \Session::flash('flash_message', $url ? 'Report card regenerated' : 'Could not regenerate');
        return redirect('admin/results/view/' . $id);
    }

    /** Delete a junk/spam record and its children. */
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

    /** Report cards manager: all generated artifacts. */
    public function cards()
    {
        $list = ReportCard::orderBy('id', 'DESC')->paginate(20);
        $page_title = 'Report Cards';
        return view('admin.pages.results.cards', compact('page_title', 'list'));
    }
}
