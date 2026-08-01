<?php

namespace App\Http\Controllers;

use Auth; 
use App\Pages;
use App\Books;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class PagesController extends Controller
{
	 
 
    public function page_details($page_id,$page_slug)    
    {      
          $page_info = Pages::findOrFail($page_id);
        
          return view('.pages.page',compact('page_info'));
        
    }

    public function share_book($book_id)    
    {      
          $book_info = Books::findOrFail($book_id);

          $view_url = 'book://ebook_app/share/book/' . $book_id;

          $download_url = 'https://play.google.com/store/apps/details?id='.getcong('app_package_name');
        
          return view('.pages.share_book',compact('book_info','view_url','download_url'));
        
    }

    
}
