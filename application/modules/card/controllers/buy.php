<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Buy extends REST_Controller {
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
		$this->publisher =  null;
		$this->obj = array();
		$this->_param = array();
		$this->cart = array();
	}
	public function index_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3 || (int)$this->_level == 4){
				if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3 || (int)$this->_level == 4){
					if(!empty($_GET['param'])){
						$p = $this->apps->_params($_GET['param'],$this->_api_key);
					
						if(!empty($p->token)){
							if(!empty($p->keys)){
								if(!empty($p->quantity)){
									if(!empty($p->cardprice)){
											$this->reseller = $this->apps->_token_reseller($p->token);
											$this->param['reseller'] = $this->reseller;
											if(!empty($p->client_id)){ $client_id = $p->client_id;}else{ $client_id = $this->reseller;}
											if(!empty($p->publisher)){ $this->publisher = $p->publisher;}else{ $this->publisher = null; }
											$this->param['publisher'] = $this->publisher;
											$this->param['client_id'] = $client_id; 
											$this->param['time_tracking'] = time(); 
											///// CHECK INFO Telco///
											$TelcoInfo = $this->TelcoInfo($p->keys);
											
											if(!empty($TelcoInfo)){
											$types = $TelcoInfo['types'];
											if($types=="DT"){
												$Telco = $TelcoInfo['telco'];
												$this->cart['Telco'] = $TelcoInfo['telco'];
												$this->cart['Type'] = $TelcoInfo['type'];
												$this->cart['ProductCode'] = $TelcoInfo['ProductCode'];
												$ProductCode = $TelcoInfo['ProductCode'];
												$CardQuantity = (int)$p->quantity;
												$CardPrice = (int)$p->cardprice;
												$TotalOder = $CardQuantity * $CardPrice;
												$this->cart['Type'] = $TelcoInfo['type'];
												$deduct = (($TelcoInfo['deduct'])/100);
												$rose = (($TelcoInfo['rose'])/100);
												$PriceDiscount = $TotalOder * $deduct;
												$MoneyAfterDiscount = $TotalOder - $PriceDiscount;
												$PriceRose = $MoneyAfterDiscount * $rose;
												$MoneyTransfer = $MoneyAfterDiscount - $PriceRose;
												///////////////
												$milliseconds = round(microtime(true) * 1000);
												$this->cart['OrderID'] = $this->cart['Telco'].'-'.md5($client_id .sha1('-'.time().'-'.$this->cart['Type'])).time().'-'.$milliseconds;
												$this->cart['PriceDiscount'] = $PriceDiscount;
												$this->cart['time_create'] = time();
												$this->cart['date_create'] = date("Y-m-d H:i:s",time());
												$this->cart['deduct'] = $deduct;
												$this->cart['rose'] = $TelcoInfo['rose'];
												$this->cart['CardPrice'] = $CardPrice;
												$this->cart['CardName'] = $TelcoInfo['name'];
												$this->cart['CardTitle'] = $TelcoInfo['title'];
												$this->cart['PriceRose'] = $PriceRose;
												$this->cart['MoneyTransfer'] = $MoneyTransfer;
												$this->cart['TotalOder'] = $TotalOder;
												$this->cart['CardQuantity'] = $CardQuantity;
												$this->cart['publisher'] = $this->publisher;
												$this->cart['client_id'] = $client_id;
												
												/////////////// INFO CLIENT /////////////////////
												$this->profile = $this->profile_client($client_id);
												if(!empty($this->profile)){
													$this->cart['full_name'] = $this->profile['full_name'];
													$this->cart['email'] = $this->profile['email'];
													$this->cart['phone'] = $this->profile['phone'];
													$this->cart['transaction_card'] = "hold";
													if(isset($p->mobile)){ $this->cart['CustMobile'] = $p->mobile; }
													/////////// CHECK BALANCER /////////////
													$balancer = (int)$this->profile['balancer'];
													
														if($balancer >  1000){
															if($balancer > $MoneyTransfer){
															$balancer_munis = $balancer - $MoneyTransfer;
																if($balancer_munis > 1000){
																$check_OrderCode = $this->mongo_db->where(array('OrderID' => $this->cart['OrderID'],))->get('cart');
																	if(empty($check_OrderCode)){
																	$traking_orders = $this->mongo_db->insert('cart',$this->cart);
																		if($traking_orders==true){
																		$this->param = array(
																			"Tracking_Orders" => getObjectId($traking_orders),
																			"money_transfer" => (int)$MoneyTransfer,
																			"date_create" => date("Y-m-d H:i:s",time()),
																			"time_create" => time(),
																			"fee" => 0,
																			"total_transfer" => (int)$MoneyTransfer,
																			"balancer_clients" => $balancer,
																			"beneficiary_balancer" => 0,
																			"balancer_plus" => 0,
																			"balancer_munis" => (int)$balancer_munis,
																			"payer_balancer" => 0,
																			"payer_id" => 0,
																			"beneficiary_id" => $this->reseller,
																			"beneficiary" => "Buy Card",
																			"payer_name" => "Buy Card",
																			"client_id" => $client_id,
																			"client_name" => $this->profile['full_name'],
																			"password_transfer" => md5($client_id),
																			"reseller" => $this->reseller,
																			"transaction" => "done",
																			"bank_id" => $client_id,
																			"auth" => md5($client_id),
																			"bank_name" => "Buy Card",
																			"account_holders" => $this->profile['full_name'],
																			"bank_account" => md5($client_id),
																			"provinces_bank" => "Buy Card",
																			"branch_bank" => "Buy Card",
																			"type" => "buy",
																		);
																		$v1 = $this->apps->_transfer_minus($balancer_munis,$client_id,$this->param);
																	
																			if($v1==true){
																			$RefNumber = getObjectId($traking_orders);
																			$this->cart['RefNumber'] = getObjectId($traking_orders);
																			$this->_param['Type'] = $TelcoInfo['type'];
																			$this->_param['publisher'] = $this->publisher;
																			$this->_param['client_id'] = $client_id; 
																			$this->_param['RefNumber'] = $RefNumber;
																			$this->_param['CardPrice'] = $CardPrice;
																			if(isset($p->mobile)){ $this->_param['CustMobile'] = $p->mobile; }
																			$this->_param['CardQuantity'] = $CardQuantity;
																			$this->_param['ProductCode'] = $ProductCode;
																			$this->_param['Telco'] = $Telco;
																			$this->_param['CustIP'] = $this->input->ip_address();
																			$this->_param['note'] = handesk_encode(json_encode($this->_param));
																			$Func = 'buyPrepaidCards';
																			$check_cart = $this->mongo_db->where(array('_id' => new \MongoId($RefNumber),'transaction_card'=>'hold'))->get('cart');
																				if(!empty($check_cart)){
																					$this->obj = $this->apps->_Service_Alego_ByCard_Sendding($this->_param,$Func);
																					if(!empty($this->obj)){
																						$this->r = $this->apps->_msg_response(1000);
																						$this->r['trace'] = $this->obj;
																						$this->r['cart'] =  $RefNumber;
																					}else{ 
																						$this->r = $this->apps->_msg_response(4105);
																						$this->r['result'] = $this->obj;
																						$this->r['trace'] =  $RefNumber;
																					}
																				}else{
																					$this->r = $this->apps->_msg_response(4106);
																					$this->r['result'] = $this->obj;
																					$this->r['trace'] = $RefNumber;
																				}
																			}else{
																				$this->mongo_db->where(array('_id' => new \MongoId($RefNumber),))->set(array('transaction_card'=>'reject'))->update('cart');
																				$this->r = $this->apps->_msg_response(4106); 
																			}
																		}else{ $this->r = $this->apps->_msg_response(4106);}
																	}else{ $this->r = $this->apps->_msg_response(4106);}
																}else{ $this->r = $this->apps->_msg_response(2021);}
															}else{ $this->r = $this->apps->_msg_response(2020);}
														}else{ $this->r = $this->apps->_msg_response(2021);}
													//////////////////////////////////////////////////////
													}else{ $this->r = $this->apps->_msg_response(1002);}
											}else{ $this->r = $this->apps->_msg_response(2000);}
										}else{ $this->r = $this->apps->_msg_response(4100);}
									}else{ $this->r = $this->apps->_msg_response(4101);}
								}else{ $this->r = $this->apps->_msg_response(4103);}
							}else{ $this->r = $this->apps->_msg_response(4015);}
						}else{ $this->r = $this->apps->_msg_response(2011);}
					}else{ $this->r = $this->apps->_msg_response(2000);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function game_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3 || (int)$this->_level == 4){
				if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3 || (int)$this->_level == 4){
					if(!empty($_GET['param'])){
						$p = $this->apps->_params($_GET['param'],$this->_api_key);
						if(!empty($p->token)){
							if(!empty($p->keys)){
								if(!empty($p->quantity)){
									if(!empty($p->cardprice)){
											$this->reseller = $this->apps->_token_reseller($p->token);
											$this->param['reseller'] = $this->reseller;
											if(!empty($p->client_id)){ $client_id = $p->client_id;}else{ $client_id = $this->reseller;}
											if(!empty($p->publisher)){ $this->publisher = $p->publisher;}else{ $this->publisher = null; }
											$this->param['publisher'] = $this->publisher;
											$this->param['client_id'] = $client_id; 
											$this->param['time_tracking'] = time(); 
											///// CHECK INFO Telco///
											$TelcoInfo = $this->TelcoInfo($p->keys);
											if(!empty($TelcoInfo)){
											$types = $TelcoInfo['types'];
											if($types=="GAME"){
												$Telco = $TelcoInfo['telco'];
												$this->cart['Telco'] = $TelcoInfo['telco'];
												$this->cart['Type'] = $TelcoInfo['type'];
												$this->cart['ProductCode'] = $TelcoInfo['ProductCode'];
												$ProductCode = $TelcoInfo['ProductCode'];
												$CardQuantity = (int)$p->quantity;
												$CardPrice = (int)$p->cardprice;
												$TotalOder = $CardQuantity * $CardPrice;
												$this->cart['Type'] = $TelcoInfo['type'];
												$deduct = (($TelcoInfo['deduct'])/100);
												$rose = (($TelcoInfo['rose'])/100);
												$PriceDiscount = $TotalOder * $deduct;
												$MoneyAfterDiscount = $TotalOder - $PriceDiscount;
												$PriceRose = $MoneyAfterDiscount * $rose;
												$MoneyTransfer = $MoneyAfterDiscount - $PriceRose;
												///////////////
												$milliseconds = round(microtime(true) * 1000);
												$this->cart['OrderID'] = $this->cart['Telco'].'-'.md5($client_id .sha1('-'.time().'-'.$this->cart['Type'])).time().'-'.$milliseconds;;
												$this->cart['PriceDiscount'] = $PriceDiscount;
												$this->cart['deduct'] = $deduct;
												$this->cart['rose'] = $TelcoInfo['rose'];
												$this->cart['CardPrice'] = $CardPrice;
												$this->cart['CardName'] = $TelcoInfo['name'];
												$this->cart['CardTitle'] = $TelcoInfo['title'];
												$this->cart['PriceRose'] = $PriceRose;
												$this->cart['MoneyTransfer'] = $MoneyTransfer;
												$this->cart['TotalOder'] = $TotalOder;
												$this->cart['CardQuantity'] = $CardQuantity;
												$this->cart['publisher'] = $this->publisher;
												$this->cart['client_id'] = $client_id;
												/////////////// INFO CLIENT /////////////////////
												$this->profile = $this->profile_client($client_id);
												if(!empty($this->profile)){
													$this->cart['full_name'] = $this->profile['full_name'];
													$this->cart['email'] = $this->profile['email'];
													$this->cart['phone'] = $this->profile['phone'];
													$this->cart['transaction_card'] = "hold";
													if(isset($p->mobile)){ $this->cart['CustMobile'] = $p->mobile; }
													/////////// CHECK BALANCER /////////////
													$balancer = (int)$this->profile['balancer'];
														if($balancer >  1000){
															if($balancer > $MoneyTransfer){
															$balancer_munis = $balancer - $MoneyTransfer;
																if($balancer_munis > 1000){
																$check_OrderCode = $this->mongo_db->where(array('OrderID' => $this->cart['OrderID'],))->get('cart');
																	if(empty($check_OrderCode)){
																	$traking_orders = $this->mongo_db->insert('cart',$this->cart);
																		if($traking_orders==true){
																		$this->param = array(
																			"Tracking_Orders" => getObjectId($traking_orders),
																			"money_transfer" => (int)$MoneyTransfer,
																			"date_create" => date("Y-m-d H:i:s",time()),
																			"time_create" => time(),
																			"fee" => 0,
																			"total_transfer" => (int)$MoneyTransfer,
																			"balancer_clients" => $balancer,
																			"beneficiary_balancer" => 0,
																			"balancer_plus" => 0,
																			"balancer_munis" => (int)$balancer_munis,
																			"payer_balancer" => 0,
																			"payer_id" => 0,
																			"beneficiary_id" => $this->reseller,
																			"beneficiary" => "Buy Card",
																			"payer_name" => "Buy Card",
																			"client_id" => $client_id,
																			"client_name" => $this->profile['full_name'],
																			"password_transfer" => md5($client_id),
																			"reseller" => $this->reseller,
																			"transaction" => "done",
																			"bank_id" => $client_id,
																			"auth" => md5($client_id),
																			"bank_name" => "Buy Card",
																			"account_holders" => $this->profile['full_name'],
																			"bank_account" => md5($client_id),
																			"provinces_bank" => "Buy Card",
																			"branch_bank" => "Buy Card",
																			"type" => "buy",
																		);
																		$v1 = $this->apps->_transfer_minus($balancer_munis,$client_id,$this->param);
																			if($v1==true){
																			$RefNumber = getObjectId($traking_orders);
																			$this->_param['publisher'] = $this->publisher;
																			$this->_param['client_id'] = $client_id; 
																			$this->_param['RefNumber'] = $RefNumber;
																			$this->_param['CardPrice'] = $CardPrice;
																			$this->_param['CardQuantity'] = $CardQuantity;
																			$this->_param['ProductCode'] = $ProductCode;
																			$this->_param['CustIP'] = $this->input->ip_address();
																			$this->_param['note'] = handesk_encode(json_encode($this->_param));
																			$Func = 'buyPrepaidCards';
																			$check_cart = $this->mongo_db->where(array('_id' => new \MongoId($RefNumber),'transaction_card'=>'hold'))->get('cart');
																				if(!empty($check_cart)){
																					$this->obj = $this->apps->_Service_Alego_ByCard_Sendding($this->_param,$Func);
																					var_dump($this->obj);
																					die;
																					if(!empty($this->obj)){
																						$this->r = $this->apps->_msg_response(1000);
																						$this->r['trace'] = $this->obj;
																						$this->r['cart'] =  $RefNumber;
																					}else{ 
																						$this->r = $this->apps->_msg_response(4105);
																						$this->r['result'] = $this->obj;
																						$this->r['trace'] =  $RefNumber;
																					}
																				}else{
																					$this->r = $this->apps->_msg_response(4106);
																					$this->r['result'] = $this->obj;
																					$this->r['trace'] = $RefNumber;
																				}
																			}else{
																				$this->mongo_db->where(array('_id' => new \MongoId($RefNumber),))->set(array('transaction_card'=>'reject'))->update('cart');
																				$this->r = $this->apps->_msg_response(4106); 
																			}
																		}else{ $this->r = $this->apps->_msg_response(4106);}
																	}else{ $this->r = $this->apps->_msg_response(4106);}
																}else{ $this->r = $this->apps->_msg_response(2021);}
															}else{ $this->r = $this->apps->_msg_response(2020);}
														}else{ $this->r = $this->apps->_msg_response(2021);}
													//////////////////////////////////////////////////////
													}else{ $this->r = $this->apps->_msg_response(1002);}
											}else{ $this->r = $this->apps->_msg_response(2000);}
										}else{ $this->r = $this->apps->_msg_response(4100);}
									}else{ $this->r = $this->apps->_msg_response(4101);}
								}else{ $this->r = $this->apps->_msg_response(4103);}
							}else{ $this->r = $this->apps->_msg_response(4015);}
						}else{ $this->r = $this->apps->_msg_response(2011);}
					}else{ $this->r = $this->apps->_msg_response(2000);}
				}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function payment_buycard_orders_post($cart){
			$v1 = $this->apps->_transfer_minus($balancer_munis,$p->client_id,$this->param);
			if($v1==true){
				$this->r = array('status'=>1000,'result'=>getObjectId($traking_orders),'msg'=>$this->apps->_msg_response(1000));
			}else{  
				return $this->_param(); 
			}
	}
	private function profile_client($keys){
		try{
			$this->obj = $this->mongo_db->where(array('_id' => new \MongoId($keys),'status'=>true))->get("ask_users");
			if(isset($this->obj)){
				if(!empty($this->obj[0])){
					return $this->obj[0];
				}else{ return $this->_param();}
			}else{ return $this->_param();}
		}catch (Exception $e) { return $this->_param(); }
	}
	private function TelcoInfo($keys){
		try{
			$this->obj = $this->mongo_db->where(array('_id' => new \MongoId($keys),'status'=>'active'))->get("Telco");
			if(isset($this->obj)){
				if(!empty($this->obj[0])){
					return $this->obj[0];
				}else{ return $this->_param();}
			}else{ return $this->_param();}
		}catch (Exception $e) { return $this->_param(); }
	}
	
}


?>