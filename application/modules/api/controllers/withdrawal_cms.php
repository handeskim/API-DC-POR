<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Withdrawal_cms extends REST_Controller {
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
									$this->result = $this->apps->_del_withdrawal_cms($p);
								$this->r = $this->result;
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
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									$this->reseller = $this->apps->_token_reseller($p->token);
									$authentication = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,'transaction'=>'hold','action'=>'minus'))->get('withdrawal');
									if(!empty($authentication)){
										$client_id = $authentication[0]['client_id'];
									
										//Lây thông tin user cộng tiền//
										$client = $this->mongo_db->where(array('_id' => new \MongoId($client_id),'reseller'=>$this->reseller,))->get('ask_users');
										$check_beneficiary = array( '_id' => new \MongoId($client_id),);
										$beneficiary = $this->mongo_db->select(array('full_name','balancer'))->where($check_beneficiary)->get('ask_users');
										if(!empty($beneficiary)){
											if(!empty($client)){
												$check_payer = array( '_id' => new \MongoId($this->reseller),);
												$payer = $this->mongo_db->select(array('full_name','balancer'))->where($check_payer)->get('ask_users');
												if(!empty($payer)){
												$balancer = (int)$client[0]['balancer'];
												$beneficiary_balancer = (int)$beneficiary[0]['balancer'];
												$total_transfer = (int)$authentication[0]['total_transfer'];
												$balancer_plus = (int)$balancer + (int)$total_transfer;
												$param = array(
													'money_transfer'=> (int)$total_transfer,
													'date_create'=> date("Y-m-d H:i:s",time()),
													'time_create'=> time(),
													'fee'=> 0,
													'total_transfer'=> (int)$total_transfer,
													'types'=> 'withdrawal',
													'balancer_clients'=> (int)$balancer,
													'beneficiary_balancer'=> $beneficiary_balancer,
													'balancer_plus'=> (int)$balancer_plus,
													'balancer_munis'=> 0,
													'payer_balancer' =>  $payer[0]['balancer'],
													'payer_id' =>  $this->reseller,
													'payer_name'=>  $payer[0]['full_name'],
													'beneficiary_id'=> $authentication[0]['client_id'],
													'beneficiary'=> $authentication[0]['client_name'],
													'client_id'=> $authentication[0]['client_id'],
													'client_name'=> $authentication[0]['client_name'],
													'password_transfer'=> null,
													'reseller'=> $this->reseller,
													'type' => 'transfers',
													'transaction'=> 'done',
													'bank_name' => 'Internal transaction',
													'account_holders' => 'Reject Withdrawal',
													'bank_account' => 'Reject Withdrawal',
													'provinces_bank' => 'Reject Withdrawal',
													'branch_bank' => 'Reject Withdrawal',
												);
												$v1 = $this->apps->_transfer_plus($balancer_plus,$client_id,$param);
												// if($v1==true){
												$transfer_transaction = getObjectId($authentication[0]['transfer_transaction']);
												$this->mongo_db->where(array('_id' => new \MongoId($transfer_transaction),'reseller'=>$this->reseller,'transaction'=>'hold'))->set(array('transaction' => 'reject',))->update('transfer_log');
												$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,'transaction'=>'hold'))->set(array('transaction' => 'reject',))->update('withdrawal');
												$this->r = array( 'status'=> $this->apps->_msg_response(1999), 'result'=> $this->result);
												// }else{ $this->r = $this->apps->_msg_response(2019);}
												}else{ $this->r = $this->apps->_msg_response(1001);}
											}else{ $this->r = $this->apps->_msg_response(1002);}
									}else{ $this->r = $this->apps->_msg_response(1001);}
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
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									$this->reseller = $this->apps->_token_reseller($p->token);
									$authentication = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,'transaction'=>'hold','action'=>'minus'))->get('withdrawal');
									if(!empty($authentication)){
										$client_id = $authentication[0]['beneficiary_id'];
										$client = $this->mongo_db->where(array('_id' => new \MongoId($client_id),'reseller'=>$this->reseller,))->get('ask_users');
										$check_beneficiary = array( '_id' => new \MongoId($client_id),);
										$beneficiary = $this->mongo_db->select(array('full_name','balancer'))->where($check_beneficiary)->get('ask_users');
										if(!empty($beneficiary)){
											if(!empty($client)){
												$check_payer = array( '_id' => new \MongoId($this->reseller),);
												$payer = $this->mongo_db->select(array('full_name','balancer'))->where($check_payer)->get('ask_users');
												if(!empty($payer)){
												$balancer = (int)$client[0]['balancer'];
												$beneficiary_balancer = (int)$beneficiary[0]['balancer'];
												$total_transfer = (int)$authentication[0]['money_transfer'];
												$fee = (int)$authentication[0]['fee'];
												$balancer_plus = (int)$balancer + (int)$total_transfer;
												$param = array(
													'money_transfer'=> (int)$total_transfer,
													'date_create'=> date('Y-m-d H:i:s',time()),
													'time_create'=> time(),
													'fee'=> $fee,
													'total_transfer'=> (int)$total_transfer,
													'types'=> 'withdrawal',
													'balancer_clients'=> (int)$balancer,
													'beneficiary_balancer'=> $beneficiary_balancer,
													'balancer_plus'=> (int)$balancer_plus,
													'balancer_munis'=> 0,
													'payer_balancer' =>  $payer[0]['balancer'],
													'payer_id' =>  $this->reseller,
													'payer_name'=>  $payer[0]['full_name'],
													'beneficiary_id'=> $authentication[0]['client_id'],
													'beneficiary'=> $authentication[0]['client_name'],
													'client_id'=> $authentication[0]['client_id'],
													'client_name'=> $authentication[0]['client_name'],
													'password_transfer'=> null,
													'reseller'=> $this->reseller,
													'type' => 'transfers',
													'transaction'=> 'done',
													'bank_name' => 'Internal transaction',
													'account_holders' => 'Agree Withdrawal',
													'bank_account' => 'Agree Withdrawal',
													'provinces_bank' => 'Agree Withdrawal',
													'branch_bank' => 'Agree Withdrawal',
												);
												$v1 = $this->apps->_transfer_plus($balancer_plus,$client_id,$param);
												$transfer_transaction = getObjectId($authentication[0]['transfer_transaction']);
												$this->mongo_db->where(array('_id' => new \MongoId($transfer_transaction),'reseller'=>$this->reseller,'transaction'=>'hold'))->set(array('transaction' => 'done',))->update('transfer_log');
												$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller,'transaction'=>'hold'))->set(array('transaction' => 'done',))->update('withdrawal');
												$this->r = array( 'status'=> $this->apps->_msg_response(1999), 'result'=> $this->result);
												}else{ $this->r = $this->apps->_msg_response(1001);}
											}else{ $this->r = $this->apps->_msg_response(1002);}
									}else{ $this->r = $this->apps->_msg_response(1001);}
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
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								if(!empty($p->keys)){
									if((int)$this->_role == 1 || (int)$this->_role == 2){
										$this->result = $this->mongo_db->select(array('money_transfer','date_create','fee',
										'total_transfer','payer_balancer','balancer_munis',
										'beneficiary' ,'payer_name', 'client_id','client_name', 
										'reseller','transaction','bank_name','account_holders','transfer_transaction',  
										'bank_account','provinces_bank','branch_bank','type','action','date_update_transfer'
										))->where(array('_id' => new \MongoId($p->keys)))->get('withdrawal');;
									}else{
										$this->reseller = $this->apps->_token_reseller($p->token);
										$this->result = $this->mongo_db->select(array('money_transfer','date_create','fee',
										'total_transfer','balancer_munis',
										'payer_balancer' ,'beneficiary' ,'payer_name', 'client_id','client_name', 
										'reseller','transaction','bank_name','account_holders','transfer_transaction', 
										'bank_account','provinces_bank','branch_bank','type','action','date_update_transfer'
										))->where(array('_id' => new \MongoId($p->keys),'reseller'=>$this->reseller))->get('withdrawal');
									}
									if(!empty($this->result)){
											$this->obj = $this->result[0];
											// foreach($this->result as $v){ }
											// $client_id = $v['client_id'];
											// $tracking = $v['transfer_transaction'];
											// if(!empty($tracking)){
												// $transaction_transfer = $this->mongo_db->where(array('note' => $tracking,'client_id'=>$client_id,))->get('transfer_log');
												// if(!empty($transaction_transfer[0])){
													// $this->obj = $transaction_transfer[0];
												// }
											// }
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
									$k = $this->mongo_db->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('withdrawal');
									if(empty($k)){
										$k = $this->mongo_db->where(array('transaction' => 'hold'))->get('withdrawal');
									}
								}else{
									$k = $this->mongo_db->where(array('reseller'=>$this->reseller,))->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('withdrawal');
									if(empty($k)){
										$k = $this->mongo_db->where(array('reseller'=>$this->reseller,'transaction' => 'hold'))->get('withdrawal');
									}
								}
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['money_transfer'])){ $money_transfer = $v['money_transfer'];}else{$money_transfer = null;}
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['fee'])){ $fee = $v['fee'];}else{$fee = null;}
										if(!empty($v['total_transfer'])){ $total_transfer = $v['total_transfer'];}else{$total_transfer = null;}
										if(!empty($v['balancer_clients'])){ $balancer_clients = $v['balancer_clients'];}else{$balancer_clients = null;}
										if(!empty($v['beneficiary_balancer'])){ $beneficiary_balancer = $v['beneficiary_balancer'];}else{$beneficiary_balancer = null;}
										if(!empty($v['balancer_plus'])){ $balancer_plus = $v['balancer_plus'];}else{$balancer_plus = null;}
										if(!empty($v['balancer_munis'])){ $balancer_munis = $v['balancer_munis'];}else{$balancer_munis = null;}
										if(!empty($v['payer_balancer'])){ $payer_balancer = $v['payer_balancer'];}else{$payer_balancer = null;}
										if(!empty($v['payer_id'])){ $payer_id = $v['payer_id'];}else{$payer_id = null;}
										if(!empty($v['beneficiary_id'])){ $beneficiary_id = $v['beneficiary_id'];}else{$beneficiary_id = null;}
										if(!empty($v['beneficiary'])){ $beneficiary = $v['beneficiary'];}else{$beneficiary = null;}
										if(!empty($v['payer_name'])){ $payer_name = $v['payer_name'];}else{$payer_name = null;}
										if(!empty($v['client_id'])){ $client_id = $v['client_id'];}else{$client_id = null;}
										if(!empty($v['client_name'])){ $client_name = $v['client_name'];}else{$client_name = null;}
										if(!empty($v['reseller'])){ $reseller = $v['reseller'];}else{$reseller = null;}
										if(!empty($v['transaction'])){ $transaction = $v['transaction'];}else{$transaction = null;}
										if(!empty($v['bank_id'])){ $bank_id = $v['bank_id'];}else{$bank_id = null;}
										if(!empty($v['bank_name'])){ $bank_name = $v['bank_name'];}else{$bank_name = null;}
										if(!empty($v['account_holders'])){ $account_holders = $v['account_holders'];}else{$account_holders = null;}
										if(!empty($v['bank_account'])){ $bank_account = $v['bank_account'];}else{$bank_account = null;}
										if(!empty($v['provinces_bank'])){ $provinces_bank = $v['provinces_bank'];}else{$provinces_bank = null;}
										if(!empty($v['branch_bank'])){ $branch_bank = $v['branch_bank'];}else{$branch_bank = null;}
										if(!empty($v['type'])){ $type = $v['type'];}else{$type = null;}
										if(!empty($v['status'])){ $status = $v['status'];}else{$status = null;}
										if(!empty($v['action'])){ $action = $v['action'];}else{$action = null;}
										if(!empty($v['transfer_transaction'])){ $transfer_transaction = $v['transfer_transaction'];}else{$transfer_transaction = null;}
										
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'money_transfer' => $money_transfer,
											'date_created' => $date_create,'fee' => $fee,'total_transfer' => $total_transfer,
											'balancer_clients' => $balancer_clients,'beneficiary_balancer' => $beneficiary_balancer,
											'balancer_plus' => $balancer_plus,'balancer_munis' => $balancer_munis,
											'payer_balancer' => $payer_balancer,
											'payer_id' => $payer_id,
											'beneficiary_id' => $beneficiary_id,
											'beneficiary' => $beneficiary,
											'payer_name' => $payer_name,
											'client_id' => $client_id,
											'client_name' => $client_name,
											'reseller' => $reseller,
											'transaction' => $transaction,
											'bank_id' => $bank_id,
											'bank_name' => $bank_name,
											'account_holders' => $account_holders,
											'bank_account' => $bank_account,
											'provinces_bank' => $provinces_bank,
											'branch_bank' => $branch_bank,
											'type' => $type,
											'status' => $status,
											'action' => $action,
											'transfer_transaction' => $transfer_transaction,
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