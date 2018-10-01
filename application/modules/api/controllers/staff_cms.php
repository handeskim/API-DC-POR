<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Staff_cms extends REST_Controller {
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
	public function update_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								if(!empty($p->keys)){
									if(!empty($p->role)){ $this->obj['role'] = (int)$p->role; }
									if(!empty($p->level)){ $this->obj['level'] = (int)$p->level; }
									if((int)$p->role > (int)$this->_role){
										if($p->level != 2){
												$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $this->reseller,))->where_gt('role',$this->_role)->set($this->obj)->update('api_keys');
												$this->prams = array('action'=> 'update','status'=> $this->result,'key_del' => $p->keys,'date_insert'=> date("Y-m-d H:i:s A"),'time_insert'=> time(),'param'=>$p,);
												$this->mongo_db->insert('log_action_developer',$this->prams);
												if(!empty($this->result)){
													$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->result,);
												}else{
													$this->r = array( 'status'=> $this->apps->_msg_response(1002), 'result'=> $this->result,);
												}
										}else{ $this->r = $this->apps->_msg_response(1002);}
									}else{ $this->r = $this->apps->_msg_response(1002);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function del_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									$this->result = $this->apps->_del_staff_cms($p);
								$this->r = $this->result;
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}	
	public function update_status_post(){
		if((int)$this->_role == 1 || (int)$this->_role == 2 ){
			if(!empty($_POST['param'])){
					$p = $this->apps->_params($_POST['param'],$this->_api_key);
					if($p->status == 'Disable'){
						$status = false;
					}else{
						$status = true;
					}
					$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->set(array('status'=>$status,'type'=>$p->type))->update('ask_users');
					$this->r = array( 'status'=> true, 'result'=> $this->result);
					$this->response($this->r);
			}
		}
		$this->response($this->r);
	}
	
	public function transfer_out_post(){
		if((int)$this->_role == 1 || (int)$this->_role == 2 ){
			if(!empty($_POST['param'])){
				$p = $this->apps->_params($_POST['param'],$this->_api_key);
				$client_info = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->get('ask_users');
				if(!empty($client_info)){
					$reseller =  $this->apps->_token_reseller($p->token);
					$full_name = $client_info[0]['full_name'];
					$balancer_client = (int)$client_info[0]['balancer'];
					$money_transfer = (int)$p->money_transfer;
					$balancer_munis = $balancer_client - $money_transfer;
					$this->param = array(
						"money_transfer" => $money_transfer,
						"date_create" => date('Y-m-d H:i:s',time()),
						"time_create" => time(),
						"fee" => 0,
						"total_transfer" => $money_transfer,
						"types" => 'transfer',
						"balancer_clients" => $balancer_client,
						"beneficiary_balancer" => $balancer_client,
						"balancer_plus" => 0,
						"balancer_munis" => $balancer_munis,
						"payer_balancer" => "Admin",
						"payer_id" => "Admin",
						"payer_name" => "Admin",
						"beneficiary_id" => $p->keys,
						"beneficiary" => $full_name,
						"client_id" => $p->keys,
						"client_name" => $full_name,
						"password_transfer" => md5($full_name),
						"reseller" => $reseller,
						"type" => 'withdrawn',
						"transaction" => 'done',
						"bank_name" => "Admin",
						"account_holders" => "Admin",
						"bank_account" => "Admin",
						"provinces_bank" => "Admin",
						"branch_bank" => "Admin",
						"note" => $p->note,
					);
					$transfer = $this->apps->_transfer_minus($balancer_munis,$p->keys,$this->param);
					$this->r = array('status'=>true,'result'=>$transfer);
				}	
			}
		}
		$this->response($this->r);
	}
	public function info_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									$this->result = $this->mongo_db->select(array('username','role','full_name',
									'email','address','city','country','birthday','date_create',
									'time_crate','phone','status',
									))->where(array('_id' => new \MongoId($p->keys)))->get('ask_users');
									
									if(!empty($this->result)){$this->result = $this->result[0];}
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
									$k = $this->mongo_db->where_gt('role',1)->where_gte('time_crate',$date_start)->where_lte('time_crate',$date_end)->get('ask_users');
								}else{
									$k = $this->mongo_db->where(array('reseller'=>$this->reseller,))->where_gte('time_crate',$date_start)->where_lte('time_crate',$date_end)->get('ask_users');
								}
							
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['reseller'])){ $v_reseller = $v['reseller'];}else{$v_reseller = null;}
										// if(!empty($v['client_id'])){ $client_id = $v['client_id'];}else{$client_id = null;}
										if(!empty($v['phone'])){ $phone = $v['phone'];}else{$phone = null;}
										if(!empty($v['email'])){ $email = $v['email'];}else{$email = null;}
										if(!empty($v['type'])){ $type = $v['type'];}else{$type = null;}
										if(!empty($v['username'])){ $username = $v['username'];}else{$username = null;}
										if(!empty($v['full_name'])){ $full_name = $v['full_name'];}else{$full_name = null;}
										if(!empty($v['address'])){ $address = $v['address'];}else{$address = null;}
										if(!empty($v['city'])){ $city = $v['city'];}else{$city = null;}
										if(!empty($v['country'])){ $country = $v['country'];}else{$country = null;}
										if(!empty($v['birthday'])){ $birthday = $v['birthday'];}else{$birthday = null;}
										if(!empty($v['balancer'])){ $balancer = $v['balancer'];}else{$balancer = null;}
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['role'])){ $role = $v['role'];}else{$role = null;}
										if(!empty($v['status'])){ $status = $v['status'];}else{$status = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'client_id' => getObjectId($v['_id']),
											'reseller'=> $v_reseller,
											'email' => $email,
											'type' => $type,
											'username' => $username,
											'phone'=> $phone,
											'full_name'=> $full_name,
											'address'=> $address,
											'city'=> $city,
											'country'=> $country,
											'birthday'=> $birthday,
											'balancer'=> $balancer,
											'date_create'=> $date_create,
											'role'=> $role,
											'status'=> $status
										);
									}
								}
								$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->result,);
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function revenue_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 ){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								if(!empty($p->date_start)){$dstart = date("Y-m-d ",strtotime($p->date_start)).'00:00:00';}else{$dstart = date("Y-m-d ",time()).'00:00:00';}
								if(!empty($p->date_end)){$dend = date("Y-m-d ",strtotime($p->date_end)).'23:59:59';}else{$dend = date("Y-m-d ",time()).'23:59:59';}
								$date_start = strtotime($dstart);
								$date_end =  strtotime($dend);
								if($this->_role == 1 || (int)$this->_role == 2){
									$k = $this->mongo_db->where_gt('role',1)->where_gte('time_crate',$date_start)->where_lte('time_crate',$date_end)->get('ask_users');
								}
								if(!empty($k)){
									$details = array();
									foreach($k as $v){
										$client_id = getObjectId($v['_id']);
										$card_change = $this->mongo_db->where(array('client_id' => $client_id,'transaction_card'=>'done'))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('log_card_change');
										$total_transfer = array();
										$money_transfer = array();
										foreach($card_change as $v_card){
											$total_transfer[] = $v_card['total_transfer'];
											$money_transfer[] = $v_card['money_transfer'];
										}
										$cart = $this->mongo_db->where(array('client_id' => $client_id,'transaction_card'=>'done'))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('cart');
										$total_transfer_cart = array();
										$money_transfer_cart = array();
										foreach($cart as $v_cart){
											$total_transfer_cart[] = $v_cart['TotalOder'];
											$money_transfer_cart[] = $v_cart['MoneyTransfer'];
										}
										$details[] = array(
											'client_id' => $client_id,
											'username' => $v['username'],
											'card_total_transfer' => array_sum($total_transfer),
											'card_money_transfer' => array_sum($money_transfer),
											'cart_money_transfer' => array_sum($money_transfer_cart),
											'cart_total_transfer' => array_sum($total_transfer_cart),
										);
									}
									$this->result = $details;
								}
								$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->result,);
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function details_post(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_POST['param'])){
							$p = $this->apps->_params($_POST['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								if(!empty($p->date_start)){$dstart = date("Y-m-d ",strtotime($p->date_start)).'00:00:00';}else{$dstart = date("Y-m-d ",time()).'00:00:00';}
								if(!empty($p->date_end)){$dend = date("Y-m-d ",strtotime($p->date_end)).'23:59:59';}else{$dend = date("Y-m-d ",time()).'23:59:59';}
								$date_start = strtotime($dstart);
								$date_end =  strtotime($dend);
								///
								$transfer_logs = array();
								$withdrawn_logs = array();
								$cart_logs = array();
								$card_logs = array();
								
								$transfer_logs = $this->mongo_db->where('client_id',$p->client_id)->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('transfer_log');
								$withdrawn_logs = $this->mongo_db->where('client_id',$p->client_id)->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('withdrawal');
								$card_logs = $this->mongo_db->where('client_id',$p->client_id)->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('log_card_change');
								$cart_logs = $this->mongo_db->where('client_id',$p->client_id)->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('cart');
								
								$this->result = array(
									'transfer_logs' => $transfer_logs,
									'withdrawn_logs' => $withdrawn_logs,
									'cart_logs' => $cart_logs,
									'card_logs' => $card_logs,
								);
								$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->result,);
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