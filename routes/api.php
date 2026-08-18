<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1','namespace' => 'API'], function(){
     
    Route::get('/', 'AndroidApiController@index');
    Route::post('app_details', 'AndroidApiController@app_details');
    Route::post('payment_settings', 'AndroidApiController@payment_settings');
    
    Route::post('result_get',      'AndroidApiController@result_get');
    Route::post('result_save',     'AndroidApiController@result_save');
    Route::post('result_fetch',    'AndroidApiController@result_fetch');
    Route::post('hall_ticket_lookup', 'AndroidApiController@hall_ticket_lookup');
    Route::post('report_generate', 'AndroidApiController@report_generate');

    // Shop (MadeForU WooCommerce proxy — read-only; buying happens on the web).
    Route::post('shop_categories',     'ShopController@shop_categories');
    Route::post('shop_products',       'ShopController@shop_products');
    Route::post('shop_product_detail', 'ShopController@shop_product_detail');
    Route::post('shop_links',          'ShopController@shop_links');

    
    Route::post('home', 'AndroidApiController@home');
    Route::post('home_section', 'AndroidApiController@home_section');    
    
    Route::post('continue_read_list', 'AndroidApiController@continue_read_list');
    
    Route::post('trending_books', 'AndroidApiController@trending_books');
    Route::post('latest_books', 'AndroidApiController@latest_books');
      
    Route::post('category', 'AndroidApiController@category');
    Route::post('subcategory', 'AndroidApiController@subcategory');
    Route::post('authors', 'AndroidApiController@authors');
    Route::post('books_by_cat', 'AndroidApiController@books_by_cat'); 
    Route::post('books_by_sub_cat', 'AndroidApiController@books_by_sub_cat');
    Route::post('books_by_author', 'AndroidApiController@books_by_author'); 
    Route::post('author_info', 'AndroidApiController@author_info'); 

    Route::post('books_details', 'AndroidApiController@books_details'); 
    Route::post('books_reviews_list', 'AndroidApiController@books_reviews_list'); 

    Route::post('search_book', 'AndroidApiController@search_book');
    Route::post('filter_book', 'AndroidApiController@filter_book');

    Route::post('all_category', 'AndroidApiController@all_category');
    Route::post('all_authors', 'AndroidApiController@all_authors');
     
    Route::post('post_view', 'AndroidApiController@post_view');
    Route::post('post_download', 'AndroidApiController@post_download');
    Route::post('post_rate', 'AndroidApiController@post_rate');
    Route::post('delete_user_review', 'AndroidApiController@delete_user_review');
    Route::post('post_favourite', 'AndroidApiController@post_favourite');
    Route::post('post_continue_read', 'AndroidApiController@post_continue_read');

  
    Route::post('login', 'AndroidApiController@login');
    Route::post('signup', 'AndroidApiController@signup');
    Route::post('university_list', 'AndroidApiController@university_list');
    Route::post('user_upload_book', 'AndroidApiController@user_upload_book');
    Route::post('my_uploaded_books', 'AndroidApiController@my_uploaded_books');
    Route::post('media_feed', 'AndroidApiController@media_feed');
    Route::post('media_upload', 'AndroidApiController@media_upload');
    Route::post('my_uploaded_media', 'AndroidApiController@my_uploaded_media');
    Route::post('media_delete', 'AndroidApiController@media_delete');
    Route::post('media_view', 'AndroidApiController@media_view');
    Route::post('media_like', 'AndroidApiController@media_like');
    Route::post('media_comment_add', 'AndroidApiController@media_comment_add');
    Route::post('media_comment_list', 'AndroidApiController@media_comment_list');
    Route::post('media_comment_delete', 'AndroidApiController@media_comment_delete');
    Route::post('media_notification_list', 'AndroidApiController@media_notification_list');
    Route::post('media_notification_read', 'AndroidApiController@media_notification_read');
    Route::post('department_list', 'AndroidApiController@department_list');
    Route::post('college_list', 'AndroidApiController@college_list');
    Route::post('social_login', 'AndroidApiController@social_login');
    Route::post('forgot_password', 'AndroidApiController@forgot_password');

    Route::post('profile', 'AndroidApiController@profile');
    Route::post('profile_update', 'AndroidApiController@profile_update');

    Route::post('account_delete', 'AndroidApiController@account_delete');

    Route::post('user_favourite_post_list', 'AndroidApiController@user_favourite_post_list');
    Route::post('user_download_list', 'AndroidApiController@user_download_list');
    Route::post('user_rent_list', 'AndroidApiController@user_rent_list');

    Route::post('user_reports', 'AndroidApiController@user_reports');
    
    Route::post('check_user_plan', 'AndroidApiController@check_user_plan');
    Route::post('subscription_plan', 'AndroidApiController@subscription_plan');
    Route::post('transaction_add', 'AndroidApiController@transaction_add');
    Route::post('transaction_rent_add', 'AndroidApiController@transaction_rent_add');

    Route::post('stripe_token_get', 'AndroidApiController@stripe_token_get');
    Route::post('get_braintree_token', 'AndroidApiController@get_braintree_token');
    Route::post('braintree_checkout', 'AndroidApiController@braintree_checkout');
    Route::post('razorpay_order_id_get', 'AndroidApiController@razorpay_order_id_get');
    Route::post('get_payu_hash', 'AndroidApiController@payumoney_hash_generator');
    Route::post('get_paytm_token_id', 'AndroidApiController@create_paytm_token');
});
