<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Cart_cms extends REST_Controller {
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
										$this->result = $this->mongo_db->select(array(
										'Telco','CardQuantity','CardPrice','Type',
										'OrderID','PriceDiscount','deduct',
										'ProductCode','CardName',
										'MoneyTransfer','TotalOder',
										'client_id','full_name','email','phone',
										'date_create','transaction_card',
										))->where(array('_id' => new \MongoId($p->keys)))->get('cart');;
									}
									if(!empty($this->result)){
										$r = array();
										$e = array();
										foreach($this->result as $k=>$v){
											$r = $v;
											$this->obj = $this->result[0];
											$client_id = $v['client_id'];
											$tracking = getObjectId($v['_id']);
											if(!empty($tracking)){
												$transaction_transfer = $this->mongo_db->select(array('response','CustIP','RefNumber'
												))->where(array('RefNumber' => $tracking,))->get('alego_transaction');
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
									$k = $this->mongo_db->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('cart');
								}
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['Telco'])){ $Telco = $v['Telco'];}else{$Telco = null;}
										if(!empty($v['Type'])){ $Type = $v['Type'];}else{$Type = null;}
										if(!empty($v['CardName'])){ $CardName = $v['CardName'];}else{$CardName = null;}
										if(!empty($v['TotalOder'])){ $TotalOder = $v['TotalOder'];}else{$TotalOder = null;}
										if(!empty($v['client_id'])){ $client_id = $v['client_id'];}else{$client_id = null;}
										if(!empty($v['full_name'])){ $full_name = $v['full_name'];}else{$full_name = null;}
										if(!empty($v['phone'])){ $phone = $v['phone'];}else{$phone = null;}
										if(!empty($v['email'])){ $email = $v['email'];}else{$email = null;}
										if(!empty($v['MoneyTransfer'])){ $MoneyTransfer = $v['MoneyTransfer'];}else{$MoneyTransfer = null;}
										if(!empty($v['PriceDiscount'])){ $PriceDiscount = $v['PriceDiscount'];}else{$PriceDiscount = null;}
										if(!empty($v['CardPrice'])){ $CardPrice = $v['CardPrice'];}else{$CardPrice = null;}
										if(!empty($v['transaction_card'])){ $transaction_card = $v['transaction_card'];}else{$transaction_card = null;}
										if(!empty($v['CardQuantity'])){ $CardQuantity = $v['CardQuantity'];}else{$CardQuantity = null;}
									
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'date_create' => $date_create,
											'Telco' => $Telco,
											'Type' => $Type,
											'CardName' => $CardName,
											'TotalOder' => $TotalOder,
											'client_id' => $client_id,
											'full_name' => $full_name,
											'email' => $email,
											'phone' => $phone,
											'MoneyTransfer' => $MoneyTransfer,
											'PriceDiscount' => $PriceDiscount,
											'CardPrice' => $CardPrice,
											'transaction_card' => $transaction_card,
											'CardQuantity' => $CardQuantity,
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