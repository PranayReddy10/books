<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @if(getcong('app_logo'))
  <link rel="shortcut icon" href="{{ URL::asset('/'.getcong('app_logo')) }}">
  @else
  <link rel="shortcut icon" href="{{ URL::asset('site_assets/images/favicon.png') }}">
  @endif
  <title>404</title>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css
" rel="stylesheet">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Arvo&display=swap" rel="stylesheet">
 

  <style type="text/css">
    
/*======================
    404 page
=======================*/


.page_404{ padding:40px 0; background:#fff; font-family: 'Arvo', serif;
}

.page_404  img{ width:100%;}

.four_zero_four_bg{
 
 background-image: url({{ URL::asset('site_assets/images/404.gif') }});
    height: 400px;
    background-position: center;
 }
 
 
 .four_zero_four_bg h1{
 font-size:80px;
 }
 
  .four_zero_four_bg h3{
       font-size:80px;
       }
       
       .link_404{      
  color: #fff!important;
    padding: 10px 20px;
    background: #39ac31;
    margin: 20px 0;
    display: inline-block;}
  .contant_box_404{ margin-top:-50px;}
  </style>
</head>
<body>
<section class="page_404">
  <div class="container">
    <div class="row"> 
    <div class="col-sm-12 ">
    <div class="col-sm-10 col-sm-offset-1  text-center">
    <div class="four_zero_four_bg">
      <h1 class="text-center ">404</h1>
    
    
    </div>
    
    <div class="contant_box_404">
    <h3 class="h2">
    Look like you're lost
    </h3>
    
    <p>the page you are looking for not avaible!</p>
    
    <a href="{{ URL::to('admin/') }}" class="link_404">Go to Home</a>
  </div>
    </div>
    </div>
    </div>
  </div>
</section>
</body>
</html>