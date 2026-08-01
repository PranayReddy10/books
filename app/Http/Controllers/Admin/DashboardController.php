<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Category;
use App\SubCategory;
use App\Authors;
use App\Books;
use App\MediaPost;
use App\Department;
use App\College;
use App\Reports;
use App\Transactions;
use App\PostRatings;
use App\PostViewsDownload;
 
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
 
class DashboardController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');
          
         parent::__construct();
          
    }
    public function index()
    { 
            if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', 'Access denied!');

                return redirect('dashboard');
                
             }
           
            $category = Category::count();
            $sub_category = SubCategory::count();
            $authors = Authors::count();
            $books = Books::count();

            $departments_count = Department::count();
            $colleges_count = College::count();

            //Books per department (for chart)
            $books_per_department = Department::select('departments.department_name')
                ->selectRaw('COUNT(book_department.book_id) as book_count')
                ->leftJoin('book_department', 'departments.id', '=', 'book_department.department_id')
                ->groupBy('departments.id', 'departments.department_name')
                ->orderBy('book_count', 'DESC')
                ->take(12)
                ->get();
             
            $users = User::where('usertype','User')->count(); 

            // User growth / activity metrics
            $users_today = User::where('usertype','User')->whereDate('created_at', date('Y-m-d'))->count();
            $users_week  = User::where('usertype','User')->where('created_at', '>=', date('Y-m-d', strtotime('-7 days')))->count();
            $users_month = User::where('usertype','User')->where('created_at', '>=', date('Y-m-d', strtotime('-30 days')))->count();
            // Active subscriptions = users whose exp_date is in the future
            $active_subscribers = User::where('usertype','User')->where('exp_date', '>=', time())->count();
            // Recently updated today (proxy for recent activity; not exact login tracking)
            $recently_active = User::where('usertype','User')->whereDate('updated_at', date('Y-m-d'))->count();

            $transactions = Transactions::count();
            $reviews = PostRatings::count();
            $reports = Reports::count();

            // Media / Posts stats
            $media_total = MediaPost::count();
            $media_pending = MediaPost::where('upload_status','pending')->count();
            $media_approved = MediaPost::where('upload_status','approved')->count();
            $media_photos = MediaPost::where('media_type','photo')->count();
            $media_videos = MediaPost::where('media_type','video')->count();
            // Books awaiting approval (user uploads)
            $books_pending = Books::whereNotNull('uploaded_by')->where('upload_status','pending')->count();
            
            //Trending
            $trending_start_date = date('Y-m-d', strtotime('today - 30 days'));
            $trending_end_date = date('Y-m-d');

            $trending_now = PostViewsDownload::select("post_id","post_type")->whereBetween('date', array(strtotime($trending_start_date), strtotime($trending_end_date)))->selectRaw('SUM(post_views) as total_views')->groupBy('post_id','post_type')->orderby('total_views','DESC')->take(10)->get();
            
            //Latest Books
            $latest_books = Books::where('status',1)->orderby('id','DESC')->take(10)->get();

            $start_date = date('Y-m-d', strtotime('today - 300 days'));
            $end_date = date('Y-m-d');  

            //Top Country
            $top_country= DB::table("analytics")->select("country",DB::raw("COUNT(1) as count_row"))->where('country','!=','')->whereBetween('date', array(strtotime($start_date), strtotime($end_date)))->groupBy(DB::raw("(country)"))->orderby('count_row','DESC')->take(10)->get();

            //dd($top_country);exit;
            
            //Latest Reviews
            $latest_review = PostRatings::orderby('id','DESC')->take(10)->get();

            //Latest Reports
            $reports_list = Reports::orderby('id','DESC')->take(10)->get();

             
            $page_title = trans('words.dashboard_text')?trans('words.dashboard_text'):'Dashboard';
                
            return view('admin.pages.dashboard',compact('page_title','category','sub_category','authors','books','departments_count','colleges_count','books_per_department','users','users_today','users_week','users_month','active_subscribers','recently_active','transactions','reviews','reports','trending_now','latest_books','top_country','latest_review','reports_list','media_total','media_pending','media_approved','media_photos','media_videos','books_pending'));                  
        
    }
	
	 
    	
}
