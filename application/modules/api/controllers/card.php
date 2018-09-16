<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Card extends REST_Controller {
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
		$this->note = '';
		$this->obj = array();
		$this->_param = array();
		$this->reseller = null;
		$this->publisher = null;
		
	}
	public function index_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3 || (int)$this->_level == 4){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3 || (int)$this->_level == 4){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->card_seri)){
										if(!empty($p->card_code)){
											if(!empty($p->card_type)){
												if(!empty($p->card_amount)){
													$this->reseller = $this->apps->_token_reseller($p->token);
													$this->param['card_seri'] = $p->card_seri;
													$this->param['card_code'] = $p->card_code;
													$this->param['card_type'] = $p->card_type;
													$this->param['card_amount'] = $p->card_amount;
													$this->param['reseller'] = $this->reseller;
													if(!empty($p->client_id)){ $client_id = $p->client_id;}else{ $client_id = $this->reseller;}
													if(!empty($p->publisher)){ $publisher = $p->publisher;}else{ $publisher = $this->reseller; }
													$this->param['publisher'] = $publisher;
													$this->param['client_id'] = $client_id; 
													$this->param['time_tracking'] = time(); 
													$this->param['note'] = handesk_encode(json_encode($this->param));
													$this->obj = $this->apps->_Service_Card_Change_Sendding($this->param);	
													$this->r = $this->apps->_msg_response(10000);
													$this->r['result'] = $this->obj;
												}else{ $this->r = $this->apps->_msg_response(4014);}
											}else{ $this->r = $this->apps->_msg_response(4013);}
										}else{ $this->r = $this->apps->_msg_response(4016);}
									}else{ $this->r = $this->apps->_msg_response(4015);}
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