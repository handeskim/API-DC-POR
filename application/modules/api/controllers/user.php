<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class User extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->apps = new core;
		$this->params = array();
		$this->resller = array();
		$this->px = array();
		$this->client_id = null;
		$this->param = array();
		$this->pub = array();
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->r = $this->apps->_msg_response(200);
		$this->obj = array( 'command'=> array(
				'merchant_id' => '(string) Merchant ID //YOUR API CREATED',
				'secret_key' => '(string) secret key //YOUR API CREATED',
				'auth' => '(string) Password Account // Mật khẩu giao dịch của tài khoản', )
		);
	}
	public function info_get(){
		$this->apps->_logs_user_api($_GET,'info_get');
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									$this->params = $this->apps->_user_info($p->client_id);
									if(!empty($this->params)){
											$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
									}else{ $this->r = $this->apps->_msg_response(2012);}	
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function bank_info_get(){
			$this->apps->_logs_user_api($_GET,'bank_info_get');
			if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									if(!empty($p->token)){
											$this->params  = $this->apps->_users_bank($p);
											if(!empty($this->params)){
												 $this->r = $this->apps->_msg_response(1000);
												 $this->r['info_bank'] = $this->params;
												// $this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
											}else{ $this->r = $this->apps->_msg_response(2014);}	
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}	
	public function bank_del_get(){
		$this->apps->_logs_user_api($_GET,'bank_del');
			if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									if(!empty($p->token)){
										if(!empty($p->bank_id)){
												$this->params  = $this->apps->_users_bank_del($p);
												if(!empty($this->params)){
													$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
												}else{ $this->r = $this->apps->_msg_response(2014);}	
										}else{ $this->r = $this->apps->_msg_response(2000);}
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function bank_get(){
		$this->apps->_logs_user_api($_GET,'bank_get');
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									if(!empty($p->token)){
										if(!empty($p->auth)){
											if(!empty($p->bank_id) || !empty($p->bank_name) || !empty($p->branch_bank)  || !empty($p->bank_account)  || !empty($p->account_holders) ){
												if(!empty($p->provinces_bank)){
													$this->params  = $this->apps->_users_bank_add($p);
													if(!empty($this->params)){
															$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
													}else{ $this->r = $this->apps->_msg_response(2014);}	
												}else{ $this->r = $this->apps->_msg_response(2000);}
											}else{ $this->r = $this->apps->_msg_response(2000);}
										}else{ $this->r = $this->apps->_msg_response(2008);}
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function change_password_get(){
		$this->apps->_logs_user_api($_GET,'change_password');
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									if(!empty($p->token)){
										if(!empty($p->password_old) || !empty($p->password_new)){
											$this->params = $this->apps->_users_change_password($p);
												if(!empty($this->params)){
													$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
												}else{ $this->r = $this->apps->_msg_response(2000);}
										}else{ $this->r = $this->apps->_msg_response(2000);}
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function developer_get(){
		$this->apps->_logs_user_api($_GET,'change_password');
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									if(!empty($p->token)){
										$this->params = $this->apps->_users_developer($p);
										if(!empty($this->params)){
										$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
										}else{ $this->r = $this->apps->_msg_response(2000);}
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function developer_create_get(){
		$this->apps->_logs_user_api($_GET,'change_password');
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									if(!empty($p->token)){
										$this->params = $this->apps->_users_developer_create($p);
										if(!empty($this->params)){
											$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
										}else{ $this->r = $this->apps->_msg_response(2000);}
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function change_auth_get(){
		$this->apps->_logs_user_api($_GET,'change_password');
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									if(!empty($p->token)){
										if(!empty($p->password_old) || !empty($p->password_new)){
											$this->params = $this->apps->_users_change_auth($p);
												if(!empty($this->params)){
													$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
												}else{ $this->r = $this->apps->_msg_response(2000);}
										}else{ $this->r = $this->apps->_msg_response(2000);}
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function update_get(){
		$this->apps->_logs_user_api($_GET,'update_get');
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									if(!empty($p->token)){
										if(!empty($p->auth)){
											$this->params  = $this->apps->_users_update($p);
											if(!empty($this->params)){
												$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
											}else{ $this->r = $this->apps->_msg_response(2013);}	
										}else{ $this->r = $this->apps->_msg_response(2008);}
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function Passwdupdate_get(){
		$this->apps->_logs_user_api($_GET,'update_get');
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
								if(!empty($p->client_id)){
									if(!empty($p->token)){
										if(!empty($p->password)){
											$this->params  = $this->apps->_users_update($p);
											if(!empty($this->params)){
												$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
											}else{ $this->r = $this->apps->_msg_response(2013);}	
										}else{ $this->r = $this->apps->_msg_response(2008);}
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function login_get(){
		$this->apps->_logs_user_api($_GET,'login_get');
		if(!empty($this->_level)){
				if(!empty($this->_role)){
					if((int)$this->_level == 2 || (int)$this->_level == 3){
						if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 2){
							if(!empty($_GET['param'])){
								$p = $this->apps->_params($_GET['param'],$this->_api_key());
							
									if(!empty($p->token)){
											if(!empty($p->username)){
												if(!empty($p->password)){
													$this->params = $this->apps->_user_login($p);
													if(!empty($this->params)){
														$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
													}else{ $this->r = $this->apps->_msg_response(2013);}
												}else{ $this->r = $this->apps->_msg_response(2011);}												
											}else{ $this->r = $this->apps->_msg_response(2011);}												
									}else{ $this->r = $this->apps->_msg_response(2011);}
							}else{ $this->r = $this->apps->_msg_response(2000);}
						}else{ $this->r = $this->apps->_msg_response(1002);}
					}else{ $this->r = $this->apps->_msg_response(1001);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function create_get(){
		$this->apps->_logs_user_api($_GET,'create_get');
		if((int)$this->_level == 2 || (int)$this->_level == 3){
			if((int)$this->_role == 1 || (int)$this->_role == 2 ){
				if(!empty($_GET['param'])){								
					$p = $this->apps->_params($_GET['param'],$this->_api_key());
					if(!empty($p->email) || !empty($p->username) || !empty($p->password) || !empty($p->auth) || !empty($p->phone) || !empty($p->full_name) ){
						if(email_regex($p->email)==true){
							if(strlen($p->password) > 5 || strlen($p->password) < 32){
								if(strlen($p->auth) > 5 || strlen($p->auth) < 32){
									if(!empty($p->address)){ $address = $p->address;}else{ $address = null; }
									if(!empty($p->city)){ $city = $p->city;}else{ $city = null; }
									if(!empty($p->country)){ $country = $p->country;}else{ $country = null; }
									if(!empty($p->birthday)){ $birthday = $p->birthday; }else{ $birthday = null; }
									if(!empty($p->avatar)){ $avatar = $p->avatar; }else{ $avatar = null; }
									if(!empty($p->token)){
										$resller = $this->apps->_token_reseller($p->token);
										if(!empty($resller)){
											$check = $this->apps->_user_create_check($p->username,$p->email);
											if(empty($check)){
												$this->param = array(
													'email'=> $p->email,
													'username'=> $p->username,
													'password'=> md5($p->password),
													'auth'=> md5($p->auth),
													'phone'=> $p->phone,
													'full_name'=> $p->full_name,
													'address'=> $address,
													'city'=> $city,
													'type' => "Member",
													'country'=> $country,
													'balancer'=> 0,
													'birthday'=> $birthday,
													'date_create'=> date('Y-m-d H:i:s',time()),
													'time_crate'=> time(),
													'avatar'=> $avatar,
													'role'=> 4,
													'status'=> true,
													'partner'=> null,
													'publisher'=> null,
													'rose'=> 0,
													'reseller'=> $resller,
												);
												
												if(isset($p->partner)){
													if(!empty($p->partner)){
														$partner = $this->mongo_db->where(array('_id' => new \MongoId($p->partner)))->get('ask_users');
														if(!empty($partner)){ $this->param['partner'] = $p->partner;}
													}
												}
												
												$this->params = $this->apps->_action_insert_user($this->param);
												
												if(!empty($this->params)){
													if(isset($p->partner)){
														if(!empty($p->partner)){
															$partner = $this->mongo_db->where(array('_id' => new \MongoId($p->partner)))->get('ask_users');
															if(!empty($partner)){ 
																$this->client_id = getObjectid($this->params);
																
																$this->pub['partner'] = $p->partner;
																$this->pub['reseller'] = $resller;
																$this->pub['client_id'] = $this->client_id;
																$this->pub['full_name'] = $p->full_name;
																$this->pub['email'] = $p->email;
																$this->pub['phone'] = $p->phone;
																$this->pub['levels'] = 1;
																$this->pub['username'] = $p->username;
																$this->pub['date_create'] = date('Y-m-d H:i:s',time());
																$this->pub['time_create'] = time();
																$this->pub['balancer'] = 0;
																$this->pub['ip'] = $this->input->ip_address();
															
																$this->px = $this->mongo_db->insert('publisher',$this->pub);
																if(!empty($this->px)){
																	$update_publicer = array(
																		'publisher' => getObjectid($this->px),
																		'partner' => $p->partner,
																	);
																	$this->mongo_db->where(array('_id' => new \MongoId($this->client_id)))->set($update_publicer)->update('ask_users');
																}
															}
														}
													}
													$this->r = $this->apps->_msg_response(1000);
													$this->r = $this->apps->_result(1000,array($this->params),$this->_api_key());
												}else{ $this->r = $this->apps->_msg_response(199);}
											}else{ $this->r = $this->apps->_msg_response(2012);}
										}else{ $this->r = $this->apps->_msg_response(2011);}
									}else{ $this->r = $this->apps->_msg_response(2011);}
								}else{ $this->r = $this->apps->_msg_response(2007);}
							}else{ $this->r = $this->apps->_msg_response(2005);}
						}else{ $this->r = $this->apps->_msg_response(2002);}
					}else{ $this->r = $this->apps->_msg_response(2001);}
				}else{ $this->r = $this->apps->_msg_response(2000);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	
	
}


?>