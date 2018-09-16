<?php
class Migrate extends MY_Controller{
	private $auth;
	function __construct(){
		parent::__construct();
		// $this->load->model('global_model', 'GlobalMD');	
		$this->private_key = $this->config->item('encryption_key');
		$this->auth = $this->config->item('restore_key');
		$this->r = array('status'=>false,'connect'=>200,'result'=>null);
		$this->obj = array();
		
	}
	
	
	public function restore(){
		$auth = $this->input->get('auth');
		if($this->auth == $auth){
			$this->_private_key();
			$this->_role();
			$this->_users();
			$this->r = array('status'=>true,'connect'=>200,'result'=>null);
		}
		echo json_encode($this->r);
	}
	
	
	private function _site(){
		$param = array(
			"brand" => "DOICARD.VN",
			"company" => "Công ty cổ phẩn trực tuyến",
			"phone" => "0123-7711-777",
			"hotline" => "0123-7711-777",
			"skype" => "handesk.dotvn",
			"email_brand" => "info@handesk.xyz",
			"smtp_host" => "mail.handesk.xyz",
			"smtp_user" => "smtp@handesk.xyz",
			"smtp_password" => "123123fF",
			"address_brand" => "Ngô Thì Nhậm - Hai Bà Trưng - Hà nội",
			"brands_vat" => "011932337122",
			"facebook_page_name" => "root.ug",
			"google_plus_url" => "https=>//plus.google.com",
			"google_recaptcha" => "6LcKd2wUAAAAAO-ebaApoUbUjoHMeTPxPfFN2pGE",
			"abouts_url" => "#",
			"support_url" => "#",
			"privacy_url" => "#",
			"order_guide" => "#",
			"delivery_url" => "#",
			"developer_url" => "https=>//api.demo2308.handesk.xyz/documents?API-DOICARD-V1=5ed230c6c7c64fdeb87c186e21f8880a",
			"contact_url" => "#",
			"infomation_url" => "#",
			"website" => "www.handesk.xyz",
			"url_read" => "https=>//images",
			"logo" => "https=>//images",
			"img_details" => "https=>//images",
			"title" => "Đổi thẻ điện thoại trực tuyến,",
			"title_main" => "Đổi thẻ điện thoại trực tuyến,",
			"keywords" => "Đổi thẻ điện thoại trực tuyến,",
			"description" => "Đổi thẻ điện thoại trực tuyến,"
			);
	}
	public function _users(){
		$u = array();
		$params = array( 
				'username' => 'Administrator',
				'role' =>1,
				'password' => md5('1234567890'),
				'full_name' => 'Administrator',
				'email' => 'admin@handesk.xyz',
			);
		try{
			$u = $this->mongo_db->get('ask_users');
			$this->_api_key(1);
			
		if(!empty($u)){
			$this->mongo_db->drop_collection('ask_users');
			$id_users = $this->mongo_db->insert('ask_users',$params);
			$this->_new_api_key($level=2,$key=null,$id_users);			
		}else{
				$this->mongo_db->insert('ask_users',$params); 
				$id_users = $this->mongo_db->insert('ask_users',$params);
				$this->_new_api_key($level=2,$key=null,$id_users);
			}
		}catch (Exception $e) { 
			$this->mongo_db->insert('ask_users',$params); 
				$id_users = $this->mongo_db->insert('ask_users',$params);
				$this->_new_api_key($level=2,$key=null,$id_users);
		}
	}
	
	private function _new_api_key($level=1,$key=null,$id_users){
		if(empty($key)){ 
			$key = md5(sha1($id_users));
		}else{ $key = md5(sha1($key)); }
		$params = array(
			"key"=>$key,"ignore_limits"=>true,"level"=>$level,
			"ip_addresses"=> "127.0.0.1","is_private_key"=> core_encrypt(md5($key)), 
			"users"=> $id_users,
			"role" => 1,
			"date_created"=> date("Y-m-d H:i:s",time()) 
		);
		$k = $this->mongo_db->where(array('key'=>$key))->get('api_keys');
		if(!empty($k)){
			$k = $this->mongo_db->insert('api_keys',$params); 
		}else{
			$k = $this->mongo_db->insert('api_keys',$params); 	
		}
		return (string)$k;
	}
	private function _api_key($level=0,$key=null){
		if(empty($key)){ 
			$key = md5(sha1($this->private_key));
		}else{  $key = md5(sha1($key));}
		$params = array("key"=>$key,"ignore_limits"=>true,"level"=>$level,"ip_addresses"=> "127.0.0.1","is_private_key"=> core_encrypt(md5($this->_private_key())), "date_created"=> date("Y-m-d H:i:s",time()) );
		$k = $this->mongo_db->get('api_keys');
		if(!empty($k)){
			$this->mongo_db->drop_collection('api_keys');
			$k = $this->mongo_db->insert('api_keys',$params); 
		}else{
			$k = $this->mongo_db->insert('api_keys',$params); 	
		}
		return (string)$k;
	}
	private function _role(){
		$r = array();
		try{
			$r = $this->mongo_db->get('ask_role');
			if(!empty($r)){
				$this->mongo_db->drop_collection('ask_role');
				$this->obj[] = array('role'=>1,'role_name'=>'administrator');
				$this->obj[] = array('role'=>2,'role_name'=>'admin');
				$this->obj[] = array('role'=>3,'role_name'=>'mod');
				$this->obj[] = array('role'=>4,'role_name'=>'client');
				foreach($this->obj as $v){ $this->mongo_db->insert('ask_role',$v); }
			}else{
				$this->obj[] = array('role'=>1,'role_name'=>'administrator');
				$this->obj[] = array('role'=>2,'role_name'=>'admin');
				$this->obj[] = array('role'=>3,'role_name'=>'mod');
				$this->obj[] = array('role'=>4,'role_name'=>'client');
				foreach($this->obj as $v){ $this->mongo_db->insert('ask_role',$v); }
			}
		}catch (Exception $e) { }
	}
	
	
	public function signature(){
		
		// $pub_key = $this->_public_key();
		// $priv_key = $this->_private_key();
		// $string = "0000-0000-0000-0000";
		// openssl_sign($string, $signature, $priv_key);
		// var_dump($signature);
		// $encrypt = ssl_encrypt($priv_key,$string);
		// $decrypt = ssl_decrypt($pub_key,$encrypt);
		// var_dump($encrypt);
		// var_dump($public_key);
		// var_dump($pub_key);
		
	}
	
	public function _public_key($dn=null){
		if(empty($dn)){
			$dn = $this->_dn();
		}
		$dn = array("countryName" => "VN","stateOrProvinceName" => "Hanoi","localityName" => "Hanoi","organizationName" => "HANDESK.COM","organizationalUnitName" => "HANDESK.COM","commonName" => "handesk.COM","emailAddress" => "info@handesk.COM" );
		$csr = openssl_csr_new($dn, $this->_private_key(), $this->_cf());
		$public_key = openssl_csr_get_public_key($csr,null); 
		$info = openssl_pkey_get_details($public_key); 
		return $info["key"];
	}
	
	private function _private_key(){
		$ssl = array();
		try{
			$ssl = $this->mongo_db->get('ssl');
			if(!empty($ssl)){
				$ssl;
			}else{
				$privkey = openssl_pkey_new($this->_cf());
				openssl_pkey_export($privkey, $priv_key);
				$this->mongo_db->insert('ssl',array('private_key'=>$priv_key));
				$ssl = $this->mongo_db->get('ssl');
			}
		}catch (Exception $e) {
			$privkey = openssl_pkey_new($this->_cf());
			openssl_pkey_export($privkey, $priv_key);
			$this->mongo_db->insert('ssl',array('private_key'=>$priv_key));
			$ssl = $this->mongo_db->get('ssl');
		}
		return $ssl[0]["private_key"];
	}
	private function _cf(){
		return  array("digest_alg" => "sha512","private_key_bits" => 4096,"private_key_type" => OPENSSL_KEYTYPE_RSA,);
	}
	private function _dn(){
		$dn = array();
		try{
			$dn = $this->mongo_db->get('dn');
			if(!empty($dn)){
				$dn;
			}else{
				$dn = array("countryName" => "VN","stateOrProvinceName" => "Hanoi","localityName" => "Hanoi","organizationName" => "HANDESK.XYZ","organizationalUnitName" => "HANDESK.XYZ","commonName" => "handesk.xyz","emailAddress" => "info@handesk.xyz" );
				$this->mongo_db->insert('dn',$dn);
				$dn = $this->mongo_db->get('dn');
			}
		}catch (Exception $e) {
			$dn = array("countryName" => "VN","stateOrProvinceName" => "Hanoi","localityName" => "Hanoi","organizationName" => "HANDESK.XYZ","organizationalUnitName" => "HANDESK.XYZ","commonName" => "handesk.xyz","emailAddress" => "info@handesk.xyz" );
			$this->mongo_db->insert('dn',$dn);
			$dn = $this->mongo_db->get('dn');
		}
		foreach($dn as $v){
			$e =  array("countryName" => $v['countryName'],"stateOrProvinceName" => $v['stateOrProvinceName'],"localityName" => $v['localityName'],"organizationName" => $v['organizationName'],"organizationalUnitName" => $v['organizationalUnitName'],"commonName" => $v['commonName'], "emailAddress" => $v['emailAddress'] );
		}
		return $e;
	}
	
}

?>

