<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Payments_cms extends REST_Controller {
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
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
							$this->reseller = $this->apps->_token_reseller($p->token);
							$card_info = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->get('history_payments');
							if(!empty($card_info)){
								$this->prams = array(
									'key_del' => $p->keys,
									'date_insert'=> date("Y-m-d H:i:s A"),
									'time_insert'=> time(),
									'reseller'=>$this->reseller,
									'param'=>$p,
								);
								$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->delete('history_payments');
									$this->prams['status'] = $del;
									$this->mongo_db->insert('log_action_developer',$this->prams);
									$this->prams['action'] = 'delete-card-change';
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
	public function cancel_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 ){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								$card_info = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'transaction'=>'done',))->get('history_payments');
								if(!empty($card_info)){
									foreach($card_info as $card){}
									$total_transfer =  $card['total_amount'];
									$client_id =  $card['id_clients'];
									$beneficiary_id =  $card['id_clients'];
									$check_client = array( '_id' => new \MongoId($client_id),);
									$beneficiary = $this->mongo_db->select(array('full_name','balancer'))->where($check_client)->get('ask_users');
									$balancer_clients = (int)$beneficiary[0]['balancer'];
									$balancer_munis =  (int)$balancer_clients - (int)$total_transfer ;
								
								$this->objects['fee']	= 0;
									$this->objects['total_transfer']	= (int)$total_transfer;
									$this->objects['token_service'] = $card['token_service'];
									$this->objects['types']	= 'transfer';
									$this->objects['balancer_clients']	= (int)$balancer_clients;
									$this->objects['beneficiary_balancer']	= $beneficiary[0]['balancer'];
									$this->objects['balancer_plus']	= 0;
									$this->objects['balancer_munis']	=  $balancer_munis;
									$this->objects['payer_balancer'] =  0;
									$this->objects['payer_id'] =  $client_id;
									$this->objects['payer_name']	=  "In System";
									$this->objects['beneficiary_id']	= $this->reseller;
									$this->objects['beneficiary']	=  'Hold Transaction Refill';
									$this->objects['client_name']	= $beneficiary[0]['full_name'];
									$this->objects['password_transfer']	= md5($client_id);
									$this->objects['type'] = 'refill';
									$this->objects['transaction']	= 'done';
									$this->objects['bank_name'] = 'Hold Transaction Refill';
									$this->objects['account_holders'] = $beneficiary[0]['full_name'];
									$this->objects['bank_account'] = $client_id;
									$this->objects['provinces_bank'] = 'Hold Transaction Refill';
									$this->objects['branch_bank'] = 'Hold Transaction Refill';
									$this->apps->_transfer_minus($balancer_munis,$client_id,$this->objects);		
									$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),))->set(array('transaction' => 'hold',))->update('history_payments');
									$this->r = array( 'status'=> $this->result, 'result'=> $this->result);
								}else{ $this->r = $this->apps->_msg_response(2000);}
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
								$card_info = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'transaction'=>'hold',))->get('history_payments');
								if(!empty($card_info)){
									foreach($card_info as $card){}
									$total_transfer =  $card['total_amount'];
									$client_id =  $card['id_clients'];
									$beneficiary_id =  $card['id_clients'];
									$check_client = array( '_id' => new \MongoId($client_id),);
									$beneficiary = $this->mongo_db->select(array('full_name','balancer'))->where($check_client)->get('ask_users');
									$balancer_clients = (int)$beneficiary[0]['balancer'];
									$balancer_plus =  (int)$balancer_clients + (int)$total_transfer ;
								
								$this->objects['fee']	= 0;
									$this->objects['total_transfer']	= (int)$total_transfer;
									$this->objects['token_service'] = $card['token_service'];
									$this->objects['types']	= 'transfer';
									$this->objects['balancer_clients']	= (int)$balancer_clients;
									$this->objects['beneficiary_balancer']	= $beneficiary[0]['balancer'];
									$this->objects['balancer_plus']	= $balancer_plus;
									$this->objects['balancer_munis']	=  0;
									$this->objects['payer_balancer'] =  0;
									$this->objects['payer_id'] =  $client_id;
									$this->objects['payer_name']	=  "In System";
									$this->objects['beneficiary_id']	= $this->reseller;
									$this->objects['beneficiary']	=  'Agree Transaction Refill';
									$this->objects['client_name']	= $beneficiary[0]['full_name'];
									$this->objects['password_transfer']	= md5($client_id);
									$this->objects['type'] = 'refill';
									$this->objects['transaction']	= 'done';
									$this->objects['bank_name'] = 'Agree Transaction Refill';
									$this->objects['account_holders'] = $beneficiary[0]['full_name'];
									$this->objects['bank_account'] = $client_id;
									$this->objects['provinces_bank'] = 'Agree Transaction Refill';
									$this->objects['branch_bank'] = 'Agree Transaction Refill';
									$this->apps->_transfer_plus($balancer_plus,$client_id,$this->objects);		
									$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'transaction'=>'hold',))->set(array('transaction' => 'done',))->update('history_payments');
									$this->r = array( 'status'=> $this->result, 'result'=> $this->result);
								}else{ $this->r = $this->apps->_msg_response(2000);}
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
										$this->result = $this->mongo_db->select(array('order_code',
										'id_clients','payment_method','buyer_fullname','buyer_email',
										'buyer_mobile','payment_type','order_description',
										'total_amount','bank_code','date_create',
										'service_name','transaction','status','token_service',
										))->where(array('_id' => new \MongoId($p->keys)))->get('history_payments');;
									}else{
										$this->reseller = $this->apps->_token_reseller($p->token);
										$this->result = $this->mongo_db->select(array('order_code',
										'id_clients','payment_method','buyer_fullname','buyer_email',
										'buyer_mobile','payment_type','order_description',
										'total_amount','bank_code','date_create',
										'service_name','transaction','status','token_service',
										))->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller))->get('history_payments');
									}
								
									if(!empty($this->result)){
									
										$r = array();
										$e = array();
										foreach($this->result as $k=>$v){
											$r = $v;
											$transfer_transaction = getObjectId($v['_id']);
											$client_id = $v['id_clients'];
											if($v['transaction'] == 'done'){
												if(!empty($v['transaction'])){
													$transaction_transfer = $this->mongo_db->select(array(
															'transaction_service','types','balancer_clients',
															'beneficiary_balancer','balancer_plus','balancer_munis', 'payer_balancer','payer_id','payer_name',
															'beneficiary_id','beneficiary','client_name','type','transaction','bank_name',
															'account_holders','bank_account','provinces_bank','branch_bank','status','action',
															'date_update_transfer','time_update_transfer'
													))->where(array('Tracking' => $transfer_transaction,'client_id'=>$client_id,))->get('transfer_log');
												
													if(!empty($transaction_transfer[0])){
														foreach($transaction_transfer as $y=>$x){$e = $x;}
													}
												}
											}
										}
										$this->obj = array_merge($r,$e);
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
									$k = $this->mongo_db->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('history_payments');
									if(empty($k)){
										$k = $this->mongo_db->where(array('transaction' => 'hold'))->get('history_payments');
									}
								}
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['payment_method'])){ $payment_method = $v['payment_method'];}else{$payment_method = null;}
										if(!empty($v['id_clients'])){ $id_clients = $v['id_clients'];}else{$id_clients = null;}
										if(!empty($v['order_code'])){ $order_code = $v['order_code'];}else{$order_code = null;}
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['buyer_fullname'])){ $buyer_fullname = $v['buyer_fullname'];}else{$buyer_fullname = null;}
										if(!empty($v['buyer_email'])){ $buyer_email = $v['buyer_email'];}else{$buyer_email = null;}
										if(!empty($v['buyer_mobile'])){ $buyer_mobile = $v['buyer_mobile'];}else{$buyer_mobile = null;}
										if(!empty($v['payment_type'])){ $payment_type = $v['payment_type'];}else{$payment_type = null;}
										if(!empty($v['order_description'])){ $order_description = $v['order_description'];}else{$order_description = null;}
										if(!empty($v['total_amount'])){ $total_amount = $v['total_amount'];}else{$total_amount = null;}
										if(!empty($v['service_name'])){ $service_name = $v['service_name'];}else{$service_name = null;}
										if(!empty($v['transaction'])){ $transaction = $v['transaction'];}else{$transaction = null;}
										if(!empty($v['status'])){ $status = $v['status'];}else{$status = null;}
										if(!empty($v['token_service'])){ $token_service = $v['token_service'];}else{$token_service = null;}
										if(!empty($v['bank_code'])){ $bank_code = $v['bank_code'];}else{$bank_code = null;}
										
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'id_clients' => $id_clients,
											'payment_method' => $payment_method,
											'order_code' => $order_code,
											'date_create' => $date_create,
											'buyer_fullname' => $buyer_fullname,
											'buyer_mobile' => $buyer_mobile,
											'buyer_email' => $buyer_email,
											'payment_type' => $payment_type,
											'order_description' => $order_description,
											'total_amount' => $total_amount,
											'bank_code' => $bank_code,
											'service_name' => $service_name,
											'transaction' => $transaction,
											'status' => $status,
											'token_service' => $token_service,
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