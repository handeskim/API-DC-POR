<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Transfer extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->param = array();
		$this->result = array();
		$this->objects = array();
		$this->params = array();
		$this->reseller = null;
		$this->confim = array();
		$this->obj = array();
		$this->_param = array();
		$this->apps = new core;
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->r = $this->apps->_msg_response(200);
		$this->_api_key = $this->_api_key();
		$this->_is_private_key = $this->apps->_is_private_key($this->_api_key());	
	
	}

	public function info_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->keys)){
								
									$this->result = $this->mongo_db->select(array('money_transfer','date_create','branch_bank','status','action','date_update_transfer',
									'fee','total_transfer','types','balancer_clients','beneficiary_balancer','balancer_plus','balancer_munis','payer_balancer','payer_id','payer_name',
									'beneficiary_id','beneficiary','reseller','type','transaction','bank_name','account_holders','bank_account','provinces_bank',
									))->where(array('_id' => new \MongoId($p->keys),'client_id'=>$p->client_id))->get('transfer_log');
									if(!empty($this->result)){$this->result = $this->result[0];}
									$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->result);
								}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	
}


?>