<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Category;
use App\SubCategory;
use App\Authors;
use App\Books;
use App\Department;
use App\Favourite;
use App\PostRatings;
use App\Reports;
use App\PostViewsDownload;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 

class BooksController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');
          
    }

    public function list()
    { 
 
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
        }

        if(isset($_GET['s']))
        {
            $keyword = $_GET['s'];  
            $list = Books::where("title", "LIKE","%$keyword%")->orderBy('title')->paginate(12);

            $list->appends(\Request::only('s'))->links();
        }
        else if(isset($_GET['filter']))
        {
            
            if($_GET['filter']=="Slider")
            {
                $list = Books::where("featured", "1")->orderBy('id','DESC')->paginate(12);      
            }
            else if($_GET['filter']=="Paid")
            {
                $list = Books::where("book_access", "Paid")->orderBy('id','DESC')->paginate(12);      
            }   
            else if($_GET['filter']=="Free")
            {
                $list = Books::where("book_access", "Free")->orderBy('id','DESC')->paginate(12);      
            }
            else if($_GET['filter']=="Active")
            {
                $list = Books::where("status",1)->orderBy('id','DESC')->paginate(12);      
            }
            else if($_GET['filter']=="Inactive")
            {
                $list = Books::where("status", 0)->orderBy('id','DESC')->paginate(12);      
            } 
            else
            {
                $list = Books::orderBy('id','DESC')->paginate(12);      
            }
                 
            $list->appends(request()->input())->links();
        } 
        else if(isset($_GET['cat_id']) AND isset($_GET['author_id']))
        {
           
            $cat_id = $_GET['cat_id'];  
            $author_id = $_GET['author_id']; 
            
            $list = Books::when($cat_id, function ($q) use ($cat_id) {
                return $q->where('cat_id',$cat_id);
            })             
            ->when($author_id, function ($q) use ($author_id) {
                return $q->whereRaw("find_in_set('$author_id',author_ids)");
            })             
            ->orderBy('id','DESC')->paginate(12);
              
            $list->appends(request()->input())->links();
        }        
        else
        {
            $list = Books::orderBy('id','DESC')->paginate(12);

        }

        $cat_list = Category::orderBy('category_name')->get();
        $authors_list = Authors::orderBy('name')->get();

        $page_title=trans('words.books_text');

          
        return view('admin.pages.books.list',compact('page_title','list','cat_list','authors_list'));
    }

    public function add()    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.add_book');

          $cat_list = Category::orderBy('category_name')->get();
          $sub_cat_list = SubCategory::orderBy('sub_category_name')->get();
          $authors_list = Authors::orderBy('name')->get();
          $department_list = Department::where('status',1)->orderBy('department_name')->get();

          return view('admin.pages.books.addedit',compact('page_title','cat_list','sub_cat_list','authors_list','department_list'));
        
    }

    public function edit($page_id)    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.edit_book');

          $info = Books::findOrFail($page_id);

          $cat_list = Category::orderBy('category_name')->get();
          $sub_cat_list = SubCategory::where('cat_id',$info->cat_id)->orderBy('sub_category_name')->get();
          $authors_list = Authors::orderBy('name')->get();
          $department_list = Department::where('status',1)->orderBy('department_name')->get();
          $selected_departments = $info->departments()->pluck('departments.id')->toArray();

          return view('admin.pages.books.addedit',compact('page_title','info','cat_list','sub_cat_list','authors_list','department_list','selected_departments'));
        
    }

    public function addnew(Request $request)
    {  
       
       $data =  \Request::except(array('_token')) ;
       
       if(!empty($inputs['id'])){
                
            $rule=array(
            'title' => 'required',
            'category' => 'required'           
                 );
        }
        else
        {
            $rule=array(
                'title' => 'required',
                'category' => 'required'
                    );
            // Cover required on create: either an elFinder path or a DigitalOcean upload,
            // UNLESS the admin chose to auto-generate a cover.
            $auto_cover = isset($data['use_custom_image']) && $data['use_custom_image']=='0';
            if (!$auto_cover && empty($data['book_image']) && !$request->hasFile('book_image_do')) {
                $rule['book_image'] = 'required';
            }
        }

        
        $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
        $inputs = $request->all();

        if(!empty($inputs['id'])){
           
            $data_obj = Books::findOrFail($inputs['id']);

        }else{

            $data_obj = new Books;

        }         
        
        $author_ids= isset($inputs['author_ids'])?implode(',', $inputs['author_ids']):'';

        $data_obj->book_access = $inputs['book_access']; 
        $data_obj->title = addslashes($inputs['title']);
        $data_obj->description = addslashes($inputs['description']);
        $data_obj->cat_id = $inputs['category']; 
        $data_obj->sub_cat_id = $inputs['sub_category']; 
        $data_obj->author_ids = $author_ids;
        // Cover image: an uploaded DigitalOcean file overrides the elFinder field.
        if ($request->hasFile('book_image_do')) {
            $cover = $request->file('book_image_do');
            $coverName = Str::slug(substr($inputs['title'], 0, 40), '-') . '-' . md5(time()) . '-cover.jpg';
            $img = Image::make($cover)->fit(400, 550)->encode('jpg', 85);
            $coverKey = 'images/books/' . $coverName;
            Storage::disk('spaces')->put($coverKey, (string) $img, 'public');
            $data_obj->image = Storage::disk('spaces')->url($coverKey);
        } elseif (isset($inputs['use_custom_image']) && $inputs['use_custom_image']=='1' && !empty($inputs['book_image'])) {
            $data_obj->image = $inputs['book_image'];
        } elseif (isset($inputs['use_custom_image']) && $inputs['use_custom_image']=='0') {
            // Auto cover: no file generated. Leave image empty so the display layer
            // renders a live gradient (category color) + title. Editing the title
            // updates the cover automatically since nothing is baked.
            $data_obj->image = '';
        } elseif (!empty($inputs['book_image'])) {
            $data_obj->image = $inputs['book_image'];
        }

        // Content type: book (default) or video (YouTube).
        $content_type = isset($inputs['content_type']) ? $inputs['content_type'] : 'book';
        $data_obj->content_type = $content_type;

        if ($content_type == 'video') {
            // Video: store the YouTube URL, mark url_type youtube. No file upload.
            $data_obj->url_type = 'youtube';
            $data_obj->url = isset($inputs['youtube_url']) ? trim($inputs['youtube_url']) : '';
            $data_obj->download_enable = 0;
            // If no custom cover was set, leave image empty; the display layer
            // (coverUrl / public partial) falls back to the YouTube thumbnail.
        }
        else
        {
        // Book file / URL.
        if($inputs['url_type']=="server_url")
        {
            $data_obj->url_type = 'server_url';
            $data_obj->url = $inputs['book_url_server'];
        }
        elseif($inputs['url_type']=="digitalocean")
        {
            // Upload the book file to DigitalOcean Spaces; store its full CDN URL.
            if ($request->hasFile('book_file_do')) {
                $do_file = $request->file('book_file_do');
                $ext = strtolower($do_file->getClientOriginalExtension());
                if (in_array($ext, array('pdf', 'epub'))) {
                    $doName = Str::slug(substr($inputs['title'], 0, 40), '-') . '-' . md5(time()) . '.' . $ext;
                    $data_obj->url = spaces_upload($do_file, 'books', $doName);
                }
            }
            // Stored value is a full URL, so treat as server_url for the reader.
            $data_obj->url_type = 'server_url';
        }
        else
        {
            $data_obj->url_type = 'local';
            $data_obj->url = $inputs['book_url_local'];
        }

        if(isset($inputs['download_enable']))
         {
            $data_obj->download_enable = $inputs['download_enable'];  
          }
        }

        $data_obj->book_on_rent = $inputs['book_on_rent'];

        if($inputs['book_on_rent']==1)
        {
            $data_obj->book_rent_price = $inputs['book_rent_price'];
            $data_obj->book_rent_time = $inputs['book_rent_time'];
        }

        $data_obj->status = $inputs['status']; 
        $data_obj->rewarded_ad = isset($inputs['rewarded_ad']) ? $inputs['rewarded_ad'] : 0;
        
        $data_obj->save();

        $department_ids = isset($inputs['department_ids']) ? $inputs['department_ids'] : [];
        $data_obj->departments()->sync($department_ids);
 
        if(!empty($inputs['id'])){

            \Session::flash('flash_message', trans('words.successfully_updated'));

            return \Redirect::back();
        }else{

            \Session::flash('flash_message', trans('words.added'));

            return \Redirect::back();

        }   
    }
  
}
