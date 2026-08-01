@extends("admin.admin_app")

@section("content")
 <?php $base_url=\URL::to('/').'/api/v1/';?>
  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-xl-12">
              <div class="card-box" style="color: #a9b7c6;">
                <h3 style="font-weight: 600;">APIs</h3>
                 <code style="color: #a9b7c6;background: #2b3751;padding: 5px 10px;font-size: 14px;font-weight: 600;"> API URL  {{$base_url}}</code><br/><br/>
                  <b>App Details:</b>  (Method: app_details)<br/><br/>
  
                  <br/>
                  <b>Payment Settings:</b> (Method: payment_settings) <br/><br/>

                  <b>Home:</b> (Method: home) (Parameter: user_id)<br/><br/>
                  <b>Home Single:</b> (Method: home_section) (Parameter: id, user_id)<br/><br/>                 
                  
                  <b>Continue Read List:</b> (Method: continue_read_list) (Parameter: user_id)  <br/><br/>

                  <b>Trending Books:</b> (Method: trending_books) (Parameter: user_id)  <br/><br/>
                  <b>Latest Books:</b> (Method: latest_books) (Parameter: user_id)  <br/><br/>

                  <b>Category List:</b> (Method: category) <br/><br/>
                  <b>Sub Category List:</b> (Method: subcategory) (Parameter: cat_id)  <br/><br/>
                  <b>Authors List:</b> (Method: authors) <br/><br/>
                  <b>Books by Category:</b> (Method: books_by_cat) (Parameter: cat_id, user_id)  <br/><br/>
                  <b>Books by Sub Category:</b> (Method: books_by_sub_cat) (Parameter: sub_cat_id, user_id)  <br/><br/>
                  <b>Books by Author:</b> (Method: books_by_author) (Parameter: author_id, user_id)  <br/><br/>

                  <b>Author Info:</b> (Method: author_info) (Parameter: author_id, user_id)  <br/><br/>

                  <b>Books Details:</b> (Method: books_details) (Parameter: book_id, user_id)  <br/><br/>

                  <b>Search Book:</b> (Method: search_book) (Parameter: search_text, user_id)  <br/><br/>

                  <b>Filter Book:</b> (Method: filter_book) (Parameter: filter_type(sort_by, author_by, category_by),filter_val(sort_by(Popularity,Ratings,NewArrival), author_by(author comma separated), category_by(category comma separated)))  <br/><br/>

                  <b>All Category List:</b> (Method: all_category) <br/><br/>
                  <b>All Authors List:</b> (Method: all_authors) <br/><br/>

                  <b>Books Reviews List:</b> (Method: books_reviews_list) (Parameter: book_id)  <br/><br/>
 
                  <b>Post Views:</b> (Method: post_view) (Parameter: post_id, post_type(Book)) <br/><br/>
                  <b>Post Download:</b> (Method: post_download) (Parameter: user_id, post_id, post_type(Book))<br/><br/>
                  <b>Post Rate:</b> (Method: post_rate) (Parameter: user_id, post_id, rate, post_type(Book), review_text)<br/><br/>
                  <b>Delete User Review:</b> (Method: delete_user_review) (Parameter: review_id)<br/><br/>
                  <b>Post Favourite:</b> (Method: post_favourite) (Parameter: user_id, post_id,post_type(Book))<br/><br/>
                  <b>Post Continue Read:</b> (Method: post_continue_read) (Parameter: user_id, post_id, page_num)<br/><br/>       
                    
                  <b>Login:</b> (Method: login) (Parameter: email, password)<br/><br/>
                  <b>Signup:</b> (Method: signup) (Parameter: name, email, password, phone)<br/><br/>
                  <b>Social Login:</b> (Method: social_login) (Parameter: login_type(google or facebook), social_id, name, email)<br/><br/>
                  <b>Forgot Password:</b> (Method: forgot_password) (Parameter: email)<br/><br/>
                  <b>Profile:</b> (Method: profile) (Parameter: user_id)<br/><br/>
                  <b>Profile Update:</b> (Method: profile_update) (Parameter: user_id)<br/><br/>
                  <b>User Favourite Book:</b> (Method: user_favourite_post_list) (Parameter: user_id)<br/><br/>
                  <b>User Download List:</b> (Method: user_download_list) (Parameter: user_id)  <br/><br/>
                  <b>User Rent List:</b> (Method: user_rent_list) (Parameter: user_id)  <br/><br/>
                  <b>User Report:</b> (Method: user_reports) (Parameter: user_id, post_id, post_type(Book), review_id, message)<br/><br/>

                  <b>Check User Plan:</b> (Method: check_user_plan) (Parameter: user_id)<br/><br/>
                  <b>Subscription Plan:</b> (Method: subscription_plan) <br/><br/>
                  <b>Transaction Add:</b> (Method: transaction_add) (Parameter: plan_id, user_id, payment_id, payment_gateway) <br/><br/>
                  
                  <b>Transaction Add:</b> (Method: transaction_rent_add) (Parameter: rent_id, user_id, payment_id, payment_gateway) <br/><br/>
                  
                  <b>Stripe Token Get:</b> (Method: stripe_token_get) (Parameter: amount)<br/><br/>
                  <b>Braintree Token Get:</b> (Method: get_braintree_token)<br/><br/>
                  <b>Braintree Checkout:</b> (Method: braintree_checkout) (Parameter: payment_amount, payment_nonce)<br/><br/>
                  <b>Razorpay Order ID Get:</b> (Method: razorpay_order_id_get) (Parameter: amount,user_id)<br/><br/>
                  <b>Payu Hash Get:</b> (Method: get_payu_hash) (Parameter: hashdata)<br/><br/>
                  <b>Paytm Token Get:</b> (Method: get_paytm_token_id) (Parameter: amount,user_id)<br/><br/>

                  <b>Account Delete:</b> (Method: account_delete) (Parameter: user_id)<br/><br/>
              </div>
 
            </div>
          </div>
        </div>
      </div>
      @include("admin.copyright") 
    </div>

@endsection