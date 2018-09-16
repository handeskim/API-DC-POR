<?php 

	$secret_key = '';
	$private_key = '';
	$username = '';
	$password = '';
	$auth = '';
	
	
	
	
	$param = array(
				
	);
	

	function getcURL($url){
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	function encrypt($string,$secret_key,$private_key) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $private_key), 0, 16);
		$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
		return $output;
	}

	function decrypt($string,$secret_key,$private_key) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $private_key), 0, 16);
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		return $output;

	}