<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>{{stripslashes($page_info->page_title)}} - {{getcong('app_name')}}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">  
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800&display=swap" rel="stylesheet">   
<style type="text/css">
body {
    color: #4c4d4d;
    font-family: "Nunito", sans-serif;
    font-size: 15px;
    line-height: 1.6;
    font-weight: 600;
}
strong{
    font-weight: 800;
}
* {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}
h1, h2, h3, h4, h5, h6{
    width: 100%;
    text-align: left;
}
h1{
    font-size: 42px;
    font-weight: 800;
}
h2{
    font-size: 34px;
    font-weight: 700;
}
h3{
    font-size: 26px;
    font-weight: 700;
}
h4{
    font-size: 20px;
    font-weight: 700;
}
ul{
    padding-left: 20px;
    margin-bottom: 25px;
}
ul li{
    font-size: 18px;
    font-weight: 700;
    line-height: 1.7;
}
.policy_logo {
    background: #1a2234;
    padding:5px 10px;
    border-radius: 6px;
    display: inherit;
    margin-bottom: 20px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
}
.policy_logo a{
    display: inherit;
}
.about {
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.about p {
    font-size: 17px;
    line-height: 1.7;
    font-weight: 600;
    margin-bottom: 15px;
}
.about_div {
    width: 90%;
    margin-top: 1rem;
    max-width: 900px;
    display: flex;
    flex-flow: row wrap;
    justify-content: center;
    align-items: flex-start;
    gap: 0.5rem;
}
span, p, section, select, small, style, sub, sup, td, title, article, cite, strong, b, code, del, dd, dl, dt, em, ol, ul{
    width: 100%;
    text-align: left;
}
</style>
</head>
<body>

<section class="about">
<div class="about_div">
    <div class="policy_logo">
        <a href="#"><img src="{{ URL::asset('/'.getcong('app_logo')) }}" alt="logo" width="150"></a>
    </div>
    <h2>{{stripslashes($page_info->page_title)}}</h2>
    
    <p>{!!stripslashes($page_info->page_content)!!}</p>

</div>  
</section>
</body>
</html>   