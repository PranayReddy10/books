<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website (subdomain: read.jntubooks.in -> points to /public)
|--------------------------------------------------------------------------
| Browse free. Reading/downloading a book requires login.
*/
Route::get('/', 'PublicSiteController@home')->name('public.home');
Route::get('/books', 'PublicSiteController@books')->name('public.books');
Route::get('/book/{slug}', 'PublicSiteController@bookDetail')->name('public.book');
Route::get('/feed', 'PublicSiteController@feed')->name('public.feed');
Route::get('/post/{id}', 'PublicSiteController@postDetail')->name('public.post');
Route::get('/account', 'PublicSiteController@account')->middleware('auth')->name('public.account');
Route::get('/read/{slug}', 'PublicSiteController@read')->middleware('auth')->name('public.read');
Route::get('/sitemap.xml', 'PublicSiteController@sitemap');
Route::get('/robots.txt', 'PublicSiteController@robots');
Route::get('/.well-known/assetlinks.json', 'PublicSiteController@assetlinks');



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    
    Route::get('/', 'IndexController@index');

    Route::get('login', [ 'as' => 'admin.login', 'uses' => 'IndexController@index']);
    
    Route::post('login', 'IndexController@postLogin');
    Route::get('logout', 'IndexController@logout');

    Route::get('forgot_password', 'IndexController@forgot_password');
    Route::post('forgot_password', 'IndexController@forgot_password_send');
 
    Route::get('dashboard', 'DashboardController@index');   
    Route::get('profile', 'AdminController@profile');   
    Route::post('profile', 'AdminController@updateProfile');

    Route::get('category', 'CategoryController@list')->middleware('adminpermission:category.view');  
    Route::get('category/add', 'CategoryController@add')->middleware('adminpermission:category.create'); 
    Route::get('category/edit/{id}', 'CategoryController@edit')->middleware('adminpermission:category.edit');  
    Route::post('category/add_edit', 'CategoryController@addnew')->middleware('adminpermission:category.edit');    
    Route::get('category/delete/{id}', 'CategoryController@delete')->middleware('adminpermission:category.delete');

    Route::get('university', 'UniversityController@list')->middleware('adminpermission:university.view');
    Route::get('university/add', 'UniversityController@add')->middleware('adminpermission:university.create');
    Route::get('university/edit/{id}', 'UniversityController@edit')->middleware('adminpermission:university.edit');
    Route::post('university/add_edit', 'UniversityController@addnew')->middleware('adminpermission:university.edit');
    Route::get('university/delete/{id}', 'UniversityController@delete')->middleware('adminpermission:university.delete');
    
    Route::get('results', 'ResultsController@list')->middleware('adminpermission:results.view');
    Route::get('results/view/{id}', 'ResultsController@view')->middleware('adminpermission:results.view');
    Route::post('results/update/{id}', 'ResultsController@update')->middleware('adminpermission:results.edit');
    Route::get('results/verify/{id}', 'ResultsController@verify')->middleware('adminpermission:results.verify');
    Route::get('results/unverify/{id}', 'ResultsController@unverify')->middleware('adminpermission:results.verify');
    Route::get('results/regenerate/{id}', 'ResultsController@regenerate')->middleware('adminpermission:results.regenerate');
    Route::get('results/delete/{id}', 'ResultsController@delete')->middleware('adminpermission:results.delete');
    Route::get('report_cards', 'ResultsController@cards')->middleware('adminpermission:results.view');
    Route::get('results/add',   'ResultsController@add')->middleware('adminpermission:results.edit');
    Route::post('results/store','ResultsController@store')->middleware('adminpermission:results.edit');

    Route::get('department', 'DepartmentController@list')->middleware('adminpermission:department.view');
    Route::get('department/add', 'DepartmentController@add')->middleware('adminpermission:department.create');
    Route::get('department/edit/{id}', 'DepartmentController@edit')->middleware('adminpermission:department.edit');
    Route::post('department/add_edit', 'DepartmentController@addnew')->middleware('adminpermission:department.edit');
    Route::get('department/delete/{id}', 'DepartmentController@delete')->middleware('adminpermission:department.delete');

    Route::get('college', 'CollegeController@list')->middleware('adminpermission:college.view');
    Route::get('college/add', 'CollegeController@add')->middleware('adminpermission:college.create');
    Route::get('college/edit/{id}', 'CollegeController@edit')->middleware('adminpermission:college.edit');
    Route::post('college/add_edit', 'CollegeController@addnew')->middleware('adminpermission:college.edit');
    Route::get('college/delete/{id}', 'CollegeController@delete')->middleware('adminpermission:college.delete');

    Route::get('sub_category', 'SubCategoryController@list')->middleware('adminpermission:category.view');  
    Route::get('sub_category/add', 'SubCategoryController@add')->middleware('adminpermission:category.create'); 
    Route::get('sub_category/edit/{id}', 'SubCategoryController@edit')->middleware('adminpermission:category.edit');  
    Route::post('sub_category/add_edit', 'SubCategoryController@addnew')->middleware('adminpermission:category.edit');    
    Route::get('sub_category/delete/{id}', 'SubCategoryController@delete')->middleware('adminpermission:category.delete');

    Route::get('authors', 'AuthorsController@list')->middleware('adminpermission:authors.view');  
    Route::get('authors/add', 'AuthorsController@add')->middleware('adminpermission:authors.create'); 
    Route::get('authors/edit/{id}', 'AuthorsController@edit')->middleware('adminpermission:authors.edit');  
    Route::post('authors/add_edit', 'AuthorsController@addnew')->middleware('adminpermission:authors.edit');    
    Route::get('authors/delete/{id}', 'AuthorsController@delete')->middleware('adminpermission:authors.delete');
 
    Route::get('books', 'BooksController@list')->middleware('adminpermission:books.view');  

    Route::get('user_books', 'UserBooksController@list')->middleware('adminpermission:books.view');
    Route::get('user_books/approve/{id}', 'UserBooksController@approve')->middleware('adminpermission:user_books.approve');
    Route::get('user_books/reject/{id}', 'UserBooksController@reject')->middleware('adminpermission:user_books.approve');
    Route::get('user_books/delete/{id}', 'UserBooksController@delete')->middleware('adminpermission:user_books.delete');

    Route::get('media', 'MediaPostsController@list')->middleware('adminpermission:media.view');
    Route::get('media/add', 'MediaPostsController@add')->middleware('adminpermission:media.create');
    Route::post('media/store', 'MediaPostsController@store')->middleware('adminpermission:media.create');
    Route::get('media/edit/{id}', 'MediaPostsController@edit')->middleware('adminpermission:media.edit');
    Route::post('media/update/{id}', 'MediaPostsController@update')->middleware('adminpermission:media.edit');
    Route::get('media/toggle_status/{id}', 'MediaPostsController@toggle_status')->middleware('adminpermission:media.edit');
    Route::get('media/approve/{id}', 'MediaPostsController@approve')->middleware('adminpermission:media.approve');
    Route::get('media/reject/{id}', 'MediaPostsController@reject')->middleware('adminpermission:media.approve');
    Route::get('media/delete/{id}', 'MediaPostsController@delete')->middleware('adminpermission:media.delete');

    Route::get('posts', 'PostsController@list')->middleware('adminpermission:posts.view');
    Route::get('posts/edit/{id}', 'PostsController@edit')->middleware('adminpermission:posts.edit');
    Route::post('posts/update/{id}', 'PostsController@update')->middleware('adminpermission:posts.edit');
    Route::get('posts/toggle/{id}/{field}', 'PostsController@toggle')->middleware('adminpermission:posts.edit');
    Route::get('posts/notify/{id}', 'PostsController@notify')->middleware('adminpermission:posts.notify');
    Route::get('posts/delete/{id}', 'PostsController@delete')->middleware('adminpermission:posts.delete');
    Route::get('posts/comments/{id}', 'PostsController@comments')->middleware('adminpermission:posts.view');
    Route::get('posts/comment_toggle/{id}', 'PostsController@comment_toggle')->middleware('adminpermission:posts.edit');
    Route::get('posts/comment_delete/{id}', 'PostsController@comment_delete')->middleware('adminpermission:posts.delete');

    Route::get('users/verify/{id}', 'UsersController@verify_toggle')->middleware('adminpermission:users.verify');
    Route::get('books/add', 'BooksController@add')->middleware('adminpermission:books.create'); 
    Route::get('books/edit/{id}', 'BooksController@edit')->middleware('adminpermission:books.edit');  
    Route::post('books/add_edit', 'BooksController@addnew')->middleware('adminpermission:books.create');
    Route::post('books/edit_save', 'BooksController@edit_save')->middleware('adminpermission:books.edit');    
     
    Route::get('ajax_get_sub_cat/{id}', 'SubCategoryController@ajax_get_sub_cat');
         
    Route::get('home_sections', 'HomeSectionsController@list')->middleware('adminpermission:home_sections.view');  
    Route::get('home_sections/add', 'HomeSectionsController@add')->middleware('adminpermission:home_sections.create'); 
    Route::get('home_sections/edit/{id}', 'HomeSectionsController@edit')->middleware('adminpermission:home_sections.edit');  
    Route::post('home_sections/add_edit', 'HomeSectionsController@addnew')->middleware('adminpermission:home_sections.edit');    
    Route::get('home_sections/delete/{id}', 'HomeSectionsController@delete')->middleware('adminpermission:home_sections.delete');

    Route::get('reviews', 'ReviewsController@list')->middleware('adminpermission:reviews.view');

    Route::get('reports', 'ReportsController@list')->middleware('adminpermission:reports.view');
 
    Route::get('users', 'UsersController@list')->middleware('adminpermission:users.view');   
    Route::get('users/add', 'UsersController@add')->middleware('adminpermission:users.create'); 
    Route::get('users/edit/{id}', 'UsersController@edit')->middleware('adminpermission:users.edit'); 
    Route::post('users/add_edit', 'UsersController@addnew')->middleware('adminpermission:users.edit');   
    Route::get('users/delete/{id}', 'UsersController@delete')->middleware('adminpermission:users.delete');
    Route::get('users/export', 'UsersController@user_export')->middleware('adminpermission:users.view');
    Route::get('users/history/{id}', 'UsersController@user_history')->middleware('adminpermission:users.view');

    Route::get('sub_admin', 'UsersController@admin_list')->middleware('adminpermission:roles.view'); 
    Route::get('sub_admin/add', 'UsersController@admin_add')->middleware('adminpermission:roles.create'); 
    Route::get('sub_admin/edit/{id}', 'UsersController@admin_edit')->middleware('adminpermission:roles.edit');   
    Route::post('sub_admin/add_edit', 'UsersController@admin_addnew')->middleware('adminpermission:roles.edit'); 
    Route::get('sub_admin/delete/{id}', 'UsersController@admin_delete')->middleware('adminpermission:roles.delete');

    Route::get('roles', 'RolesController@list')->middleware('adminpermission:roles.view');
    Route::get('roles/add', 'RolesController@add')->middleware('adminpermission:roles.create');
    Route::get('roles/edit/{id}', 'RolesController@edit')->middleware('adminpermission:roles.edit');
    Route::post('roles/save', 'RolesController@save')->middleware('adminpermission:roles.edit');
    Route::get('roles/delete/{id}', 'RolesController@delete')->middleware('adminpermission:roles.delete');


    Route::get('subscription_plan', 'SubscriptionPlanController@list')->middleware('adminpermission:subscription.view');  
    Route::get('subscription_plan/add', 'SubscriptionPlanController@add')->middleware('adminpermission:subscription.create'); 
    Route::get('subscription_plan/edit/{id}', 'SubscriptionPlanController@edit')->middleware('adminpermission:subscription.edit');  
    Route::post('subscription_plan/add_edit', 'SubscriptionPlanController@addnew')->middleware('adminpermission:subscription.edit');
  
    Route::get('payment_gateway', 'PaymentGatewayController@list')->middleware('adminpermission:payment.view');
    Route::get('payment_gateway/edit/{id}', 'PaymentGatewayController@edit')->middleware('adminpermission:payment.edit');   
    Route::post('payment_gateway/paypal', 'PaymentGatewayController@paypal')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/stripe', 'PaymentGatewayController@stripe')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/razorpay', 'PaymentGatewayController@razorpay')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/paystack', 'PaymentGatewayController@paystack')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/instamojo', 'PaymentGatewayController@instamojo')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/payu', 'PaymentGatewayController@payu')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/mollie', 'PaymentGatewayController@mollie')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/flutterwave', 'PaymentGatewayController@flutterwave')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/paytm', 'PaymentGatewayController@paytm')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/cashfree', 'PaymentGatewayController@cashfree')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/cinetpay', 'PaymentGatewayController@cinetpay')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/banktransfer', 'PaymentGatewayController@banktransfer')->middleware('adminpermission:payment.edit');
    Route::post('payment_gateway/sslcommerz', 'PaymentGatewayController@sslcommerz')->middleware('adminpermission:payment.edit');

    Route::get('transactions', 'TransactionsController@transactions_list')->middleware('adminpermission:transactions.view');
    Route::post('transactions/export', 'TransactionsController@transactions_export')->middleware('adminpermission:transactions.view');  
  
    Route::get('pages', 'PagesController@pages_list')->middleware('adminpermission:pages.view');  
    Route::get('pages/add', 'PagesController@add')->middleware('adminpermission:pages.create'); 
    Route::get('pages/edit/{id}', 'PagesController@edit')->middleware('adminpermission:pages.edit');  
    Route::post('pages/add_edit', 'PagesController@addnew')->middleware('adminpermission:pages.edit');    
    Route::get('pages/delete/{id}', 'PagesController@delete')->middleware('adminpermission:pages.delete');

    Route::get('ad_list', 'AppAdsController@list')->middleware('adminpermission:app_ads.view');
    Route::get('ad_list/edit/{id}', 'AppAdsController@edit')->middleware('adminpermission:app_ads.edit');   
    Route::post('ad_list/admob', 'AppAdsController@admob')->middleware('adminpermission:app_ads.edit');
    Route::post('ad_list/startapp', 'AppAdsController@startapp')->middleware('adminpermission:app_ads.edit');
    Route::post('ad_list/facebook', 'AppAdsController@facebook')->middleware('adminpermission:app_ads.edit');
    Route::post('ad_list/applovins', 'AppAdsController@applovins')->middleware('adminpermission:app_ads.edit');
    Route::post('ad_list/wortise', 'AppAdsController@wortise')->middleware('adminpermission:app_ads.edit');
    Route::post('ad_list/unity', 'AppAdsController@unity')->middleware('adminpermission:app_ads.edit');

    Route::get('general_settings', 'SettingsController@general_settings')->middleware('adminpermission:settings.view');
    Route::post('general_settings', 'SettingsController@update_general_settings')->middleware('adminpermission:settings.edit');
    Route::get('email_settings', 'SettingsController@email_settings')->middleware('adminpermission:settings.view');
    Route::post('email_settings', 'SettingsController@update_email_settings')->middleware('adminpermission:settings.edit');          
    Route::get('onesignal_notification', 'SettingsController@onesignal_notification')->middleware('adminpermission:settings.view');
    Route::post('onesignal_notification', 'SettingsController@update_onesignal_notification')->middleware('adminpermission:settings.edit');
    Route::get('app_update_popup', 'SettingsController@app_update_popup')->middleware('adminpermission:settings.view');
    Route::post('app_update_popup', 'SettingsController@update_app_update_popup')->middleware('adminpermission:settings.edit');
    Route::get('others_settings', 'SettingsController@others_settings')->middleware('adminpermission:settings.view');
    Route::post('others_settings', 'SettingsController@update_others_settings')->middleware('adminpermission:settings.edit');

     
    Route::get('notification_send', 'SettingsController@notification_send')->middleware('adminpermission:notification.view');
    Route::post('notification_send', 'SettingsController@send_android_notification')->middleware('adminpermission:notification.create');

      
    Route::get('verify_purchase_app', 'SettingsController@verify_purchase_app');
    Route::post('verify_purchase_app', 'SettingsController@verify_purchase_app_update');

    Route::get('api_urls', 'SettingsController@api_urls');

    Route::post('ajax_status', 'ActionsController@ajax_status');
    Route::post('ajax_delete', 'ActionsController@ajax_delete');
 
});

//Site

// Root now serves the public website (see PublicSiteController@home at top).
// Old: Route::get('/', 'IndexController@index');
Route::get('page/{id}/{slug}', 'PagesController@page_details');
Route::get('share/book/{id}', 'PagesController@share_book');  

//For App Only
Route::any('app_payu_success', function () {
    return view('app_payu.app_payu_success');
});

Route::any('app_payu_failed', function () {
    return view('app_payu.app_payu_failed');
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');

    $clearCache = Artisan::call('cache:clear');
    echo "Cache cleared. \r\n";

    $setCache = Artisan::call('config:cache');
    echo "Cache configured. \r\n";

    $exitCode = Artisan::call('view:clear');
 
    $exitCode = Artisan::call('route:clear');

    echo "View cache cleared. \r\n";

     
    return '<h1>Cache facade value cleared</h1>';
});
/*
|--------------------------------------------------------------------------
| User Web Registration (department feature)
|--------------------------------------------------------------------------
*/
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

Route::get('/report/{token}', 'PublicSiteController@reportDetail')->name('public.report');

Route::get('signin', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('signin', 'Auth\LoginController@login');
Route::get('signout', 'Auth\LoginController@logout')->name('logout');