<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Department;
use App\University;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

class DepartmentController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        if (isset($_GET['s'])) {
            $keyword = $_GET['s'];
            $list = Department::where("department_name", "LIKE", "%$keyword%")->orderBy('department_name')->paginate(10);
            $list->appends(\Request::only('s'))->links();
        } else {
            $list = Department::orderBy('id', 'DESC')->paginate(10);
        }

        $page_title = 'Departments';

        return view('admin.pages.department.list', compact('page_title', 'list'));
    }

    public function add()
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'Add Department';

        $university_list = University::where('status', 1)->orderBy('university_name')->get();

        return view('admin.pages.department.addedit', compact('page_title', 'university_list'));
    }

    public function edit($page_id)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'Edit Department';
        $info = Department::findOrFail($page_id);
        $university_list = University::where('status', 1)->orderBy('university_name')->get();

        return view('admin.pages.department.addedit', compact('page_title', 'info', 'university_list'));
    }

    public function addnew(Request $request)
    {
        $data = \Request::except(array('_token'));

        $rule = array(
            'department_name' => 'required',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        $inputs = $request->all();

        if (!empty($inputs['id'])) {
            $data_obj = Department::findOrFail($inputs['id']);
        } else {
            $data_obj = new Department;
        }

        $data_obj->university_id = !empty($inputs['university_id']) ? $inputs['university_id'] : null;
        $data_obj->department_name = addslashes($inputs['department_name']);
        $data_obj->status = $inputs['status'];
        $data_obj->save();

        if (!empty($inputs['id'])) {
            \Session::flash('flash_message', trans('words.successfully_updated'));
            return \Redirect::back();
        } else {
            \Session::flash('flash_message', trans('words.added'));
            return \Redirect::back();
        }
    }

    public function delete($post_id)
    {
        if (Auth::User()->usertype == "Admin" OR Auth::User()->usertype == "Sub_Admin") {
            $data_obj = Department::findOrFail($post_id);
            $data_obj->delete();

            \Session::flash('flash_message', trans('words.deleted'));
            return redirect()->back();
        } else {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }
    }
}
