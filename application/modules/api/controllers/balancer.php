<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Balancer extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->param = array();
		$this->objects = array();
		$this->params = array();
		$this->confim = array();
		
		$this->apps = new core;
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->r = $this->apps->_msg_response(200);
		$this->_api_key = $this->_api_key();
		$this->_is_private_key = $this->apps->_is_private_key($this->_api_key());	
		$this->obj = array( 'command'=> array(
				'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
				'secret_key' => '(string) secret key //YOUR API CREATED',
				'auth' => '(string) Password Account // Mật khẩu giao dịch của tài khoản', )
		);
		$this->_param = array();
	}
	public function bank_withdrawal_info_get(){
			if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								if(!empty($p->client_id)){
									$reseller = (string)$this->apps->_token_reseller($p->token);
									$this->objects = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),'reseller'=>$reseller,))->get('ask_users');
									if(!empty($this->objects)){
											$ask_bank = $this->mongo_db->where(array('_id' => new \MongoId($p->bank_id),'client_id' => $p->client_id,'reseller'=>$reseller,))->get('ask_bank');
											if(!empty($ask_bank)){
												$bank = array();
												foreach($ask_bank as $v){
													$bank[] = array(
														'id' => getObjectId($v['_id']),
														'bank_id' => $v['bank_id'],
														'bank_name' => $v['bank_name'],
														'bank_option' => $v['bank_option'],
														'account_holders' => $v['account_holders'],
														'bank_account' => $v['bank_account'],
														'branch_bank' => $v['branch_bank'],
														'provinces_bank' => $v['provinces_bank'],
													);
												}
												$this->r = $this->apps->_result(1000,$bank,$this->_api_key);
											}else{ $this->r = $this->apps->_result(2015,$this->apps->_msg_response(1002),$this->_api_key);}
									}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
								}else{ $this->r = $this->apps->_result(2000,$this->apps->_msg_response(2000),$this->_api_key);}
							}else{ $this->r = $this->apps->_result(2011,$this->apps->_msg_response(2011),$this->_api_key);}
						}else{ $this->r = $this->apps->_result(2000,$this->apps->_msg_response(2000),$this->_api_key);}
					}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
				}else{ $this->r = $this->apps->_result(1001,$this->apps->_msg_response(1001),$this->_api_key);}
			}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
		}else{ $this->r = $this->apps->_result(1001,$this->apps->_msg_response(1001),$this->_api_key);}
		$this->response($this->r);
	}
	public function bank_withdrawal_get(){
			if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								if(!empty($p->client_id)){
									$reseller = (string)$this->apps->_token_reseller($p->token);
									$this->objects = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),'reseller'=>$reseller,))->get('ask_users');
									if(!empty($this->objects)){
									
											$ask_bank = $this->mongo_db->where(array('client_id' => $p->client_id,'reseller'=>$reseller,))->get('ask_bank');
											if(!empty($ask_bank)){
													
												$bank = array();
												foreach($ask_bank as $v){
													$bank[] = array(
														'id' => getObjectId($v['_id']),
														'bank_id' => $v['bank_id'],
														'bank_name' => $v['bank_name'],
														'bank_option' => $v['bank_option'],
														'account_holders' => $v['account_holders'],
														'bank_account' => $v['bank_account'],
														'branch_bank' => $v['branch_bank'],
														'provinces_bank' => $v['provinces_bank'],
													);
												}
												$this->r = $this->apps->_result(1000,$bank,$this->_api_key);
											}else{ $this->r = $this->apps->_result(2015,$this->apps->_msg_response(1002),$this->_api_key);}
									}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
								}else{ $this->r = $this->apps->_result(2000,$this->apps->_msg_response(2000),$this->_api_key);}
							}else{ $this->r = $this->apps->_result(2011,$this->apps->_msg_response(2011),$this->_api_key);}
						}else{ $this->r = $this->apps->_result(2000,$this->apps->_msg_response(2000),$this->_api_key);}
					}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
				}else{ $this->r = $this->apps->_result(1001,$this->apps->_msg_response(1001),$this->_api_key);}
			}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
		}else{ $this->r = $this->apps->_result(1001,$this->apps->_msg_response(1001),$this->_api_key);}
		$this->response($this->r);
	}
	public function withdrawal_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								if(!empty($p->client_id)){
									$reseller = (string)$this->apps->_token_reseller($p->token);
									$client = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),'reseller'=>$reseller,))->get('ask_users');
									if(!empty($client)){
										$balancer = (int)$client[0]['balancer'];
										$client_name = (string)$client[0]['full_name'];
										$money_transfer = (int)$p->money_transfer;
										$fee_withdrawal = 	(int)$this->apps->_withdrawal_fee();
										$min_withdraw = 	(int)$this->apps->_min_withdraw();
										$total_transfer = (int)$money_transfer + (int)$fee_withdrawal;
										if($balancer >= $min_withdraw){
											if($balancer > $total_transfer){
												$balancer_munis =  (int)$balancer - (int)$total_transfer;
												if($balancer_munis > 1000){
													$check_beneficiary = array( '_id' => new \MongoId($reseller),);
													$beneficiary_x = $this->mongo_db->select(array('full_name','balancer'))->where($check_beneficiary)->get('ask_users');
													if(!empty($beneficiary_x)){
															$beneficiary_balancer = (int)$beneficiary_x[0]['balancer'];
															$beneficiary = (string)$beneficiary_x[0]['full_name'];
															$balancer_plus = (int)$beneficiary_balancer + $money_transfer;
															$params = array(
																'money_transfer' => (int)$money_transfer,
																'date_create' => date('Y-m-d H:i:s',time()),
																'time_create' => time(),
																'fee' => (int)$fee_withdrawal,
																'total_transfer' => (int)$total_transfer,
																'balancer_clients' => (int)$balancer,
																'beneficiary_balancer' => (int)$beneficiary_balancer,
																'balancer_plus' => (int)$balancer_plus,
																'balancer_munis' => (int)$balancer_munis,
																'payer_balancer' => $balancer,
																'payer_id' => $p->client_id,
																'beneficiary_id' => $reseller,
																'beneficiary' => $beneficiary,
																'payer_name' => $client_name,
																'client_id' => $p->client_id,
																'client_name' => $client_name,
																'password_transfer' => md5($p->auth_code),
																'reseller' => $reseller,
																'transaction' => 'hold',
																'bank_id' => $p->bank_id,
																'auth' => $p->auth_code,
																'bank_name' => $p->bank_name,
																'account_holders' => $p->account_holders,
																'bank_account' => $p->bank_account,
																'provinces_bank' => $p->provinces_bank,
																'branch_bank' => $p->branch_bank,
																'type' => 'withdrawal',
															);
															$v1 = $this->apps->_transfer_withdrawal_minus($balancer_munis,$p->client_id,$params);
															
															if($v1==true){
																	$this->r =  $this->apps->_msg_response(1999);
															}else{ $this->r = $this->apps->_result(2019,$this->apps->_msg_response(2019),$this->_api_key);}
													}else{ $this->r = $this->apps->_result(2019,$this->apps->_msg_response(2019),$this->_api_key);}
												}else{ $this->r = $this->apps->_result(2021,$this->apps->_msg_response(2021),$this->_api_key);}
											}else{ $this->r = $this->apps->_result(2020,$this->apps->_msg_response(2020),$this->_api_key);}
										}else{ $this->r = $this->apps->_result(2024,$this->apps->_msg_response(2024),$this->_api_key);}
									}else{ $this->r = $this->apps->_result(2000,$this->apps->_msg_response(2000),$this->_api_key);}
								}else{ $this->r = $this->apps->_result(2000,$this->apps->_msg_response(2000),$this->_api_key);}
							}else{ $this->r = $this->apps->_result(2011,$this->apps->_msg_response(2011),$this->_api_key);}
						}else{ $this->r = $this->apps->_result(2000,$this->apps->_msg_response(2000),$this->_api_key);}
					}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
				}else{ $this->r = $this->apps->_result(1001,$this->apps->_msg_response(1001),$this->_api_key);}
			}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
		}else{ $this->r = $this->apps->_result(1001,$this->apps->_msg_response(1001),$this->_api_key);}
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
								if(!empty($p->client_id)){
									$this->param = $this->apps->_balancer_users($p->client_id,$p->token);
									$this->r = $this->apps->_result(1000,$this->param,$this->_api_key);
								}else{ $this->r = $this->apps->_result(2000,$this->apps->_msg_response(2000),$this->_api_key);}
							}else{ $this->r = $this->apps->_result(2011,$this->apps->_msg_response(2011),$this->_api_key);}
						}else{ $this->r = $this->apps->_result(2000,$this->apps->_msg_response(2000),$this->_api_key);}
					}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
				}else{ $this->r = $this->apps->_result(1001,$this->apps->_msg_response(1001),$this->_api_key);}
			}else{ $this->r = $this->apps->_result(1002,$this->apps->_msg_response(1002),$this->_api_key);}
		}else{ $this->r = $this->apps->_result(1001,$this->apps->_msg_response(1001),$this->_api_key);}
		$this->response($this->r);
	}
	public function transfer_confirm_get(){
			if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$authentication = json_decode(handesk_decode($p->authentication));
								$time_die = $authentication->time_die;
								if($time_die > time()){
									$reseller = (string)$this->apps->_token_reseller($p->token);
									$check_clients = array('_id' => new \MongoId($authentication->client_id),'reseller'=> $reseller);
									$client = $this->mongo_db->select(array('full_name','balancer','password'))->where($check_clients)->get('ask_users');
									if(!empty($client)){
										if($p->auth_code === $authentication->auth_code){
											$check_beneficiary = array( '_id' => new \MongoId($authentication->beneficiary_id),);
											$beneficiary = $this->mongo_db->select(array('full_name','balancer'))->where($check_beneficiary)->get('ask_users');
											if(!empty($beneficiary)){
													if(!empty($client[0]['balancer'])){
														$balancer = (int)$client[0]['balancer'];
														$beneficiary_balancer = (int)$beneficiary[0]['balancer'];
														$total_transfer = (int)$authentication->total_transfer;
														if($balancer > $total_transfer){
															$money_transfer = (int)$authentication->money_transfer;
															$balancer_munis =  (int)$balancer - (int)$total_transfer;
															if($balancer_munis > 1000){
																$balancer_plus =  (int)$beneficiary_balancer + (int)$money_transfer;
																$beneficiary_id = $authentication->beneficiary_id;
																$client_id = $authentication->client_id;
																$params = array(
																	'money_transfer'=> (int)$authentication->money_transfer,
																	'date_create'=> $authentication->date_create,
																	'time_create'=> $authentication->time_create,
																	'fee'=> (int)$authentication->fee,
																	'total_transfer'=> (int)$total_transfer,
																	'types'=> 'transfer',
																	'balancer_clients'=> (int)$balancer,
																	'beneficiary_balancer'=> $beneficiary_balancer,
																	'balancer_plus'=> (int)$balancer_plus,
																	'balancer_munis'=> (int)$balancer_munis,
																	'payer_balancer' =>  $balancer,
																	'payer_id' =>  $authentication->client_id,
																	'payer_name'=>  $authentication->client_name,
																	'beneficiary_id'=> $authentication->beneficiary_id,
																	'beneficiary'=> $authentication->beneficiary,
																	'client_id'=> $authentication->client_id,
																	'client_name'=> $authentication->client_name,
																	'password_transfer'=> $authentication->auth_code,
																	'reseller'=> $reseller,
																	'type' => 'transfers',
																	'transaction'=> 'done',
																	'bank_name' => 'Internal transaction',
																	'account_holders' => $authentication->client_name,
																	'bank_account' => $authentication->client_id,
																	'provinces_bank' => 'In system',
																	'branch_bank' => 'In system',
																);
																$v1 = $this->apps->_transfer_minus($balancer_munis,$client_id,$params);
																
																if($v1==true){
																	$params['client_id'] = $authentication->beneficiary_id;
																	$params['account_holders'] = $authentication->beneficiary;
																	$params['bank_account'] = $authentication->beneficiary_id;
																	$v2 = $this->apps->_transfer_plus($balancer_plus,$beneficiary_id,$params);
																	if($v2==true){
																		$this->r = $this->apps->_msg_response(1999);
																	}else{ $this->r = $this->apps->_msg_response(2023);}
																}else{ $this->r = $this->apps->_msg_response(2019);}
															}else{ $this->r = $this->apps->_msg_response(2019); }
														}else{ $this->r = $this->apps->_msg_response(2020);}
													}else{ $this->r = $this->apps->_msg_response(2018);}
											}else{ $this->r = $this->apps->_msg_response(2018);}
										}else{ $this->r = $this->apps->_msg_response(2000);}
									}else{ $this->r = $this->apps->_msg_response(2000);}
								}else{ $this->r = $this->apps->_msg_response(2022);}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function transfer_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								if(!empty($p->client_id) || !empty($p->auth) || !empty($p->beneficiary_id) || !empty($p->money_transfer)){
									$reseller = (string)$this->apps->_token_reseller($p->token);
									$check_clients = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
									$client = $this->mongo_db->select(array('full_name','balancer','password'))->where($check_clients)->get('ask_users');
									if(!empty($client)){
										$check_beneficiary = array( 'email' => $p->beneficiary_id,);
										$beneficiary = $this->mongo_db->select(array('full_name'))->where($check_beneficiary)->get('ask_users');
										if(!empty($beneficiary)){
											$beneficiary_id = getObjectId($beneficiary[0]["_id"]);
											if(!empty($client[0]['balancer'])){
												$balancer = (float)$client[0]['balancer'];
												$fee = 	$this->apps->_transfer_fee();
												$money_transfer = (float)$p->money_transfer;
												$total_transfer = $money_transfer + $fee;
												if($balancer > $total_transfer){
													$balancer_update =  (float)$balancer - (float)$total_transfer;
													if($balancer_update > 1000){
														$this->confim = $this->apps->_msg_response(1000);
														$this->confim['money_transfer'] = $money_transfer;
														$this->confim['time_die'] = time() + 120;
														$this->confim['date_create'] = date("Y-m-d H:i:s A",time());
														$this->confim['time_create'] = time();
														$this->confim['types'] = 'transfer';
														$this->confim['fee'] = (int)$fee;
														$this->confim['total_transfer'] = (int)$total_transfer;
														$this->confim['balancer'] = (int)$balancer;
														$this->confim['balancer_update'] = (int)$balancer_update;
														$this->confim['beneficiary_email'] = $p->beneficiary_id;
														$this->confim['beneficiary_id'] = $beneficiary_id;
														$this->confim['beneficiary'] = $beneficiary[0]['full_name'];
														$this->confim['client_id'] = getObjectId($client[0]['_id']);
														$this->confim['client_name'] = $client[0]['full_name'];
														$this->confim['auth_code'] = auth_code();
														$this->confim['password_transfer'] = $client[0]['password'];
														$this->confim['authentication'] = handesk_encode(json_encode($this->confim));
														$this->r = $this->confim;
													}else{ $this->r = $this->apps->_msg_response(2021);}
												}else{ $this->r = $this->apps->_msg_response(2020);}
											}else{ $this->r = $this->apps->_msg_response(2020);}
										}else{ $this->r = $this->apps->_msg_response(2019);}
									}else{ $this->r = $this->apps->_msg_response(2018);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
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