<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Books;
use App\Authors;
use App\Category;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

class UserBooksController extends MainAdminController
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

        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'pending';

        $query = Books::whereNotNull('uploaded_by');
        if (in_array($filter, ['pending', 'approved', 'rejected'])) {
            $query->where('upload_status', $filter);
        }
        $list = $query->orderBy('id', 'DESC')->paginate(15);

        $pending_count = Books::whereNotNull('uploaded_by')->where('upload_status', 'pending')->count();

        $page_title = 'User Uploaded Books';

        return view('admin.pages.user_books.list', compact('page_title', 'list', 'filter', 'pending_count'));
    }

    public function approve($id)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $book = Books::findOrFail($id);

        // Set the uploading user as the author (create Authors row if needed).
        if (!empty($book->uploaded_by)) {
            $user = User::find($book->uploaded_by);
            if ($user) {
                $author = Authors::where('name', $user->name)->first();
                if (!$author) {
                    $author = new Authors;
                    $author->name = addslashes($user->name);
                    if (\Schema::hasColumn('authors', 'status')) { $author->status = 1; }
                    $author->save();
                }
                $book->author_ids = $author->id;
            }
        }

        $book->upload_status = 'approved';
        $book->reject_reason = null;
        $book->status = 1; // go live
        $book->save();

        \Session::flash('flash_message', 'Book approved and published.');
        return redirect()->back();
    }

    public function reject(Request $request, $id)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $book = Books::findOrFail($id);
        $book->upload_status = 'rejected';
        $book->reject_reason = $request->input('reject_reason', 'Not approved');
        $book->status = 0; // stays hidden
        $book->save();

        \Session::flash('flash_message', 'Book rejected.');
        return redirect()->back();
    }

    public function delete($id)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $book = Books::findOrFail($id);
        $book->delete();

        \Session::flash('flash_message', trans('words.deleted'));
        return redirect()->back();
    }
}
