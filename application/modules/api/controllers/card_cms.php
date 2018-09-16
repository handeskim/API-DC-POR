<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Card_cms extends REST_Controller {
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
							$card_info = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,))->get('log_card_change');
							if(!empty($card_info)){
								$this->prams = array(
									'key_del' => $p->keys,
									'date_insert'=> date("Y-m-d H:i:s A"),
									'time_insert'=> time(),
									'reseller'=>$this->reseller,
									'param'=>$p,
								);
								$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $this->reseller,))->delete('log_card_change');
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
										$card_info = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,'transaction_card'=>'done',))->get('log_card_change');
									
										if(!empty($card_info)){
											foreach($card_info as $card){}
											$client_id =  $card['client_id'];
											$beneficiary_id =  $card['client_id'];
											$check_client = array( '_id' => new \MongoId($client_id),);
											$beneficiary = $this->mongo_db->select(array('full_name','balancer'))->where($check_client)->get('ask_users');
											$balancer_clients = (int)$beneficiary[0]['balancer'];
											$tracking = $card['tracking'];
											$transaction_info = $this->mongo_db->where(array('tracking'=>$tracking,'reseller'=>$this->reseller,'transaction_card'=>'done',))->get('transfer_log');
											
											foreach($transaction_info as $tcard){}
											
											$this->objects['card_seri'] = $tcard['card_seri'];
											$this->objects['card_code'] = $tcard['card_code'];
											$this->objects['card_type'] = (int)$tcard['card_type'];
											$this->objects['card_amount'] = (int)$tcard['card_amount'];
											$this->objects['client_id'] = $tcard['client_id'];
											$this->objects['publisher'] = $tcard['publisher'];
											$this->objects['reseller'] = $tcard['reseller'];
											$this->objects['card_deduct'] =  $tcard['card_deduct'];
											$this->objects['card_rose'] =  $tcard['card_rose'];
											$this->objects['date_create'] = date("Y-m-d H:i:s",time());
											$this->objects['time_create'] = time();
											$this->objects['transaction_service'] = $tcard['transaction_service'];
											$this->objects['money_transfer'] = (int)$tcard['money_transfer'];
											$this->objects['money_rose'] = (int)$tcard['money_rose'];
											$this->objects['note'] = $tcard['note'];
											$this->objects['card_status'] = $tcard['card_status'];
											$this->objects['card_message'] =  $tcard['card_message'];
											$this->objects['tracking'] = $tcard['tracking'];
											$total_transfer = (int)$tcard['total_transfer'];
											////////////////////Transaction Insert///////////////////////////
											$balancer_munis =  $balancer_clients - (int)$total_transfer ;
											$this->objects['fee']	= (int)$tcard['fee'];
											$this->objects['total_transfer']	= (int)$total_transfer;
											$this->objects['transaction_card'] = $tcard['transaction_card'];
											$this->objects['types']	= 'transfer';
											$this->objects['balancer_clients']	= (int)$balancer_clients;
											$this->objects['beneficiary_balancer']	= $beneficiary[0]['balancer'];
											$this->objects['balancer_plus']	= 0;
											$this->objects['balancer_munis']	=  $balancer_munis;
											$this->objects['payer_balancer'] =  0;
											$this->objects['payer_id'] =  $tcard['client_id'];
											$this->objects['payer_name']	=  $beneficiary[0]['full_name'];
											$this->objects['beneficiary_id']	= $tcard['reseller'];
											$this->objects['beneficiary']	=  'Hold Transaction Card';
											$this->objects['client_name']	= $beneficiary[0]['full_name'];
											$this->objects['password_transfer']	= md5($balancer_clients);
											$this->objects['type'] = 'card_transfers';
											$this->objects['transaction']	= 'done';
											$this->objects['bank_name'] = 'Internal Card ';
											$this->objects['account_holders'] = $beneficiary[0]['full_name'];
											$this->objects['bank_account'] = $tcard['client_id'];
											$this->objects['provinces_bank'] = 'Hold Transaction Card';
											$this->objects['branch_bank'] = 'Hold Transaction Card';
											$this->apps->_transfer_minus($balancer_munis,$beneficiary_id,$this->objects);	
											$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$tcard['reseller'],'client_id'=>$tcard['client_id']))->set(array('transaction_card' => 'hold',))->update('log_card_change');
											$this->r = array( 'status'=> $this->result, 'result'=> $this->result);
											// $this->r = $this->result;
										}else{ $this->r = $this->apps->_msg_response(2011);}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}	
	public function agreed_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 ){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
										$this->reseller = $this->apps->_token_reseller($p->token);
										$card_info = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,'transaction_card'=>'hold',))->get('log_card_change');
										if(!empty($card_info)){
											foreach($card_info as $card){}
											$client_id =  $card['client_id'];
											$beneficiary_id =  $card['client_id'];
											$check_client = array( '_id' => new \MongoId($client_id),);
											$beneficiary = $this->mongo_db->select(array('full_name','balancer'))->where($check_client)->get('ask_users');
											$balancer_clients = (int)$beneficiary[0]['balancer'];
											$tracking = $card['tracking'];
											$transaction_info = $this->mongo_db->where(array('tracking'=>$tracking,'reseller'=>$this->reseller,))->get('transfer_log');
											
											foreach($transaction_info as $tcard){}
											
											$this->objects['card_seri'] = $tcard['card_seri'];
											$this->objects['card_code'] = $tcard['card_code'];
											$this->objects['card_type'] = (int)$tcard['card_type'];
											$this->objects['card_amount'] = (int)$tcard['card_amount'];
											$this->objects['client_id'] = $tcard['client_id'];
											$this->objects['publisher'] = $tcard['publisher'];
											$this->objects['reseller'] = $tcard['reseller'];
											$this->objects['card_deduct'] =  $tcard['card_deduct'];
											$this->objects['card_rose'] =  $tcard['card_rose'];
											$this->objects['date_create'] = date("Y-m-d H:i:s",time());
											$this->objects['time_create'] = time();
											$this->objects['transaction_service'] = $tcard['transaction_service'];
											$this->objects['money_transfer'] = (int)$tcard['money_transfer'];
											$this->objects['money_rose'] = (int)$tcard['money_rose'];
											$this->objects['note'] = $tcard['note'];
											$this->objects['card_status'] = $tcard['card_status'];
											$this->objects['card_message'] =  $tcard['card_message'];
											$this->objects['tracking'] = $tcard['tracking'];
											$total_transfer = (int)$tcard['total_transfer'];
											////////////////////Transaction Insert///////////////////////////
											$balancer_munis =  $balancer_clients + (int)$total_transfer ;
											$this->objects['fee']	= (int)$tcard['fee'];
											$this->objects['total_transfer']	= (int)$total_transfer;
											$this->objects['transaction_card'] = $tcard['transaction_card'];
											$this->objects['types']	= 'transfer';
											$this->objects['balancer_clients']	= (int)$balancer_clients;
											$this->objects['beneficiary_balancer']	= $beneficiary[0]['balancer'];
											$this->objects['balancer_plus']	= 0;
											$this->objects['balancer_munis']	=  $balancer_munis;
											$this->objects['payer_balancer'] =  0;
											$this->objects['payer_id'] =  $tcard['client_id'];
											$this->objects['payer_name']	=  $beneficiary[0]['full_name'];
											$this->objects['beneficiary_id']	= $tcard['reseller'];
											$this->objects['beneficiary']	=  'Hold Transaction Card';
											$this->objects['client_name']	= $beneficiary[0]['full_name'];
											$this->objects['password_transfer']	= md5($balancer_clients);
											$this->objects['type'] = 'card_transfers';
											$this->objects['transaction']	= 'done';
											$this->objects['bank_name'] = 'Internal Card ';
											$this->objects['account_holders'] = $beneficiary[0]['full_name'];
											$this->objects['bank_account'] = $tcard['client_id'];
											$this->objects['provinces_bank'] = 'Hold Transaction Card';
											$this->objects['branch_bank'] = 'Hold Transaction Card';
											$this->apps->_transfer_plus($balancer_munis,$beneficiary_id,$this->objects);	
											$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$tcard['reseller'],'client_id'=>$tcard['client_id']))->set(array('transaction_card' => 'done',))->update('log_card_change');
											$this->r = array( 'status'=> $this->result, 'result'=> $this->result);
										}else{ $this->r = $this->apps->_msg_response(2011);}
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
										$this->result = $this->mongo_db->select(array('card_seri','card_code','card_type',
										'card_amount','client_id','publisher','tracking','note',
										'reseller' ,'card_deduct', 'card_rose','date_create', 
										'card_status','card_message','transaction_service','transaction_card',	))->where(array('_id' => new \MongoId($p->keys)))->get('log_card_change');;
									}else{
										$this->reseller = $this->apps->_token_reseller($p->token);
										$this->result = $this->mongo_db->select(array('card_seri','card_code','card_type',
										'card_amount','client_id','publisher','tracking','note',
										'reseller' ,'card_deduct', 'card_rose','date_create', 
										'card_status','card_message','transaction_service','transaction_card',))->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller))->get('log_card_change');
									}
									
									if(!empty($this->result)){
										$r = array();
										$e = array();
										foreach($this->result as $k=>$v){
											$r = $v;
											// $this->obj = $this->result[0];
											$client_id = $v['client_id'];
											$tracking = $v['tracking'];
											if(!empty($tracking)){
												$node_info = $this->mongo_db->where(array('_id' => new \MongoId($tracking)))->get('doithe_transaction');
												$transaction_transfer = $this->mongo_db->select(array(
														'transaction_service','types','balancer_clients',
														'beneficiary_balancer','balancer_plus','balancer_munis', 'payer_balancer','payer_id','payer_name',
														'beneficiary_id','beneficiary','client_name','type','transaction','bank_name',
														'account_holders','bank_account','provinces_bank','branch_bank','status','action',
														'date_update_transfer','time_update_transfer'
												))->where(array('note' => $tracking,'client_id'=>$client_id,))->get('transfer_log');
												if(!empty($node_info[0]['ip'])){ $this->obj['ip'] = $node_info[0]['ip'];}else{ $this->obj['ip'] = null;}
												if(!empty($transaction_transfer[0])){
													foreach($transaction_transfer as $y=>$x){$e = $x;}
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
									$k = $this->mongo_db->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('log_card_change');
									
								}else{
									$k = $this->mongo_db->where(array('reseller'=>$this->reseller,))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('log_card_change');
								}
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['card_seri'])){ $card_seri = $v['card_seri'];}else{$card_seri = null;}
										if(!empty($v['card_code'])){ $card_code = $v['card_code'];}else{$card_code = null;}
										if(!empty($v['card_type'])){ $card_type = $v['card_type'];}else{$card_type = null;}
										if(!empty($v['card_amount'])){ $card_amount = $v['card_amount'];}else{$card_amount = null;}
										if(!empty($v['client_id'])){ $client_id = $v['client_id'];}else{$client_id = null;}
										if(!empty($v['publisher'])){ $publisher = $v['publisher'];}else{$publisher = null;}
										if(!empty($v['reseller'])){ $reseller = $v['reseller'];}else{$reseller = null;}
										if(!empty($v['card_deduct'])){ $card_deduct = $v['card_deduct'];}else{$card_deduct = null;}
										if(!empty($v['card_rose'])){ $card_rose = $v['card_rose'];}else{$card_rose = null;}
										if(!empty($v['card_status'])){ $card_status = $v['card_status'];}else{$card_status = null;}
										if(!empty($v['card_message'])){ $card_message = $v['card_message'];}else{$card_message = null;}
										if(!empty($v['transaction_service'])){ $transaction_service = $v['transaction_service'];}else{$transaction_service = null;}
										if(!empty($v['transaction_card'])){ $transaction_card = $v['transaction_card'];}else{$transaction_card = null;}
										if(!empty($v['note'])){ $note = $v['note'];}else{$note = null;}
										if(!empty($v['tracking'])){ $tracking = $v['tracking'];}else{$tracking = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'note' => $note,
											'date_create' => $date_create,
											'card_seri' => $card_seri,
											'card_code' => $card_code,
											'card_type' => $card_type,
											'card_amount' => $card_amount,
											'client_id' => $client_id,
											'publisher' => $publisher,
											'reseller' => $reseller,
											'card_deduct' => $card_deduct,
											'tracking' => $tracking,
											'card_rose' => $card_rose,
											'card_status' => $card_status,
											'card_message' => $card_message,
											'transaction_service' => $transaction_service,
											'transaction_card' => $transaction_card,
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