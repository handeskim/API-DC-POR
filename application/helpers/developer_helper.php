<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 if(! function_exists('json')){
    function json($array){
       header('Content-Type=> application/json');
       echo json_encode($array);
    }
  }
	if(! function_exists('msg_temp')){
    function msg_temp($msg,$color){
			if(isset($color)){
					$backgroud = $color;
			}else{
				$backgroud = 'info';
			}
      $temp = '<div class="callout callout-'.$backgroud.'">
          <h4>'.$msg.'</h4>
        </div>';
				return $temp ;
    }
  }
  if(! function_exists('Extend_Domain')){
    function Extend_Domain($domain_name){
      $exteds = explode(".",$domain_name); 
		$i = count($exteds);
		$extends = null;
		if($i<=2){
			$extends = '.'.$exteds[1];
		}
		if($i>2){
			$extends = '.'.$exteds[1].'.'.$exteds[2];
		}
		return $extends;
    }
  }
 if(! function_exists('validateDomainName')){
    function validateDomainName($value)
	{
		$pattern = '/^([a-z0-9]([-a-z0-9]*[a-z0-9])?\.)+(vn)$/';
		preg_match($pattern, $value, $matches);
		return $matches;
		
	}
  } 
  if(! function_exists('ejson')){
    function ejson($array){
       header('Content-Type=> application/json');
       return json_encode($array);
    }
  }
	
    
  
  
  
 if(! function_exists('debug')){
    function debug($array){
      echo '<pre>';
	  print_r($array);
      echo '</pre>';
	  die;
    }
  }
  if(! function_exists('debug_dump')){
    function debug_dump($array){
      echo '<pre>';
	  var_dump($array);
      echo '</pre>';
	 
    }
  }
if(! function_exists('view_date')){
  function view_date(){
    
    return date('Y-m-d');
  }
}
 if(! function_exists('token_csrf')){
    function token_csrf(){
       $ci = &get_instance();
       return $ci->security->get_csrf_hash();
    }
  }
  if(! function_exists('csrf_name')){
    function csrf_name(){
      $ci = &get_instance();
      return $ci->security->get_csrf_token_name();
    }
  }


if(! function_exists('url_base64_encode')){
  function url_base64_encode($str){
    
    $encode_str = base64_encode($str);
    return urlencode($encode_str);
  }
}
if(! function_exists('url_base64_decode')){
  function url_base64_decode($str){
     
    return base64_decode(urldecode($str));
  }
}
if(! function_exists('removedot')){
  function removedot($string){
     
    return preg_replace('/([a-z]+)\.([^\s])/i', '$1. $2', $string);
  }
}
if(! function_exists('url_encoded')){
 function url_encoded($str){
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
          return $str;
    }
}
  if(! function_exists('encrypt')){
    function encrypt($string){
       $ci = &get_instance();
       $ci->load->library('encrypt');
       return $ci->encrypt->encode($string);
    }
  }
  if(! function_exists('decrypt')){
    function decrypt($string){
       $ci = &get_instance();
       $ci->load->library('encrypt');
       return $ci->encrypt->decode($string);
    }
  }

   if(! function_exists('random_pass')){
    function random_pass(){
		$length = 8;
		$rand_pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		return $rand_pass;
    }
  }
  if(! function_exists('random_username')){
    function random_username(){
		$length = 4;
		$lengthc = 2;
		$randoms = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$randomc = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $lengthc);
		$username = "ITVN-".$randoms.time().$randomc;
		return $username;
    }
  }
  if(! function_exists('random_otp')){
    function random_otp(){
		$length = 4;
		$lengthc = 2;
		$randoms = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$randomc = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $lengthc);
		$username = "ITORC".$randomc.time().$randoms;
		return $username;
    }
  }
  if(! function_exists('code_orderstmtv')){
    function code_orderstmtv(){
		$length = 6;
		$lengthc = 8;
		$randoms = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$randomc = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $lengthc);
		$code_orders = "ITVNTMTV".$randomc.time().$randoms.'-'.time();
		return $code_orders;
    }
  }
	 if(! function_exists('account_transfer_code')){
    function account_transfer_code(){
		$length = 4;
		$lengthc = 4;
		$randoms = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$randomc = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $lengthc);
		$username = "AT-".$randomc.time().$randoms;
		return $username;
    }
  }
  if(! function_exists('code_orders')){
    function code_orders($id){
		$length = 6;
		$lengthc = 8;
		$randoms = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$randomc = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $lengthc);
		$code_orders = "ITVNORD".$randomc.time().$id.$randoms.'-'.time();
		return $code_orders;
    }
  }
  if(! function_exists('id_contact_random')){
    function id_contact_random($id){
		$lengthc = 6;
		$randomc = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $lengthc);
		$code_orders = "HITEK-".$randomc.$id.'-'.time();
		return $code_orders;
    }
  }
  if(! function_exists('random_otps')){
    function random_otps(){
		$length = 4;
		$lengthc = 2;
		$randoms = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$randomc = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $lengthc);
		$username = $randomc.time().$randoms;
		return $username;
    }
  }
  if(! function_exists('create_path')){
	  function create_path(){
		$dir = FCPATH .'/public/logs/'.date('Y'). '/' . date('m'). '/' . date('d');
		$create_path_month = FCPATH .'/public/logs/'.date('Y'). '/' . date('m');
		$create_path_years = FCPATH .'/public/logs/'.date('Y');
		if(!is_dir($dir)){
		  umask(0);
		   mkdir($dir, 0777, true);
		  return $dir;
		  
		}else{
		  umask(0);
		  return $dir;
		}
	  }
	}
	 if(! function_exists('handesk_path_system_logs')){
	  function handesk_path_system_logs($directory){
		$dir = FCPATH .'/logs/'.$directory.'/'.date('Y'). '/' . date('m'). '/' . date('d');
		$create_path_month = FCPATH .'/logs/'.$directory.'/'.date('Y'). '/' . date('m');
		$create_path_years = FCPATH .'/logs/'.$directory.'/'.date('Y');
		if(!is_dir($dir)){
		  umask(0);
		   mkdir($dir, 0777, true);
		  return $dir;
		  
		}else{
		  umask(0);
		  return $dir;
		}
	  }
	}
	if ( ! function_exists('handesk_logs')){
	  function handesk_logs($msg = null) {
			$ci = & get_instance();
			$logs_handesk = array(
			'header' => $ci->session->all_userdata(),
			'content' => $msg,
		);
			file_put_contents(handesk_path_system_logs($ci->router->fetch_class()).'/'.$ci->router->fetch_method().'-'.date("d-m-Y",time()).".txt", date("d/m/Y H:i:s",time()).": ".print_r($logs_handesk, TRUE)."\n", FILE_APPEND | LOCK_EX);
	  }
	}
	if ( ! function_exists('logscontact')){
	  function logscontact($file,$msg) {
		file_put_contents(create_path().'/'.$file.'-'.date("d-m-Y",time()).".txt", $msg."\n", FILE_APPEND | LOCK_EX);
	  }
	}
	if ( ! function_exists('logsdb')){
	  function logsdb($file,$msg) {
		file_put_contents(create_path().'/'.$file.'-'.date("d-m-Y",time()).".txt", print_r($msg, TRUE)."\n", FILE_APPEND | LOCK_EX);
	  }
	}
	if ( ! function_exists('logs')){
	  function logs($file,$msg) {
		file_put_contents(create_path().'/'.$file.'-'.date("d-m-Y",time()).".txt", date("d/m/Y H:i:s",time()).": ".print_r($msg, TRUE)."\n", FILE_APPEND | LOCK_EX);
	  }
	}
	if ( ! function_exists('warring_hacker')){
	  function warring_hacker() {
		return redirect(base_url()."dashboard/logout");
	  }
	}
	
