<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



	if(! function_exists('ssl_encrypt')){
		///////////////////// Mã hoá SSL dùng file .key để mã hoá ///////////////
    function ssl_encrypt($priv_key,$string){
			$res = openssl_get_privatekey($priv_key,null);
			openssl_private_encrypt($string,$crypttext,$res);
			return $crypttext;
    }
  }
	if(! function_exists('ssl_decrypt')){ 
		////////////////// Giải Mã SSL Dùng File .crt để Giải mã //////////////
    function ssl_decrypt($pub_key,$string){
			openssl_get_publickey($pub_key);
			openssl_public_decrypt($string,$decrypttext,$pub_key);
			return $decrypttext;
    }
  }
	if(! function_exists('ssl_read_file')){
    function ssl_read_file($file_name){
			$fps=fopen ($file_name,"r");
			$pub_key=fread($fps,8192);
			fclose($fps);
			return $pub_key;
    }
  }
//////////////////////////////////////
