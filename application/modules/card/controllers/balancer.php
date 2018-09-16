<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Balancer extends REST_Controller {
	private $reseller = null;
	private $blancer = 0;
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->param = array();
		$this->objects = array();
		$this->params = array();
		$this->confim = array();
		$this->apps = new core;
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->r = $this->apps->_msg_response(200);
		$this->_api_key = $this->_api_key();
		$this->_is_private_key = $this->apps->_is_private_key($this->_api_key());	
		$this->obj = array( );
		$this->_param = array();
	}
	public function index_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 4 || (int)$this->_role == 3 || (int)$this->_role == 2 || (int)$this->_role == 1){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									$this->reseller = (string)$this->apps->_token_reseller($p->token);
									$this->blancer = $this->apps->_balancer_users($this->reseller,$p->token);
									$this->r['status'] = $this->apps->_msg_response(1000);
									$this->r['balancer'] = $this->blancer;
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(2000);}
		}else{ $this->r = $this->apps->_msg_response(2000);}
		$this->response($this->r);
	}
	
}


?>