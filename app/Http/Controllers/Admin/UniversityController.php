<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\University;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

class UniversityController extends MainAdminController
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
            $list = University::where("university_name", "LIKE", "%$keyword%")->orderBy('university_name')->paginate(10);
            $list->appends(\Request::only('s'))->links();
        } else {
            $list = University::orderBy('id', 'DESC')->paginate(10);
        }

        $page_title = 'Universitys';

        return view('admin.pages.university.list', compact('page_title', 'list'));
    }

    public function add()
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'Add University';

        return view('admin.pages.university.addedit', compact('page_title'));
    }

    public function edit($page_id)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'Edit University';
        $info = University::findOrFail($page_id);

        return view('admin.pages.university.addedit', compact('page_title', 'info'));
    }

    public function addnew(Request $request)
    {
        $data = \Request::except(array('_token'));

        $rule = array(
            'university_name' => 'required',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        $inputs = $request->all();

        if (!empty($inputs['id'])) {
            $data_obj = University::findOrFail($inputs['id']);
        } else {
            $data_obj = new University;
        }

        $data_obj->university_name = addslashes($inputs['university_name']);
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
            $data_obj = University::findOrFail($post_id);
            $data_obj->delete();

            \Session::flash('flash_message', trans('words.deleted'));
            return redirect()->back();
        } else {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }
    }
}
