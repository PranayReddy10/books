<?php
use App\Settings;
use App\Pages;
use App\PostViewsDownload;
use App\Favourite;
use App\Analytics;
use App\PostRatings;
use App\PaymentGateway;
use App\User;
use App\RentInfo;
use App\ContinueRead;

if (! function_exists(function: 'remove_from_string')) {
    function remove_from_string($str, $item) 
    { 
        $parts = explode(',', $str);

        while(($i = array_search($item, $parts)) !== false) {
            unset($parts[$i]);
        }
    
        return implode(',', $parts);
    }
    
}

if (!function_exists('get_book_continue_page_num')) {
    function get_book_continue_page_num($post_id,$user_id)
    {
            $continue_info = ContinueRead::where('post_id', '=', $post_id)->where('user_id', '=', $user_id)->first();

			if($continue_info)
			{
				return  $continue_info->page_num;
			}
			else
			{
				return  '';
			}

     }
}

if (! function_exists('check_on_rent')) {

    function check_on_rent($user_id,$rent_id,$type)
    {

        $rent_user_info=RentInfo::where('user_id',$user_id)->where('rent_id',$rent_id)->where('rent_type',$type)->orderBy('id', 'desc')->first();
        
         //echo date('d-m-Y',$rent_user_info->rent_exp_date);exit;

        if($rent_user_info AND $rent_user_info->rent_exp_date > strtotime(date('m/d/Y')))
        {

                return true;
        }
        else
        {
                return false;
        }
         
    }
}

if (! function_exists('check_app_user_plan')) {

    function check_app_user_plan($user_id)
    {

        // $user_id=$get_data['user_id'];

        $user_info = User::findOrFail($user_id);
        $user_plan_id=$user_info->plan_id;
        $user_plan_exp_date=$user_info->exp_date;
 

        if($user_plan_id==0)
        {          
             return false;
        }
        else if(strtotime(date('m/d/Y'))>$user_plan_exp_date)
        {

                return false;
        }
        else
        {
                return true;
        }
         
    }
}

if (! function_exists('getPaymentGatewayInfo')) {
function getPaymentGatewayInfo($id,$field_name=null)
{ 
 
    $gateway_obj= PaymentGateway::find($id); 

    if(isset($field_name))
    {
        $gateway_info=json_decode($gateway_obj->gateway_info);

        //echo $gateway_info->status;
        //exit;

        return $gateway_info->$field_name;
    }
    else
    { 
        return $gateway_obj;
    }
     
}
}

if (!function_exists('post_total_reviews_count')) {
    function post_total_reviews_count($post_id,$post_type)
    {
            $view_count = PostRatings::where('post_id', '=', $post_id)->where('post_type', '=', $post_type)->count();

            return $view_count;
    }
}
 
if (!function_exists('check_user_rating')) {
    function check_user_rating($post_type,$post_id,$user_id=null)
    {       
        if($user_id)
        {
             $rate_obj = PostRatings::where('post_type', '=', $post_type)->where('post_id', '=', $post_id)->where('user_id', '=', $user_id)->first();

             if($rate_obj)
             {
                return true;
             }
             else
             {
                return false;
             }
        }
        else
        {
            return false;
        }
          
    }
}

if (!function_exists('check_favourite')) {
    function check_favourite($post_type,$post_id,$user_id=null)
    {       
        if($user_id)
        {
             $fav_obj = Favourite::where('post_type', '=', $post_type)->where('post_id', '=', $post_id)->where('user_id', '=', $user_id)->first();

             if($fav_obj)
             {
                return true;
             }
             else
             {
                return false;
             }
        }
        else
        {
            return false;
        }
          
    }
}

if (!function_exists('post_views_count')) {
    function post_views_count($post_id,$post_type)
    {
            $view_count = PostViewsDownload::where('post_id', '=', $post_id)->where('post_type', '=', $post_type)->sum('post_views');

            return $view_count;
    }
}

if (!function_exists('post_views_save')) {
    function post_views_save($post_id,$post_type,$user_id=null)
    {       

           $today_date=  strtotime(date('m/d/Y'));

        $view_info = PostViewsDownload::where('post_id', '=', $post_id)->where('post_type', '=', $post_type)->where('date', '=', $today_date)->first();   


        if($view_info)
        { 
            $view_obj = PostViewsDownload::findOrFail($view_info->id);        
            $view_obj->increment('post_views');     
            $view_obj->save();
             
        }
        else
        {
            $view_obj = new PostViewsDownload;

            $view_obj->post_id = $post_id;
            $view_obj->post_type = $post_type;
            $view_obj->post_views = 1;
            $view_obj->date = $today_date;
            $view_obj->save();
        }
 
    }
}

if (!function_exists('post_download_count')) {
    function post_download_count($post_id,$post_type)
    {
            $view_count = PostViewsDownload::where('post_id', '=', $post_id)->where('post_type', '=', $post_type)->sum('post_download');

            return $view_count;
    }
}

if (!function_exists('post_download_save')) {
    function post_download_save($post_id,$post_type,$user_id=null)
    {       

           $today_date=  strtotime(date('m/d/Y'));

        $view_info = PostViewsDownload::where('post_id', '=', $post_id)->where('post_type', '=', $post_type)->where('date', '=', $today_date)->first();   


        if($view_info)
        { 
            $view_obj = PostViewsDownload::findOrFail($view_info->id);        
            $view_obj->increment('post_download');     
            $view_obj->save();
             
        }
        else
        {
            $view_obj = new PostViewsDownload;

            $view_obj->post_id = $post_id;
            $view_obj->post_type = $post_type;
            $view_obj->post_download = 1;
            $view_obj->date = $today_date;
            $view_obj->save();
        }
 
    }
}


if (! function_exists('number_format_short')) {
function number_format_short( $n, $precision = 1 ) {
    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }

  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }

    return $n_format . $suffix;
}
}



if (! function_exists('putPermanentEnv')) {

 function putPermanentEnv($key, $value)
{
    $path = app()->environmentFilePath();

    $escaped = preg_quote('='.env($key), '/');

    file_put_contents($path, preg_replace(
        "/^{$key}{$escaped}/m",
        "{$key}={$value}",
        file_get_contents($path)
    ));
}

}

if (! function_exists('getcong')) {

    function getcong($key)
    {
    	//echo "string";exit;

        //if(file_exists(base_path('/public/.lic')))
       // { 
            $settings = Settings::findOrFail('1'); 

            return $settings->$key;
       // }
    }
}

if (!function_exists('alreadyInstalled')) {
    function alreadyInstalled()
    {
            return file_exists(base_path('/public/.lic'));

     }
}

 
//Site

if (!function_exists('classActivePathSite')) {
    function classActivePathSite($path)
    {
        $path = explode('.', $path);
        $segment = 1;
        foreach($path as $p) {
            if((request()->segment($segment) == $p) == false) {
                return '';
            }
            $segment++;
        }
        return ' active';
    }
} 

//Admin
if (!function_exists('classActivePath')) {
    function classActivePath($path)
    {
        $path = explode('.', $path);
        $segment = 2;
        foreach($path as $p) {
            if((request()->segment($segment) == $p) == false) {
                return '';
            }
            $segment++;
        }
        return ' active';
    }
}

if (!function_exists('classActivePathSub')) {
    function classActivePathSub($path)
    {
        $path = explode('.', $path);
        $segment = 2;
        foreach($path as $p) {
            if((request()->segment($segment) == $p) == false) {
                return '';
            }
            $segment++;
        }
        return ' subdrop';
    }
}

if (!function_exists('classActivePathSub_Style')) {
    function classActivePathSub_Style($path)
    {
        $path = explode('.', $path);
        $segment = 2;
        foreach($path as $p) {
            if((request()->segment($segment) == $p) == false) {
                return '';
            }
            $segment++;
        }
        return 'display: block;';
    }
}

if (!function_exists('classActivePathSite')) {
    function classActivePathSite($path)
    {
        $path = explode('.', $path);
        $segment = 1;
        foreach($path as $p) {
            if((request()->segment($segment) == $p) == false) {
                return '';
            }
            $segment++;
        }
        return 'active';
    }
}

if (!function_exists('generate_timezone_list')) {
function generate_timezone_list()
{
    static $regions = array(
        DateTimeZone::AFRICA,
        DateTimeZone::AMERICA,
        DateTimeZone::ANTARCTICA,
        DateTimeZone::ASIA,
        DateTimeZone::ATLANTIC,
        DateTimeZone::AUSTRALIA,
        DateTimeZone::EUROPE,
        DateTimeZone::INDIAN,
        DateTimeZone::PACIFIC,
    );

    $timezones = array();
    foreach( $regions as $region )
    {
        $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
    }

    $timezone_offsets = array();
    foreach( $timezones as $timezone )
    {
        $tz = new DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
    }

    // sort timezone by offset
    ksort($timezone_offsets);

    $timezone_list = array();
    foreach( $timezone_offsets as $timezone => $offset )
    {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate( 'H:i', abs($offset) );

        $pretty_offset = "UTC{$offset_prefix}{$offset_formatted}";

        $timezone_list[$timezone] = "({$pretty_offset}) $timezone";
    }

    return $timezone_list;
}

} 



if (! function_exists('verify_envato_purchase_code')) {
function verify_envato_purchase_code($product_code)
    { 
      
        $url = "https://api.envato.com/v3/market/author/sale?code=".$product_code;
        $curl = curl_init($url);


        $personal_token = "M8tF6z8lzZBBkmZt4xm3dU4lw7Rlbrwp";
        $header = array();
        $header[] = 'Authorization: Bearer '.$personal_token;
        $header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:41.0) Gecko/20100101 Firefox/41.0';
        $header[] = 'timeout: 20';
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);

        $envatoRes = curl_exec($curl);
        curl_close($curl);
        $envatoRes = json_decode($envatoRes);
         

         return $envatoRes;
      
    }
} 

if (! function_exists('grab_image')) {
function grab_image($file_url,$save_to){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 140);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        $output = curl_exec($ch);
        $file = fopen($save_to, "w+");
        fputs($file, $output);
        fclose($file);
    }
}

if (! function_exists('checkSignSalt')) {
function checkSignSalt($data_info){

        $key="viaviweb";

        $data_json = $data_info;

        $data_arr = json_decode(urldecode(base64_decode($data_json)),true);

        //echo $data_arr['salt'];
        //exit;

        if((!isset($data_arr['sign']) && !isset($data_arr['salt'])) OR ($data_arr['sign'] == '' && $data_arr['salt'] == '')){
            //$data['data'] = array("status" => -1, "message" => "Invalid sign salt.");
             
            $response = array("success" => -1, "message" => "Invalid sign salt.","status_code" => 200);
            $set['EBOOK_APP'] = $response;
 
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            exit();

             
            exit();


        }else{
            
            $data_arr['salt'];    
            
            $md5_salt=md5($key.$data_arr['salt']);

            if($data_arr['sign']!=$md5_salt){

                $response = array("success" => -1, "message" => "Invalid sign salt.","status_code" => 200);
                $set['EBOOK_APP'] = $response;

                header( 'Content-Type: application/json; charset=utf-8' );
                echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                exit();
            }
        }
        
        return $data_arr;
        
    }
}

if (! function_exists('countryNameToISO3166')) {
function countryNameToISO3166($country_name, $language) {
    if (strlen($language) != 2) {
        //Language must be on 2 caracters
        return NULL;
    }

    //Set uppercase if never
    $language = strtoupper($language);

    $countrycode_list = array('AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM', 'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB', 'BY', 'BE', 'BZ', 'BJ', 'BM', 'BT', 'BO', 'BQ', 'BA', 'BW', 'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI', 'KH', 'CM', 'CA', 'CV', 'KY', 'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO', 'KM', 'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CW', 'CY', 'CZ', 'DK', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE', 'ET', 'FK', 'FO', 'FJ', 'FI', 'FR', 'GF', 'PF', 'TF', 'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR', 'GL', 'GD', 'GP', 'GU', 'GT', 'GG', 'GN', 'GW', 'GY', 'HT', 'HM', 'VA', 'HN', 'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IM', 'IL', 'IT', 'JM', 'JP', 'JE', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'KW', 'KG', 'LA', 'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT', 'LU', 'MO', 'MK', 'MG', 'MW', 'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR', 'MU', 'YT', 'MX', 'FM', 'MD', 'MC', 'MN', 'ME', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'NC', 'NZ', 'NI', 'NE', 'NG', 'NU', 'NF', 'MP', 'NO', 'OM', 'PK', 'PW', 'PS', 'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT', 'PR', 'QA', 'RE', 'RO', 'RU', 'RW', 'BL', 'SH', 'KN', 'LC', 'MF', 'PM', 'VC', 'WS', 'SM', 'ST', 'SA', 'SN', 'RS', 'SC', 'SL', 'SG', 'SX', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'SS', 'ES', 'LK', 'SD', 'SR', 'SJ', 'SZ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ', 'TH', 'TL', 'TG', 'TK', 'TO', 'TT', 'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE', 'GB', 'US', 'UM', 'UY', 'UZ', 'VU', 'VE', 'VN', 'VG', 'VI', 'WF', 'EH', 'YE', 'ZM', 'ZW');
    $ISO3166 = NULL;
    //Loop all country codes
    foreach ($countrycode_list as $countrycode) {
        $locale_cc = Locale::getDisplayRegion('-' . $countrycode, $language);
        //Case insensitive
        if (strcasecmp($country_name, $locale_cc) == 0) {
            $ISO3166 = $countrycode;
            break;
        }
    }
    //return NULL if not found or country code
    return $ISO3166;
}

}

if (! function_exists('getRandomColorCode')) {
function getRandomColorCode()
{   
    $code=array('#ff8acc', '#5b69bc','#35b8e0', '#71b6f9', '#ff8acc');

    $rand_keys = array_rand($code,2);
    //$v = $array[$k];

    //return $code[$rand_keys[0]];

    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

}

if (! function_exists('getRandomProgressColor')) {
function getRandomProgressColor()
{   
    $code=array('primary', 'pink','info', 'warning', 'danger', 'success', 'dark', 'purple');

    $rand_keys = array_rand($code,2);
     

    return $code[$rand_keys[0]];
}

}

if (! function_exists('get_ip_location')) {

   function get_ip_location($ip)
    {
            $url = "http://ip-api.com/json/".$ip;
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            // Fail fast: never let a slow/down geo-IP service stall app_details
            // (the Android splash blocks on that response).
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $response = curl_exec($ch);
            curl_close($ch);
             
            // Retrieve IP data from API response 
            $ipData = json_decode($response, true); 
             
            // Return geolocation data 
            return !empty($ipData)?$ipData:false; 
    } 
}

if (! function_exists('save_visitor_analytics_info')) {
function save_visitor_analytics_info($user_ip,$os_name,$browser_name) {
     
 
    $get_ip_info=get_ip_location($user_ip);

    // get_ip_location() returns false when the external geo-IP lookup fails or is
    // empty. Reading ['status'] off a bool throws and 500s app_details — which the
    // Android splash screen blocks on, so the app gets stuck. Guard for it.
    if(is_array($get_ip_info) && isset($get_ip_info['status']) && $get_ip_info['status']=="success")
    {
         $user_country_code=isset($get_ip_info['countryCode'])?$get_ip_info['countryCode']:'';
         $user_country=isset($get_ip_info['country'])?$get_ip_info['country']:'';
    }
    else
    {
        $user_country_code='';
         $user_country='';
    }
   
    $date=strtotime(date('m/d/Y'));

    //Check duplicate
    $analytics_info = Analytics::where('user_ip',$user_ip)->where('date',$date)->first();

    if($analytics_info=="")
    {
        $analytics_obj = new Analytics;
 
        $analytics_obj->user_ip = $user_ip;
        $analytics_obj->country_code = $user_country_code;
        $analytics_obj->country = $user_country;
        $analytics_obj->operating_system = $os_name;
        $analytics_obj->browser = $browser_name;
        $analytics_obj->date = $date;
         
        $analytics_obj->save();
    }

    return true;

 }
}    
 

if (! function_exists('getCurrencySymbols')) {
    function getCurrencySymbols($code)
    { 
        $currency_symbols = array(
                            'AED' => '&#1583;.&#1573;', // ?
                            'AFN' => '&#65;&#102;',
                            'ALL' => '&#76;&#101;&#107;',
                            'AMD' => '',
                            'ANG' => '&#402;',
                            'AOA' => '&#75;&#122;', // ?
                            'ARS' => '&#36;',
                            'AUD' => '&#36;',
                            'AWG' => '&#402;',
                            'AZN' => '&#1084;&#1072;&#1085;',
                            'BAM' => '&#75;&#77;',
                            'BBD' => '&#36;',
                            'BDT' => '&#2547;', // ?
                            'BGN' => '&#1083;&#1074;',
                            'BHD' => '.&#1583;.&#1576;', // ?
                            'BIF' => '&#70;&#66;&#117;', // ?
                            'BMD' => '&#36;',
                            'BND' => '&#36;',
                            'BOB' => '&#36;&#98;',
                            'BRL' => '&#82;&#36;',
                            'BSD' => '&#36;',
                            'BTN' => '&#78;&#117;&#46;', // ?
                            'BWP' => '&#80;',
                            'BYR' => '&#112;&#46;',
                            'BZD' => '&#66;&#90;&#36;',
                            'CAD' => '&#36;',
                            'CDF' => '&#70;&#67;',
                            'CHF' => '&#67;&#72;&#70;',
                            'CLF' => '', // ?
                            'CLP' => '&#36;',
                            'CNY' => '&#165;',
                            'COP' => '&#36;',
                            'CRC' => '&#8353;',
                            'CUP' => '&#8396;',
                            'CVE' => '&#36;', // ?
                            'CZK' => '&#75;&#269;',
                            'DJF' => '&#70;&#100;&#106;', // ?
                            'DKK' => '&#107;&#114;',
                            'DOP' => '&#82;&#68;&#36;',
                            'DZD' => '&#1583;&#1580;', // ?
                            'EGP' => '&#163;',
                            'ETB' => '&#66;&#114;',
                            'EUR' => '&#8364;',
                            'FJD' => '&#36;',
                            'FKP' => '&#163;',
                            'GBP' => '&#163;',
                            'GEL' => '&#4314;', // ?
                            'GHS' => '&#162;',
                            'GIP' => '&#163;',
                            'GMD' => '&#68;', // ?
                            'GNF' => '&#70;&#71;', // ?
                            'GTQ' => '&#81;',
                            'GYD' => '&#36;',
                            'HKD' => '&#36;',
                            'HNL' => '&#76;',
                            'HRK' => '&#107;&#110;',
                            'HTG' => '&#71;', // ?
                            'HUF' => '&#70;&#116;',
                            'IDR' => '&#82;&#112;',
                            'ILS' => '&#8362;',
                            'INR' => '&#8377;',
                            'IQD' => '&#1593;.&#1583;', // ?
                            'IRR' => '&#65020;',
                            'ISK' => '&#107;&#114;',
                            'JEP' => '&#163;',
                            'JMD' => '&#74;&#36;',
                            'JOD' => '&#74;&#68;', // ?
                            'JPY' => '&#165;',
                            'KES' => '&#75;&#83;&#104;', // ?
                            'KGS' => '&#1083;&#1074;',
                            'KHR' => '&#6107;',
                            'KMF' => '&#67;&#70;', // ?
                            'KPW' => '&#8361;',
                            'KRW' => '&#8361;',
                            'KWD' => '&#1583;.&#1603;', // ?
                            'KYD' => '&#36;',
                            'KZT' => '&#1083;&#1074;',
                            'LAK' => '&#8365;',
                            'LBP' => '&#163;',
                            'LKR' => '&#8360;',
                            'LRD' => '&#36;',
                            'LSL' => '&#76;', // ?
                            'LTL' => '&#76;&#116;',
                            'LVL' => '&#76;&#115;',
                            'LYD' => '&#1604;.&#1583;', // ?
                            'MAD' => '&#1583;.&#1605;.', //?
                            'MDL' => '&#76;',
                            'MGA' => '&#65;&#114;', // ?
                            'MKD' => '&#1076;&#1077;&#1085;',
                            'MMK' => '&#75;',
                            'MNT' => '&#8366;',
                            'MOP' => '&#77;&#79;&#80;&#36;', // ?
                            'MRO' => '&#85;&#77;', // ?
                            'MUR' => '&#8360;', // ?
                            'MVR' => '.&#1923;', // ?
                            'MWK' => '&#77;&#75;',
                            'MXN' => '&#36;',
                            'MYR' => '&#82;&#77;',
                            'MZN' => '&#77;&#84;',
                            'NAD' => '&#36;',
                            'NGN' => '&#8358;',
                            'NIO' => '&#67;&#36;',
                            'NOK' => '&#107;&#114;',
                            'NPR' => '&#8360;',
                            'NZD' => '&#36;',
                            'OMR' => '&#65020;',
                            'PAB' => '&#66;&#47;&#46;',
                            'PEN' => '&#83;&#47;&#46;',
                            'PGK' => '&#75;', // ?
                            'PHP' => '&#8369;',
                            'PKR' => '&#8360;',
                            'PLN' => '&#122;&#322;',
                            'PYG' => '&#71;&#115;',
                            'QAR' => '&#65020;',
                            'RON' => '&#108;&#101;&#105;',
                            'RSD' => '&#1044;&#1080;&#1085;&#46;',
                            'RUB' => '&#1088;&#1091;&#1073;',
                            'RWF' => '&#1585;.&#1587;',
                            'SAR' => '&#65020;',
                            'SBD' => '&#36;',
                            'SCR' => '&#8360;',
                            'SDG' => '&#163;', // ?
                            'SEK' => '&#107;&#114;',
                            'SGD' => '&#36;',
                            'SHP' => '&#163;',
                            'SLL' => '&#76;&#101;', // ?
                            'SOS' => '&#83;',
                            'SRD' => '&#36;',
                            'STD' => '&#68;&#98;', // ?
                            'SVC' => '&#36;',
                            'SYP' => '&#163;',
                            'SZL' => '&#76;', // ?
                            'THB' => '&#3647;',
                            'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
                            'TMT' => '&#109;',
                            'TND' => '&#1583;.&#1578;',
                            'TOP' => '&#84;&#36;',
                            'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
                            'TTD' => '&#36;',
                            'TWD' => '&#78;&#84;&#36;',
                            'TZS' => '',
                            'UAH' => '&#8372;',
                            'UGX' => '&#85;&#83;&#104;',
                            'USD' => '&#36;',
                            'UYU' => '&#36;&#85;',
                            'UZS' => '&#1083;&#1074;',
                            'VEF' => '&#66;&#115;',
                            'VND' => '&#8363;',
                            'VUV' => '&#86;&#84;',
                            'WST' => '&#87;&#83;&#36;',
                            'XAF' => '&#70;&#67;&#70;&#65;',
                            'XCD' => '&#36;',
                            'XDR' => '',
                            'XOF' => '',
                            'XPF' => '&#70;',
                            'YER' => '&#65020;',
                            'ZAR' => '&#82;',
                            'ZMK' => '&#90;&#75;', // ?
                            'ZWL' => '&#90;&#36;',
                        );
            
            $currency_html_code=$currency_symbols[$code];

            return $currency_html_code;
    }

}

if (! function_exists('getCurrencyList')) {
    function getCurrencyList()
    {                   
            // count 164
            $currency_list = array(
                "AFA" => "Afghan Afghani",
                "ALL" => "Albanian Lek",
                "DZD" => "Algerian Dinar",
                "AOA" => "Angolan Kwanza",
                "ARS" => "Argentine Peso",
                "AMD" => "Armenian Dram",
                "AWG" => "Aruban Florin",
                "AUD" => "Australian Dollar",
                "AZN" => "Azerbaijani Manat",
                "BSD" => "Bahamian Dollar",
                "BHD" => "Bahraini Dinar",
                "BDT" => "Bangladeshi Taka",
                "BBD" => "Barbadian Dollar",
                "BYR" => "Belarusian Ruble",
                "BEF" => "Belgian Franc",
                "BZD" => "Belize Dollar",
                "BMD" => "Bermudan Dollar",
                "BTN" => "Bhutanese Ngultrum",
                "BTC" => "Bitcoin",
                "BOB" => "Bolivian Boliviano",
                "BAM" => "Bosnia",
                "BWP" => "Botswanan Pula",
                "BRL" => "Brazilian Real",
                "GBP" => "British Pound Sterling",
                "BND" => "Brunei Dollar",
                "BGN" => "Bulgarian Lev",
                "BIF" => "Burundian Franc",
                "KHR" => "Cambodian Riel",
                "CAD" => "Canadian Dollar",
                "CVE" => "Cape Verdean Escudo",
                "KYD" => "Cayman Islands Dollar",
                "XOF" => "CFA Franc BCEAO",
                "XAF" => "CFA Franc BEAC",
                "XPF" => "CFP Franc",
                "CLP" => "Chilean Peso",
                "CNY" => "Chinese Yuan",
                "COP" => "Colombian Peso",
                "KMF" => "Comorian Franc",
                "CDF" => "Congolese Franc",
                "CRC" => "Costa Rican ColÃ³n",
                "HRK" => "Croatian Kuna",
                "CUC" => "Cuban Convertible Peso",
                "CZK" => "Czech Republic Koruna",
                "DKK" => "Danish Krone",
                "DJF" => "Djiboutian Franc",
                "DOP" => "Dominican Peso",
                "XCD" => "East Caribbean Dollar",
                "EGP" => "Egyptian Pound",
                "ERN" => "Eritrean Nakfa",
                "EEK" => "Estonian Kroon",
                "ETB" => "Ethiopian Birr",
                "EUR" => "Euro",
                "FKP" => "Falkland Islands Pound",
                "FJD" => "Fijian Dollar",
                "GMD" => "Gambian Dalasi",
                "GEL" => "Georgian Lari",
                "DEM" => "German Mark",
                "GHS" => "Ghanaian Cedi",
                "GIP" => "Gibraltar Pound",
                "GRD" => "Greek Drachma",
                "GTQ" => "Guatemalan Quetzal",
                "GNF" => "Guinean Franc",
                "GYD" => "Guyanaese Dollar",
                "HTG" => "Haitian Gourde",
                "HNL" => "Honduran Lempira",
                "HKD" => "Hong Kong Dollar",
                "HUF" => "Hungarian Forint",
                "ISK" => "Icelandic KrÃ³na",
                "INR" => "Indian Rupee",
                "IDR" => "Indonesian Rupiah",
                "IRR" => "Iranian Rial",
                "IQD" => "Iraqi Dinar",
                "ILS" => "Israeli New Sheqel",
                "ITL" => "Italian Lira",
                "JMD" => "Jamaican Dollar",
                "JPY" => "Japanese Yen",
                "JOD" => "Jordanian Dinar",
                "KZT" => "Kazakhstani Tenge",
                "KES" => "Kenyan Shilling",
                "KWD" => "Kuwaiti Dinar",
                "KGS" => "Kyrgystani Som",
                "LAK" => "Laotian Kip",
                "LVL" => "Latvian Lats",
                "LBP" => "Lebanese Pound",
                "LSL" => "Lesotho Loti",
                "LRD" => "Liberian Dollar",
                "LYD" => "Libyan Dinar",
                "LTL" => "Lithuanian Litas",
                "MOP" => "Macanese Pataca",
                "MKD" => "Macedonian Denar",
                "MGA" => "Malagasy Ariary",
                "MWK" => "Malawian Kwacha",
                "MYR" => "Malaysian Ringgit",
                "MVR" => "Maldivian Rufiyaa",
                "MRO" => "Mauritanian Ouguiya",
                "MUR" => "Mauritian Rupee",
                "MXN" => "Mexican Peso",
                "MDL" => "Moldovan Leu",
                "MNT" => "Mongolian Tugrik",
                "MAD" => "Moroccan Dirham",
                "MZM" => "Mozambican Metical",
                "MMK" => "Myanmar Kyat",
                "NAD" => "Namibian Dollar",
                "NPR" => "Nepalese Rupee",
                "ANG" => "Netherlands Antillean Guilder",
                "TWD" => "New Taiwan Dollar",
                "NZD" => "New Zealand Dollar",
                "NIO" => "Nicaraguan CÃ³rdoba",
                "NGN" => "Nigerian Naira",
                "KPW" => "North Korean Won",
                "NOK" => "Norwegian Krone",
                "OMR" => "Omani Rial",
                "PKR" => "Pakistani Rupee",
                "PAB" => "Panamanian Balboa",
                "PGK" => "Papua New Guinean Kina",
                "PYG" => "Paraguayan Guarani",
                "PEN" => "Peruvian Nuevo Sol",
                "PHP" => "Philippine Peso",
                "PLN" => "Polish Zloty",
                "QAR" => "Qatari Rial",
                "RON" => "Romanian Leu",
                "RUB" => "Russian Ruble",
                "RWF" => "Rwandan Franc",
                "SVC" => "Salvadoran ColÃ³n",
                "WST" => "Samoan Tala",
                "SAR" => "Saudi Riyal",
                "RSD" => "Serbian Dinar",
                "SCR" => "Seychellois Rupee",
                "SLL" => "Sierra Leonean Leone",
                "SGD" => "Singapore Dollar",
                "SKK" => "Slovak Koruna",
                "SBD" => "Solomon Islands Dollar",
                "SOS" => "Somali Shilling",
                "ZAR" => "South African Rand",
                "KRW" => "South Korean Won",
                "XDR" => "Special Drawing Rights",
                "LKR" => "Sri Lankan Rupee",
                "SHP" => "St. Helena Pound",
                "SDG" => "Sudanese Pound",
                "SRD" => "Surinamese Dollar",
                "SZL" => "Swazi Lilangeni",
                "SEK" => "Swedish Krona",
                "CHF" => "Swiss Franc",
                "SYP" => "Syrian Pound",
                "STD" => "São Tomé and Príncipe Dobra",
                "TJS" => "Tajikistani Somoni",
                "TZS" => "Tanzanian Shilling",
                "THB" => "Thai Baht",
                "TOP" => "Tongan pa'anga",
                "TTD" => "Trinidad & Tobago Dollar",
                "TND" => "Tunisian Dinar",
                "TRY" => "Turkish Lira",
                "TMT" => "Turkmenistani Manat",
                "UGX" => "Ugandan Shilling",
                "UAH" => "Ukrainian Hryvnia",
                "AED" => "United Arab Emirates Dirham",
                "UYU" => "Uruguayan Peso",
                "USD" => "US Dollar",
                "UZS" => "Uzbekistan Som",
                "VUV" => "Vanuatu Vatu",
                "VEF" => "Venezuelan BolÃvar",
                "VND" => "Vietnamese Dong",
                "YER" => "Yemeni Rial",
                "ZMK" => "Zambian Kwacha"
            );
 

            return $currency_list;
    }

}


 
if (!function_exists('get_user_department_id')) {
    /**
     * Return the department_id for a given user, or null.
     * Used to softly prioritise a user's department books in listings.
     */
    function get_user_department_id($user_id)
    {
        if (empty($user_id)) {
            return null;
        }

        $user = \App\User::find($user_id);

        return ($user && !empty($user->department_id)) ? $user->department_id : null;
    }
}

if (!function_exists('apply_department_priority')) {
    /**
     * Apply a soft "department first" ordering to a Books query builder.
     * Books in the user's department are shown first; all other books still appear.
     * Falls back to the given default ordering when no department applies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed  $user_id
     * @param  string $default_col
     * @param  string $default_dir
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function apply_department_priority($query, $user_id, $default_col = 'id', $default_dir = 'DESC')
    {
        $department_id = get_user_department_id($user_id);

        if ($department_id) {
            $dept_id = (int) $department_id;
            // Books linked to the user's department (via book_department pivot) sort first.
            $query->orderByRaw(
                "(SELECT COUNT(*) FROM book_department bd WHERE bd.book_id = books.id AND bd.department_id = $dept_id) DESC"
            );
        }

        return $query->orderBy($default_col, $default_dir);
    }
}

if (!function_exists('book_asset_url')) {
    /**
     * Resolve a stored asset path to a full URL.
     *
     * Handles three cases so old and new rows coexist with no migration:
     *  - Full URLs (DigitalOcean Spaces CDN or external server URLs) are returned as-is.
     *  - Legacy relative paths (e.g. "upload/user_books/x.jpg") are prefixed with APP_URL.
     */
    function book_asset_url($path)
    {
        if (empty($path)) {
            return '';
        }
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        return rtrim(env('APP_URL'), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('spaces_upload')) {
    /**
     * Upload an UploadedFile to DigitalOcean Spaces under the given folder
     * prefix ("images" or "books") and return its public CDN URL.
     */
    function spaces_upload($file, $folder, $fileName)
    {
        $key = trim($folder, '/') . '/' . $fileName;
        \Illuminate\Support\Facades\Storage::disk('spaces')
            ->putFileAs(trim($folder, '/'), $file, $fileName, 'public');
        return \Illuminate\Support\Facades\Storage::disk('spaces')->url($key);
    }
}

if (!function_exists('user_image_url')) {
    /**
     * Resolve a stored user profile image to a full URL.
     * Full URLs (Spaces CDN) pass through; legacy bare filenames resolve
     * under the local /upload/ directory to preserve old behavior.
     */
    function user_image_url($image)
    {
        if (empty($image)) {
            return '';
        }
        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }
        return \URL::asset('upload/' . $image);
    }
}

if (!function_exists('has_user_image')) {
    /**
     * Whether a stored profile image is actually displayable.
     *
     * The views used to ask file_exists('upload/'.$image), which is false for a
     * remote URL — so Google and Spaces avatars fell through to the placeholder,
     * or worse rendered as /upload/https://... Remote URLs count as present;
     * legacy bare filenames still have to exist on disk.
     */
    function has_user_image($image)
    {
        if (empty($image)) {
            return false;
        }
        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
            return true;
        }
        return file_exists(public_path('upload/' . $image));
    }
}

if (!function_exists('generate_book_cover_image')) {
    /**
     * Build a book cover as an Intervention Image object: solid category color
     * background + BIG centered, word-wrapped title in a real TTF font.
     * Returns the image (caller uploads to Spaces). Null on failure.
     */
    function generate_book_cover_image($title, $bg_hex = '#4a7dff')
    {
        try {
            $w = 600; $h = 850;
            $img = \Image::canvas($w, $h, $bg_hex);

            // subtle darker band at the bottom for a book feel
            $img->rectangle(0, $h - 130, $w, $h, function ($draw) {
                $draw->background('rgba(0,0,0,0.12)');
            });

            $title = trim(stripslashes($title));

            // Bundled font (shipped with this feature). Fall back to a couple of spots.
            $font = null;
            foreach ([
                public_path('admin_assets/fonts/cover-font.ttf'),
                storage_path('fonts/cover-font.ttf'),
                public_path('admin_assets/fonts/DejaVuSans-Bold.ttf'),
            ] as $cand) {
                if (file_exists($cand)) { $font = $cand; break; }
            }

            // Big text; shrink a little for very long titles.
            $len = mb_strlen($title);
            if ($len <= 20)      { $font_size = 64; $wrap = 12; }
            elseif ($len <= 45)  { $font_size = 52; $wrap = 15; }
            elseif ($len <= 80)  { $font_size = 42; $wrap = 18; }
            else                 { $font_size = 34; $wrap = 22; }

            $wrapped = wordwrap($title, $wrap, "\n", true);
            $lines = explode("\n", $wrapped);
            if (count($lines) > 9) {
                $lines = array_slice($lines, 0, 9);
                $lines[8] = rtrim($lines[8]) . '...';
            }
            $line_height = $font_size + 14;
            $start_y = ($h - (count($lines) * $line_height)) / 2 + $font_size;

            foreach ($lines as $i => $line) {
                $y = $start_y + ($i * $line_height);
                $img->text($line, $w / 2, $y, function ($f) use ($font, $font_size) {
                    if ($font) { $f->file($font); }
                    $f->size($font_size);
                    $f->color('#ffffff');
                    $f->align('center');
                    $f->valign('middle');
                });
            }

            return $img;
        } catch (\Exception $e) {
            \Log::error('generate_book_cover_image failed: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('generate_username')) {
    /**
     * Build a unique, readable username from a person's name.
     * e.g. "Pranay Reddy" -> "pranay_reddy_82" (number added for uniqueness).
     * Falls back to the email local-part or "user" when name is empty.
     */
    function generate_username($name, $email = '')
    {
        $base = strtolower(trim($name));
        // strip accents/symbols, keep letters+numbers+spaces
        $base = preg_replace('/[^a-z0-9\s]/', '', $base);
        $base = preg_replace('/\s+/', '_', trim($base));

        if ($base === '' && $email !== '') {
            $base = strtolower(preg_replace('/[^a-z0-9]/', '', substr($email, 0, strpos($email, '@') ?: strlen($email))));
        }
        if ($base === '') {
            $base = 'user';
        }
        // keep it a sane length
        $base = substr($base, 0, 24);

        // try base, then base_<random 2-4 digit> until unique
        $candidate = $base;
        $tries = 0;
        while (\App\User::where('username', $candidate)->exists()) {
            $tries++;
            $suffix = rand(10, ($tries < 5 ? 99 : 9999));
            $candidate = $base . '_' . $suffix;
            if ($tries > 30) { // absolute fallback
                $candidate = $base . '_' . time();
                break;
            }
        }
        return $candidate;
    }
}

if (!function_exists('onesignal_endpoint_and_auth')) {
    /**
     * Resolve the correct OneSignal endpoint + Authorization header for a key.
     * New "rich" App API keys start with os_v2_ and use api.onesignal.com with
     * "Key <key>"; legacy keys use onesignal.com/api/v1 with "Basic <key>".
     * Returns array('url' => ..., 'auth' => ...).
     */
    function onesignal_endpoint_and_auth($key)
    {
        $key = trim((string) $key);
        if (strpos($key, 'os_v2_') === 0) {
            return array(
                'url'  => 'https://api.onesignal.com/notifications',
                'auth' => 'Authorization: Key ' . $key,
            );
        }
        return array(
            'url'  => 'https://onesignal.com/api/v1/notifications',
            'auth' => 'Authorization: Basic ' . $key,
        );
    }
}

if (!function_exists('send_media_notification')) {
    /**
     * Send a OneSignal push for a media post. Tapping it should open the post
     * in the app (data.type='media', data.post_id=<id>).
     * Returns the raw OneSignal response, or false if not configured.
     */
    function send_media_notification($post_id, $title, $message, $image = '')
    {
        $settings = \App\Settings::find(1);
        if (!$settings || !$settings->onesignal_app_id || !$settings->onesignal_rest_key) {
            return false;
        }

        $data = array(
            'foo'           => 'bar',
            'type'          => 'media',
            'post_id'       => (string)$post_id,
            'post_title'    => $title,
            'external_link' => false,
        );

        $fields = array(
            'app_id'            => $settings->onesignal_app_id,
            'included_segments' => array('All'),
            'data'              => $data,
            'headings'          => array('en' => $title),
            'contents'          => array('en' => $message),
        );
        if ($image) {
            $fields['big_picture'] = $image;
            $fields['ios_attachments'] = array('id' => $image);
        }

        $ch = curl_init();
        // OneSignal "rich" API migration (legacy keys deprecated Q1 2026).
        $osauth = onesignal_endpoint_and_auth($settings->onesignal_rest_key);
        $url = $osauth['url'];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Accept: application/json',
            $osauth['auth'],
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        // Don't fail silently: log non-2xx so a bad/expired key is visible.
        if ($curlErr || $httpCode < 200 || $httpCode >= 300) {
            \Log::warning('OneSignal push failed', array(
                'http_code' => $httpCode,
                'curl_error' => $curlErr,
                'response' => is_string($response) ? substr($response, 0, 500) : $response,
                'endpoint' => $url,
            ));
        }

        return $response;
    }
}

if (!function_exists('admin_can')) {
    /**
     * RBAC permission check. Master admins (usertype 'Admin') always pass.
     * Others are checked against their role's granted permissions.
     * $permission is a "module.action" string, e.g. "books.edit".
     *
     * Results are cached per-request to avoid repeated queries.
     */
    function admin_can($permission)
    {
        $u = \Auth::user();
        if (!$u) {
            return false;
        }
        // Master admin: full access, always.
        if ($u->usertype === 'Admin') {
            return true;
        }
        // Non-admin app users never have admin access.
        if ($u->usertype !== 'Sub_Admin') {
            return false;
        }
        // Sub_Admin with no role = no access.
        if (empty($u->role_id)) {
            return false;
        }

        static $cache = [];
        $key = $u->role_id;
        if (!isset($cache[$key])) {
            $cache[$key] = \App\RolePermission::where('role_id', $u->role_id)
                ->pluck('permission')->toArray();
        }
        return in_array($permission, $cache[$key], true);
    }
}

if (!function_exists('admin_can_any')) {
    /**
     * True if the user can do ANY of the given permissions.
     * Useful for showing a module if the user has any action in it.
     */
    function admin_can_any($permissions)
    {
        foreach ((array) $permissions as $p) {
            if (admin_can($p)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('admin_can_module')) {
    /**
     * True if the user can access a module at all (has any action in it).
     * Used to decide whether to show a sidebar item.
     */
    function admin_can_module($module)
    {
        $u = \Auth::user();
        if ($u && $u->usertype === 'Admin') {
            return true;
        }
        $cfg = config('permissions.modules.' . $module);
        if (!$cfg) {
            return false;
        }
        foreach ($cfg['actions'] as $action) {
            if (admin_can($module . '.' . $action)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('youtube_id')) {
    /**
     * Extract the 11-char YouTube video id from any common URL form, or return
     * the input unchanged if it already looks like a bare id.
     * Handles: watch?v=, youtu.be/, embed/, shorts/, live/, and extra params.
     */
    function youtube_id($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }
        // Already a bare id?
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url)) {
            return $url;
        }
        $patterns = [
            '/youtube\.com\/watch\?[^ ]*v=([A-Za-z0-9_-]{11})/',
            '/youtu\.be\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/embed\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/live\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/v\/([A-Za-z0-9_-]{11})/',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return $m[1];
            }
        }
        return '';
    }
}

if (!function_exists('youtube_thumb')) {
    /** Poster image for a YouTube id (hqdefault is always available). */
    function youtube_thumb($id)
    {
        $id = youtube_id($id);
        return $id ? 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg' : '';
    }
}

if (!function_exists('youtube_embed')) {
    /** Privacy-friendly embed URL for a YouTube id. */
    function youtube_embed($id)
    {
        $id = youtube_id($id);
        return $id ? 'https://www.youtube-nocookie.com/embed/' . $id : '';
    }
}

if (!function_exists('youtube_watch')) {
    /** Canonical watch URL for a YouTube id. */
    function youtube_watch($id)
    {
        $id = youtube_id($id);
        return $id ? 'https://www.youtube.com/watch?v=' . $id : '';
    }
}

if (!function_exists('jntuh_grade_points_map')) {
    /**
     * JNTUH 10-point grade -> grade point value.
     * O=10, A+=9, A=8, B+=7, B=6, C=5, F=0. (Ab / absent treated as 0.)
     */
    function jntuh_grade_points_map()
    {
        return [
            'O'  => 10, 'A+' => 9, 'A' => 8, 'B+' => 7,
            'B'  => 6,  'C'  => 5, 'F' => 0, 'AB' => 0, 'ABSENT' => 0,
        ];
    }
}

if (!function_exists('jntuh_grade_value')) {
    /** Point value for a grade string, or null if unknown. */
    function jntuh_grade_value($grade)
    {
        $g = strtoupper(trim((string) $grade));
        $map = jntuh_grade_points_map();
        return array_key_exists($g, $map) ? $map[$g] : null;
    }
}

if (!function_exists('jntuh_subject_grade_points')) {
    /**
     * JNTUH "Grade Point (Gi)" for a subject = the grade VALUE only
     * (O=10, A+=9, A=8, B+=7, B=6, C=5, F/Ab=0). This is what the memo shows
     * per subject; it is NOT multiplied by credits here. The credit-weighting
     * (Gi x Ci) happens only inside SGPA/CGPA (see jntuh_compute_sgpa).
     * $credits is accepted for signature compatibility but is not used.
     * Returns null for an unknown grade.
     */
    function jntuh_subject_grade_points($grade, $credits = null)
    {
        return jntuh_grade_value($grade); // null if grade unknown
    }
}

if (!function_exists('jntuh_compute_sgpa')) {
    /**
     * SGPA = sum(Gi * Ci) / sum(Ci), where Gi is the subject grade value
     * (grade_points column now stores Gi) and Ci is credits. Subjects with
     * 0 credits (F/Ab, mandatory non-credit) are excluded. Returns null when
     * there are no credits.
     */
    function jntuh_compute_sgpa($subjects)
    {
        $sumGp = 0.0; $sumCr = 0.0;
        foreach ($subjects as $s) {
            $cr = isset($s['credits']) && $s['credits'] !== '' ? (float) $s['credits'] : 0;
            if ($cr <= 0) { continue; }
            // Gi (grade value). Prefer an explicit grade_points, else derive.
            $gi = isset($s['grade_points']) && $s['grade_points'] !== ''
                    ? (float) $s['grade_points']
                    : jntuh_grade_value(isset($s['grade']) ? $s['grade'] : '');
            if ($gi === null) { $gi = 0; }
            $sumGp += $gi * $cr;   // credit-weighted here
            $sumCr += $cr;
        }
        if ($sumCr <= 0) { return null; }
        return round($sumGp / $sumCr, 2);
    }
}

if (!function_exists('generate_result_report_card')) {
    /**
     * Draw a watermarked PNG report card for a Result, upload to Spaces,
     * write a report_cards row, and return the public URL (or null on error).
     *
     * @param  \App\Result $result
     * @return string|null
     */
    function generate_result_report_card($result)
    {
        try {
            $result->load('semesters');

            $W = 1000;
            $pad = 48;
            $rowH = 40;
            $verified = (int) $result->verified === 1;

            $height = 300;
            $semData = array();
            foreach ($result->semesters as $sem) {
                $subs = $sem->subjects()->get();
                $semData[] = array('sem' => $sem, 'subs' => $subs);
                $height += 70 + 44 + (max(count($subs), 1) * $rowH) + 24;
            }
            $height += 140;
            $H = max($height, 700);

            $font = null;
            foreach (array(
                public_path('admin_assets/fonts/cover-font.ttf'),
                storage_path('fonts/cover-font.ttf'),
                public_path('admin_assets/fonts/DejaVuSans-Bold.ttf'),
            ) as $cand) {
                if (file_exists($cand)) { $font = $cand; break; }
            }

            $img = \Image::canvas($W, $H, '#ffffff');

            // Brand bar
            $img->rectangle(0, 0, $W, 120, function ($d) { $d->background('#0d47a1'); });
            $img->text('JNTU BOOKS', $pad, 46, function ($f) use ($font) {
                if ($font) { $f->file($font); }
                $f->size(40); $f->color('#ffffff'); $f->align('left'); $f->valign('top');
            });
            $img->text('Student Result Report Card', $pad, 92, function ($f) use ($font) {
                if ($font) { $f->file($font); }
                $f->size(20); $f->color('#bbdefb'); $f->align('left'); $f->valign('top');
            });
            if ($verified) {
                $img->rectangle($W - 250, 40, $W - $pad, 88, function ($d) { $d->background('#2e7d32'); });
                $img->text('VERIFIED', $W - 165, 52, function ($f) use ($font) {
                    if ($font) { $f->file($font); }
                    $f->size(22); $f->color('#ffffff'); $f->align('center'); $f->valign('top');
                });
            }

            // Diagonal watermark baked into pixels
            for ($y = 60; $y < $H; $y += 190) {
                for ($x = -40; $x < $W; $x += 340) {
                    $img->text('JNTU BOOKS', $x, $y, function ($f) use ($font) {
                        if ($font) { $f->file($font); }
                        $f->size(34); $f->color(array(13, 71, 161, 0.06)); $f->angle(30);
                        $f->align('left'); $f->valign('top');
                    });
                }
            }

            // Summary block
            $y = 150;
            $line = function ($label, $val) use (&$y, $img, $font, $pad) {
                $img->text($label, $pad, $y, function ($f) use ($font) {
                    if ($font) { $f->file($font); }
                    $f->size(18); $f->color('#555555'); $f->align('left'); $f->valign('top');
                });
                $img->text($val !== null && $val !== '' ? (string) $val : '-', $pad + 220, $y, function ($f) use ($font) {
                    if ($font) { $f->file($font); }
                    $f->size(18); $f->color('#111111'); $f->align('left'); $f->valign('top');
                });
                $y += 34;
            };
            $line('Name', $result->student_name);
            $line('Hall Ticket No', $result->hall_ticket_no);
            $line('Branch', $result->branch);
            $line('Regulation', $result->regulation);
            $line('Current CGPA', $result->current_cgpa);
            $line('Total Credits', $result->total_credits);
            $line('Pending Backlogs', $result->backlogs_count);
            $y += 10;

            // Semester tables
            foreach ($semData as $sd) {
                $sem = $sd['sem']; $subs = $sd['subs'];

                $img->rectangle($pad, $y, $W - $pad, $y + 44, function ($d) { $d->background('#e3f2fd'); });
                $semTitle = 'Semester ' . $sem->sem_code
                          . ($sem->sgpa !== null ? '   |   SGPA: ' . $sem->sgpa : '')
                          . ($sem->exam_month_year ? '   |   ' . $sem->exam_month_year : '');
                $img->text($semTitle, $pad + 14, $y + 12, function ($f) use ($font) {
                    if ($font) { $f->file($font); }
                    $f->size(18); $f->color('#0d47a1'); $f->align('left'); $f->valign('top');
                });
                $y += 56;

                $cols = array(
                    array('Code', $pad + 14), array('Subject', $pad + 150),
                    array('Cr', $W - 360), array('Grade', $W - 300),
                    array('Points', $W - 210), array('Status', $W - 120),
                );
                foreach ($cols as $c) {
                    $img->text($c[0], $c[1], $y, function ($f) use ($font) {
                        if ($font) { $f->file($font); }
                        $f->size(15); $f->color('#777777'); $f->align('left'); $f->valign('top');
                    });
                }
                $y += 30;

                if (count($subs) === 0) {
                    $img->text('No subjects entered', $pad + 14, $y, function ($f) use ($font) {
                        if ($font) { $f->file($font); }
                        $f->size(15); $f->color('#999999'); $f->align('left'); $f->valign('top');
                    });
                    $y += $rowH;
                } else {
                    foreach ($subs as $sub) {
                        $back = (int) $sub->is_backlog === 1;
                        $rowColor = $back ? '#c62828' : '#111111';
                        $gp = ($sub->grade_points !== null && $sub->grade_points !== '')
                                ? (string) (0 + $sub->grade_points)
                                : (function_exists('jntuh_subject_grade_points')
                                    ? (string) (0 + jntuh_subject_grade_points($sub->grade, $sub->credits))
                                    : '-');
                        $vals = array(
                            array((string) $sub->subject_code, $pad + 14),
                            array(mb_strimwidth((string) $sub->subject_name, 0, 30, '…'), $pad + 150),
                            array($sub->credits !== null ? (string) (0 + $sub->credits) : '-', $W - 360),
                            array((string) $sub->grade, $W - 300),
                            array($gp !== '' ? $gp : '-', $W - 210),
                            array($back ? 'BACKLOG' : 'Pass', $W - 120),
                        );
                        foreach ($vals as $v) {
                            $img->text($v[0], $v[1], $y, function ($f) use ($font, $rowColor) {
                                if ($font) { $f->file($font); }
                                $f->size(15); $f->color($rowColor); $f->align('left'); $f->valign('top');
                            });
                        }
                        $y += $rowH;
                    }
                }
                $y += 24;
            }

            // Footer
            $fy = $H - 110;
            $img->rectangle(0, $fy, $W, $H, function ($d) { $d->background('#0d47a1'); });
            $img->text('read.jntubooks.in', $pad, $fy + 24, function ($f) use ($font) {
                if ($font) { $f->file($font); }
                $f->size(22); $f->color('#ffffff'); $f->align('left'); $f->valign('top');
            });
            $img->text('Unofficial — entered by student via JNTU Books. Verify at results.jntuh.ac.in',
                $pad, $fy + 60, function ($f) use ($font) {
                if ($font) { $f->file($font); }
                $f->size(14); $f->color('#bbdefb'); $f->align('left'); $f->valign('top');
            });

            $binary   = (string) $img->encode('png');
            $fileName = 'report_' . $result->id . '_' . time() . '.png';
            $key      = 'reports/' . $fileName;

            \Illuminate\Support\Facades\Storage::disk('spaces')->put($key, $binary, 'public');
            $publicUrl = \Illuminate\Support\Facades\Storage::disk('spaces')->url($key);

            $card = new \App\ReportCard();
            $card->result_id = $result->id;
            $card->pdf_url   = $publicUrl; // column reused for image URL in Phase 1
            $card->verified_at_generation = (int) $result->verified;
            $card->generated_at = now();
            $card->save();

            return $publicUrl;
        } catch (\Exception $e) {
            return null;
        }
    }
}


if (!function_exists('woo_get')) {
    /**
     * Read-only GET against the MadeForU WooCommerce REST API, with short-lived
     * caching so we don't hammer the store on every app open. Auth is sent as
     * Basic (consumer key/secret) over HTTPS. Returns a decoded array, or []
     * on any failure (never throws into the app response).
     *
     * @param string $path  e.g. 'products', 'products/categories'
     * @param array  $query e.g. ['per_page' => 20, 'category' => 15]
     * @param int    $ttl   cache seconds (default 300 = 5 min)
     */
    function woo_get($path, $query = array(), $ttl = 300)
    {
        $cfg = config('services.woocommerce');
        if (empty($cfg['base']) || empty($cfg['key']) || empty($cfg['secret'])) {
            return array();
        }

        $url = rtrim($cfg['base'], '/') . '/wp-json/wc/v3/' . ltrim($path, '/');
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $cacheKey = 'woo_' . md5($url);
        try {
            return \Cache::remember($cacheKey, $ttl, function () use ($url, $cfg) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
                curl_setopt($ch, CURLOPT_USERPWD, $cfg['key'] . ':' . $cfg['secret']);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
                $body = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $err  = curl_error($ch);
                curl_close($ch);

                if ($err || $code < 200 || $code >= 300) {
                    \Log::warning('WooCommerce fetch failed', array(
                        'url' => $url, 'http_code' => $code, 'curl_error' => $err,
                    ));
                    return array();
                }
                $decoded = json_decode($body, true);
                return is_array($decoded) ? $decoded : array();
            });
        } catch (\Exception $e) {
            \Log::warning('WooCommerce cache/get error: ' . $e->getMessage());
            return array();
        }
    }
}

if (!function_exists('woo_price')) {
    /** Format a Woo price string to a clean number string (no trailing noise). */
    function woo_price($v)
    {
        if ($v === null || $v === '') { return ''; }
        return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
    }
}
