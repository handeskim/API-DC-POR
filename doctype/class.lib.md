<?php
/* 
@ string // phải là kiểu dữ liệu dạng string nếu là array thì json_encode();
@ secret_key là key được cung cấp
@ private_key là mã khóa bí mật private_key được cung cấp
*/
class Api{
	
	protected $secret_key;
	protected $private_key;
	protected $string;

	function __construct(){
		parent::__construct();
	}
	
	public function encrypt($string,$secret_key,$private_key) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $private_key), 0, 16);
		$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
		return $output;
	}

	public function decrypt($string,$secret_key,$private_key) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $private_key), 0, 16);
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		return $output;

	}
}