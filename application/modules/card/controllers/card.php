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
		$this->note = 'doi-the-'.$this->_api_key .time();
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
							if(!empty($p->token)){
									if(!empty($p->card_seri)){
										if(!empty($p->card_code)){
											if(!empty($p->card_type)){
												// if(!empty($p->card_amount)){
												// if(!empty($p->card_amount)){
												if(strlen($p->card_seri) > 8 ){
													if(strlen($p->card_seri) < 18 ){
														$this->reseller = $this->apps->_token_reseller($p->token);
														$this->param['card_seri'] = $p->card_seri;
														$this->param['card_code'] = $p->card_code;
														$this->param['card_type'] = $p->card_type;
														if((int)$p->card_type = 1){
																$this->param['card_amount'] = $p->card_amount;
														}else{
																$this->param['card_amount'] = null;
														}
													
														$this->param['reseller'] = $this->reseller;
														if(!empty($p->client_id)){ $client_id = $p->client_id;}else{ $client_id = $this->reseller;}
														if(!empty($p->publisher)){ $publisher = $p->publisher;}else{ $publisher = null; }
														$this->param['publisher'] = $publisher;
														$this->param['client_id'] = $client_id; 
														$this->param['time_tracking'] = time(); 
														$check_seri = $this->check_seri($this->param);
														if($check_seri==true){
																$this->obj = $this->apps->_Service_Card_Change_Sendding($this->param);
																
																$this->r = $this->apps->_msg_response(10000);
																$this->r['result'] = $this->obj;
														}else{ 
															 $this->r = $this->apps->_msg_response(4026);
														}
													// }else{ $this->r = $this->apps->_msg_response(4014);}
													}else{ $this->r = $this->apps->_msg_response(2000);}
												}else{ $this->r = $this->apps->_msg_response(2000);}
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
	
	private function check_seri($p){
		$card_type = $p['card_type'];
		$card_seri = $p['card_seri'];
		$check_block_seri = $this->mongo_db->where(array('card_seri'=>$card_seri,'card_type'=>$card_type))->get('block_seri_card');
		if(empty($check_block_seri)){
			$client_id = $p['client_id'];
			$check_seri_database =  $this->mongo_db->where(array('card_seri'=>$card_seri,'transaction_card'=>'done','card_type'=>$card_type))->get('log_card_change');
			if(empty($check_seri_database)){
				$block_seri = $this->mongo_db->where(array('card_seri'=>$card_seri,'transaction_card'=>'reject','card_type'=>$card_type))->get('log_card_change');
				if(!empty($block_seri)){
					if(count($block_seri) > 5){
						$this->mongo_db->insert('block_seri_card',array('card_seri'=>$card_seri,'card_type'=>$card_type,'time_create'=>time(),'client_id'=>$client_id));
						return false;
					}else{ return true;}
				}else{ return true; }
			}else{ return false; }
		}else{ return false; }
	}
}


?>