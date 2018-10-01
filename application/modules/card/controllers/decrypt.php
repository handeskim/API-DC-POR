<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Decrypt extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->param = array();
		$this->apps = new core;
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->r = $this->apps->_msg_response(200);
		$this->_api_key = $this->_api_key();
		$this->_is_private_key = $this->apps->_is_private_key($this->_api_key());	
		$this->obj = array();
		$this->_param = array();
	}
	public function index_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3 || (int)$this->_level == 4){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3 || (int)$this->_level == 4){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							$string = $_GET['param'];
							$secret_key = $_GET['secret_key'];
							$secret_iv = $_GET['secret_iv'];
							$this->r = array('status'=>true,'result'=> decrypt_obj($string,$secret_key,$secret_iv));
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	
	
}


?>