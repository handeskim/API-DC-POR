<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
require_once(APPPATH.'/libraries/nusoap/nusoap'.EXT); 
class Bycard extends REST_Controller {
	
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
		$this->obj = array();
		$this->_param = array();
	}
	private function refun($p){
		if(!empty($p)){
			if(!empty($p->client_id)){
				if(!empty($p->RefNumber)){
					if(!empty($p->full_name)){
						
					}
				}
			}
		}
	}
	
	public function pincode_post(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3 || (int)$this->_level == 4){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3 || (int)$this->_level == 4){
						if(!empty($_POST['param'])){
							$p = $this->apps->_params($_POST['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->CardPrice)){
										if(!empty($p->CardQuantity)){
											if(!empty($p->ProductCode)){
												if(!empty($p->Telco)){
												if(!empty($p->RefNumber)){
													$this->reseller = $this->apps->_token_reseller($p->token);
														$this->param['reseller'] = $this->reseller;
														if(!empty($p->client_id)){ $client_id = $p->client_id;}else{ $client_id = $this->reseller;}
														if(!empty($p->publisher)){ $publisher = $p->publisher;}else{ $publisher = null; }
														$this->param['publisher'] = $publisher;
														$this->param['client_id'] = $client_id; 
														$this->param['RefNumber'] = $p->RefNumber;
														$this->param['CardPrice'] = $p->CardPrice;
														$this->param['CardQuantity'] = $p->CardQuantity;
														$this->param['ProductCode'] = $p->ProductCode;
														$this->param['Telco'] = $p->Telco;
														$this->param['CustIP'] = $this->input->ip_address();
														$this->param['note'] = handesk_encode(json_encode($this->param));
														$Func = 'buyPrepaidCards';
														$check_cart = $this->mongo_db->where(array('_id' => new \MongoId($p->RefNumber),'transaction_card'=>'hold'))->get('cart');
														if(!empty($check_cart)){
															$this->obj = $this->apps->_Service_Alego_ByCard_Sendding($this->param,$Func);
															if(!empty($this->obj)){
																$this->r = $this->apps->_msg_response(1000);
																$this->r['trace'] = $this->obj;
																$this->r['result'] = $this->obj;
																$this->r['cart'] =  $p->RefNumber;
															}else{ 
																$this->r = $this->apps->_msg_response(4105);
																$this->r['result'] = $this->obj;
																$this->r['trace'] =  $p->RefNumber;
															}
														}else{
																$this->r = $this->apps->_msg_response(4106);
																$this->r['result'] = $this->obj;
																$this->r['trace'] = $p->RefNumber;
														}
													}else{ $this->r = $this->apps->_msg_response(4104);}
												}else{ $this->r = $this->apps->_msg_response(4103);}
											}else{ $this->r = $this->apps->_msg_response(4102);}
										}else{ $this->r = $this->apps->_msg_response(4101);}
									}else{ $this->r = $this->apps->_msg_response(4100);}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function game_post(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3 || (int)$this->_level == 4){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3 || (int)$this->_level == 4){
						if(!empty($_POST['param'])){
							$p = $this->apps->_params($_POST['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->CardPrice)){
										if(!empty($p->CardQuantity)){
											if(!empty($p->ProductCode)){
												// if(!empty($p->Telco)){
												if(!empty($p->RefNumber)){
													$this->reseller = $this->apps->_token_reseller($p->token);
														$this->param['reseller'] = $this->reseller;
														if(!empty($p->client_id)){ $client_id = $p->client_id;}else{ $client_id = $this->reseller;}
														if(!empty($p->publisher)){ $publisher = $p->publisher;}else{ $publisher = null; }
														$this->param['publisher'] = $publisher;
														$this->param['client_id'] = $client_id; 
														$this->param['RefNumber'] = $p->RefNumber;
														$this->param['CardPrice'] = $p->CardPrice;
														$this->param['CardQuantity'] = $p->CardQuantity;
														$this->param['ProductCode'] = $p->ProductCode;
														// $this->param['Telco'] = $p->Telco;
														$this->param['CustIP'] = $this->input->ip_address();
														$this->param['note'] = handesk_encode(json_encode($this->param));
														$Func = 'buyPrepaidCards';
														$check_cart = $this->mongo_db->where(array('_id' => new \MongoId($p->RefNumber),'transaction_card'=>'hold'))->get('cart');
														if(!empty($check_cart)){
															$this->obj = $this->apps->_Service_Alego_ByCard_Sendding($this->param,$Func);
															if(!empty($this->obj)){
																$this->r = $this->apps->_msg_response(1000);
																$this->r['trace'] = $this->obj;
																$this->r['result'] = $this->obj;
																$this->r['cart'] =  $p->RefNumber;
															}else{ 
																$this->r = $this->apps->_msg_response(4105);
																$this->r['result'] = $this->obj;
																$this->r['trace'] =  $p->RefNumber;
															}
														}else{
																$this->r = $this->apps->_msg_response(4106);
																$this->r['result'] = $this->obj;
																$this->r['trace'] = $p->RefNumber;
														}
													}else{ $this->r = $this->apps->_msg_response(4104);}
												// }else{ $this->r = $this->apps->_msg_response(4103);}
											}else{ $this->r = $this->apps->_msg_response(4102);}
										}else{ $this->r = $this->apps->_msg_response(4101);}
									}else{ $this->r = $this->apps->_msg_response(4100);}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function topup_post(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3 || (int)$this->_level == 4){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3 || (int)$this->_level == 4){
						if(!empty($_POST['param'])){
							$p = $this->apps->_params($_POST['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->CardPrice)){
										if(!empty($p->CardQuantity)){
											if(!empty($p->ProductCode)){
												if(!empty($p->Telco)){
													if(!empty($p->RefNumber)){
														if(!empty($p->CustMobile)){
															$this->reseller = $this->apps->_token_reseller($p->token);
															$this->param['reseller'] = $this->reseller;
															if(!empty($p->client_id)){ $client_id = $p->client_id;}else{ $client_id = $this->reseller;}
															if(!empty($p->publisher)){ $publisher = $p->publisher;}else{ $publisher = null; }
															$this->param['publisher'] = $publisher;
															$this->param['client_id'] = $client_id; 
															$this->param['time_tracking'] = time(); 
															$this->param['note'] = handesk_encode(json_encode($this->param));
															$this->param['ProductCode'] = $p->ProductCode;
															$this->param['RefNumber'] = $p->RefNumber;
															$this->param['Type'] = $p->Type;
															$this->param['Telco'] = $p->Telco;
															$this->param['CustMobile'] = $p->CustMobile;
															$this->param['CustIP'] = $this->input->ip_address();
															$this->param['CardPrice'] = $p->CardPrice;
															$this->param['CardQuantity'] = $p->CardQuantity;
															$Func = 'buyPrepaidCards';
															$check_cart = $this->mongo_db->where(array('_id' => new \MongoId($p->RefNumber),'transaction_card'=>'hold'))->get('cart');
															if(!empty($check_cart)){
																$this->obj = $this->apps->_Service_Alego_ByCard_Sendding($this->param,$Func);
																if(!empty($this->obj)){
																	$this->r = $this->apps->_msg_response(1000);
																	$this->r['result'] = $this->obj;
																	$this->r['trace'] = $p->RefNumber;
																}else{ 
																	$this->r = $this->apps->_msg_response(4105);
																	$this->r['result'] = $this->obj;
																	$this->r['trace'] = $p->RefNumber;
																}
															}else{ 
																$this->r = $this->apps->_msg_response(4106);
																$this->r['result'] = $this->obj;
																$this->r['trace'] = $p->RefNumber;
															}
														}else{ $this->r = $this->apps->_msg_response(4104);}
													}else{ $this->r = $this->apps->_msg_response(4104);}
												}else{ $this->r = $this->apps->_msg_response(4103);}
											}else{ $this->r = $this->apps->_msg_response(4102);}
										}else{ $this->r = $this->apps->_msg_response(4101);}
									}else{ $this->r = $this->apps->_msg_response(4100);}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	
	
	
	public function banthe247_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3 || (int)$this->_level == 4){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3 || (int)$this->_level == 4){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->card_seri)){
										if(!empty($p->card_code)){
											if(!empty($p->card_type)){
												if(!empty($p->card_amount)){
													$this->reseller = $this->apps->_token_reseller($p->token);
													$this->param['card_seri'] = $p->card_seri;
													$this->param['card_code'] = $p->card_code;
													$this->param['card_type'] = $p->card_type;
													$this->param['card_amount'] = $p->card_amount;
													$this->param['reseller'] = $this->reseller;
													if(!empty($p->client_id)){ $client_id = $p->client_id;}else{ $client_id = $this->reseller;}
													if(!empty($p->publisher)){ $publisher = $p->publisher;}else{ $publisher = null; }
													$this->param['publisher'] = $publisher;
													$this->param['client_id'] = $client_id; 
													$this->param['time_tracking'] = time(); 
													$this->param['note'] = handesk_encode(json_encode($this->param));
													$api_url = 'https://banthe24h.vn/MechantServices.asmx';
													$wsdl = $api_url."?wsdl";
													$client = new nusoap_client($wsdl, 'wsdl');
													$api_username = 'handeskdotvn@gmail';
													$api_password = '112233fF';
													$client->setCredentials($api_username,$api_password);
													$this->objd = $client->getError();
													$service = 'BuyCards';
													$params = array(
														'trace' => '123123123',
														'telco' => 'VTT',
														'amount' => 20000,
														'quantity' => 1,
													);
													$this->objd = $client->call($service, $params);
													$this->r['result'] = json_encode($this->objd);
												}else{ $this->r = $this->apps->_msg_response(4014);}
											}else{ $this->r = $this->apps->_msg_response(4013);}
										}else{ $this->r = $this->apps->_msg_response(4016);}
									}else{ $this->r = $this->apps->_msg_response(4015);}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
}
// $wsdl = 'https://banthe24h.vn/MechantServices.asmx?WSDL';
// $ns = 'http://tempuri.org';
// $client = new SoapClient($wsdl, array(
// "trace" => 1,
// "exceptions" => 0
// ));
// $login = 'handeskdotvn@gmail.com';
// $password = '123123fF';
// $headerBody = array(
// 'userName' => $login,
// 'pass' => $password
// );
// $header = new SoapHeader($ns, 'UserCredentials', $headerBody);
// $n_params = array('trace' => '1231231231231', 'telco' => 'VTT','amount'=> 20000,'quantity'=>1);

// $client->__setSoapHeaders($header);
// $client->__soapCall("APIResult", $n_params);
// $this->obj = $client->BuyCards($n_params);

?>
		