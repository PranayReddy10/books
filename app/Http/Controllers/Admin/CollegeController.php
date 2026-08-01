<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\College;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

class CollegeController extends MainAdminController
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
            $list = College::where("college_name", "LIKE", "%$keyword%")->orderBy('college_name')->paginate(10);
            $list->appends(\Request::only('s'))->links();
        } else {
            $list = College::orderBy('id', 'DESC')->paginate(10);
        }

        $page_title = 'Colleges';

        return view('admin.pages.college.list', compact('page_title', 'list'));
    }

    public function add()
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'Add College';

        return view('admin.pages.college.addedit', compact('page_title'));
    }

    public function edit($page_id)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'Edit College';
        $info = College::findOrFail($page_id);

        return view('admin.pages.college.addedit', compact('page_title', 'info'));
    }

    public function addnew(Request $request)
    {
        $data = \Request::except(array('_token'));

        $rule = array(
            'college_name' => 'required',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        $inputs = $request->all();

        if (!empty($inputs['id'])) {
            $data_obj = College::findOrFail($inputs['id']);
        } else {
            $data_obj = new College;
        }

        $data_obj->college_name = addslashes($inputs['college_name']);
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
            $data_obj = College::findOrFail($post_id);
            $data_obj->delete();

            \Session::flash('flash_message', trans('words.deleted'));
            return redirect()->back();
        } else {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }
    }
}
