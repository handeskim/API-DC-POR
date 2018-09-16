<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Revenue_cms extends REST_Controller {
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
	
	
	
	public function index_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								if(!empty($p->date_start)){$dstart = date("Y-m-d ",strtotime($p->date_start)).'00:00:00';}else{$dstart = date("Y-m-d ",time()).'00:00:00';}
								if(!empty($p->date_end)){$dend = date("Y-m-d ",strtotime($p->date_end)).'23:59:59';}else{$dend = date("Y-m-d ",time()).'23:59:59';}
								$date_start = strtotime($dstart);
								$date_end =  strtotime($dend);
								if($this->_role == 1 || (int)$this->_role == 2){
									$total = array();
									$cart_total = array();
									$cart_transfer = array();
									$fee_transfer = $this->mongo_db->where(array('transaction'=>'done'))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('transfer_log');
									foreach($fee_transfer as $v_fee){
										$total[] = (int)$v_fee['fee'];
									}
									$cart = $this->mongo_db->where(array('transaction_card'=>'done'))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('cart');
									foreach($cart as $v_cart){
										$cart_total[] = (int)$v_cart['TotalOder'];
										$cart_transfer[] = (int)$v_cart['MoneyTransfer'];
									}
									$card = $this->mongo_db->where(array('transaction_card'=>'done'))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('log_card_change');
										$card_total = array();
									$card_transfer = array();
									foreach($card as $v_card){
										$card_total[] = (int)$v_card['money_transfer'];
										$card_transfer[] = (int)$v_card['total_transfer'];
									}
									
									$exchange_rate = array_sum($cart_total) - array_sum($cart_transfer);
									$exchange_card_rate = array_sum($card_total) - array_sum($card_transfer);
									$fee =  array_sum($total);
									$this->result = array(
										'card_total' => array_sum($card_total),
										'card_transfer' => array_sum($card_transfer),
										'exchange_card_rate' => $exchange_card_rate,
										'cart_total' => array_sum($cart_total),
										'cart_transfer' => array_sum($cart_transfer),
										'exchange_rate' => $exchange_rate,
										'fee' => $fee,
										'total_revenue' => $exchange_rate + $fee,
									);
								}
								$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->result);
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