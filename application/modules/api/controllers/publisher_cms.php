<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Publisher_cms extends REST_Controller {
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
	public function del_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 ){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
							$this->reseller = $this->apps->_token_reseller($p->token);
							$card_info = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,))->get('publisher');
							if(!empty($card_info)){
								$this->prams = array(
									'key_del' => $p->keys,
									'date_insert'=> date("Y-m-d H:i:s A"),
									'time_insert'=> time(),
									'reseller'=>$this->reseller,
									'param'=>$p,
								);
								$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $this->reseller,))->delete('publisher');
									$this->prams['status'] = $del;
									$this->mongo_db->insert('log_action_developer',$this->prams);
									$this->prams['action'] = 'delete-publisher';
									$this->r = $this->apps->_msg_response(1000);
									
							}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function agree_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 ){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								$card_info = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,))->get('publisher');
								if(!empty($card_info)){
									if(!empty($p->levels)){
										$prams_update = array('levels' => (int)$p->levels,);
										$this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,))->set($prams_update)->update('publisher');
										$this->r = $this->apps->_msg_response(1000);
									}else{$this->r = $this->apps->_msg_response(2000);}
								}else{$this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}	
	
	public function info_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 ){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								if(!empty($p->keys)){
									if((int)$this->_role == 1 || (int)$this->_role == 2){
										$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->get('publisher');
										if(!empty($this->result)){
											$this->obj = $this->result[0];
										}
									}
									$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->obj);
								}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	
	public function rose_get(){
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
									$publisher = $this->mongo_db->get('publisher');
									$card_total = array();
									$details = array();
									foreach($publisher as $v_publisher){
										$client_id = $v_publisher['client_id'];
										$username = $v_publisher['username'];
										$email = $v_publisher['email'];
										$full_name = $v_publisher['full_name'];
										$partner = $v_publisher['partner'];
										$levels = (int)$v_publisher['levels'];
										$rose_partner = $this->apps->_rose_partner();
										if($levels == 1){
											$rose = $this->apps->_rose_client();
										}else if($levels == 2){
											$rose = $this->apps->_rose_reseller();
										}else{
											$rose = 0;
										}
										
										$card_change = $this->mongo_db->where(array('publisher' => $client_id,'transaction_card'=>'done'))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('log_card_change');
										$details = array();
										$card_change_rose = array();
										foreach($card_change as $v_card){
											$card_money = (int)$v_card['card_amount'] * ((int)$v_card['card_deduct']/100);
											$card_money_rose = (int)$v_card['card_amount'] - (int)$card_money;
											$rose_confim = $rose * $card_money_rose;
											$card_change_rose[] = (int)$rose_confim;
											$details[] = array(
												'card_seri' => $v_card['card_seri'],
												'card_code' => $v_card['card_code'],
												'card_amount' => $v_card['card_amount'],
												'card_deduct' => $v_card['card_deduct'],
												'date_create' => $v_card['date_create'],
												'transaction_card' => $v_card['transaction_card'],
												'Telco' => null,
												'CardQuantity' =>  null,
												'CardPrice' =>  null,
												'MoneyTransfer' =>  null,
											);
										}
										$cart = $this->mongo_db->where(array('publisher' => $client_id,'transaction_card'=>'done'))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('cart');
										$cart_rose = array();
										foreach($cart as $v_cart){
											$MoneyTransfer = (int)$v_cart['MoneyTransfer'];
											$rose_confim_cart = $rose * $MoneyTransfer;
											$cart_rose[] = (int)$rose_confim_cart;
											$details[] = array(
												'card_seri' => null,
												'card_code' => null,
												'card_amount' => null,
												'card_deduct' => null,
												'Telco' => $v_cart['Telco'],
												'CardQuantity' => $v_cart['CardQuantity'],
												'CardPrice' => $v_cart['CardPrice'],
												'MoneyTransfer' => $v_cart['MoneyTransfer'],
												'date_create' => $v_cart['date_create'],
												'transaction_card' => $v_cart['transaction_card'],
											);
										}
										$total_rose = array_sum($card_change_rose) + array_sum($cart_rose);
										$card_total[] = array(
											'full_name' => $full_name,
											'username' => $username,
											'client_id' => $client_id,
											'partner' => $partner,
											'cart_rose' => array_sum($cart_rose),
											'card_change_rose' => array_sum($card_change_rose),
											'total_rose' => $total_rose,
											'total_rose_partner' => $total_rose * $rose_partner,
											'date_start' => $dstart,
											'time_start' => $date_start,
											'time_end' => $date_end,
											'date_end' => $dend,
											);
									}
									$this->result = $card_total;
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
	
	public function info_rose_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								if(!empty($p->date_start)){$dstart = date("Y-m-d ",$p->date_start).'00:00:00';}else{$dstart = date("Y-m-d ",time()).'00:00:00';}
								if(!empty($p->date_end)){$dend = date("Y-m-d ",$p->date_end).'23:59:59';}else{$dend = date("Y-m-d ",time()).'23:59:59';}
								$date_start = strtotime($dstart);
								$date_end =  strtotime($dend);
								$client_id = $p->keys;
								$publisher = $this->mongo_db->where(array('client_id'=>$client_id))->get('publisher');
								if(!empty($publisher)){
									foreach($publisher as $v_publisher){}
									if($this->_role == 1 || (int)$this->_role == 2){
										$card_total = array();
										$details = array();
											$levels = (int)$v_publisher['levels'];
											if($levels == 1){ $rose = $this->apps->_rose_client(); }else if($levels == 2){ $rose = $this->apps->_rose_reseller();}else{ $rose = 0;}
											$card_change = $this->mongo_db->where(array('publisher' => $client_id,'transaction_card'=>'done'))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('log_card_change');
											$details = array();
											$card_change_rose = array();
											foreach($card_change as $v_card){
												$card_money = (int)$v_card['card_amount'] * ((int)$v_card['card_deduct']/100);
												$card_money_rose = (int)$v_card['card_amount'] - (int)$card_money;
												$rose_confim = $rose * $card_money_rose;
												$card_change_rose[] = (int)$rose_confim;
												$details[] = array(
													'type' => 'Card Change',
													'card_seri' => $v_card['card_seri'],
													'card_code' => $v_card['card_code'],
													'card_amount' => $v_card['card_amount'],
													'card_deduct' => $v_card['card_deduct'],
													'total_transfer' => $v_card['total_transfer'],
													'date_create' => $v_card['date_create'],
													'transaction_card' => $v_card['transaction_card'],
													'Telco' => '-',
													'CardQuantity' =>  1,
													'rose' =>  $rose,
													'money_rose' =>  $rose_confim,
												);
											}
											$cart = $this->mongo_db->where(array('publisher' => $client_id,'transaction_card'=>'done'))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('cart');
											$cart_rose = array();
											foreach($cart as $v_cart){
												$MoneyTransfer = (int)$v_cart['MoneyTransfer'];
												$rose_confim_cart = $rose * $MoneyTransfer;
												$cart_rose[] = (int)$rose_confim_cart;
												$details[] = array(
													'type' => 'Card Buy',
													'card_seri' => '-',
													'card_code' => '-',
													'card_amount' => $v_cart['CardPrice'],
													'card_deduct' => $v_cart['deduct'] * 100,
													'Telco' => $v_cart['Telco'],
													'CardQuantity' => $v_cart['CardQuantity'],
													'total_transfer' => $v_cart['MoneyTransfer'],
													'date_create' => $v_cart['date_create'],
													'transaction_card' => $v_cart['transaction_card'],
													'rose' =>  $rose,
													'money_rose' =>  $rose_confim_cart,
												);
											}
											$card_total[] = array(
												'client_id' => $client_id,
												'cart_rose' => array_sum($cart_rose),
												'card_change_rose' => array_sum($card_change_rose),
												'total_rose' => array_sum($card_change_rose) + array_sum($cart_rose),
												'date_start' => $dstart,
												'time_start' => $date_start,
												'time_end' => $date_end,
												'date_end' => $dend,
												'date_end' => $dend,
												'details' => $details,
												);
										$this->result = $card_total;
									}
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
									$k = $this->mongo_db->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('publisher');
								}
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['client_id'])){ $client_id = $v['client_id'];}else{$client_id = null;}
										if(!empty($v['partner'])){ $partner = $v['partner'];}else{$partner = null;}
										if(!empty($v['full_name'])){ $full_name = $v['full_name'];}else{$full_name = null;}
										if(!empty($v['username'])){ $username = $v['username'];}else{$username = null;}
										if(!empty($v['email'])){ $email = $v['email'];}else{$email = null;}
										if(!empty($v['levels'])){ $levels = $v['levels'];}else{$levels = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'date_create' => $date_create,
											'partner' => $partner,
											'client_id' => $client_id,
											'full_name' => $full_name,
											'email' => $email,
											'username' => $username,
											'levels' => $levels,
										);
									}
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