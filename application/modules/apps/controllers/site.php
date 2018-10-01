<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Core.php';
class Site extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>100,'result'=>null);
		
		$this->obj = array();
		$this->apps = new core;
		$this->_api_key = $this->_api_key();
		$this->encryption_key = $this->config->item('encryption_key');
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->_is_private_key = $this->apps->_is_private_key($this->_api_key());	
		$this->result = array();
		$this->objects = array();
		$this->param = array();
		$this->realtime = array();
	}
	public function realtime_task_post(){
		$this->realtime['withdraw'] = $this->mongo_db->where(array('transaction'=>'hold'))->get('withdrawal');
		// $this->realtime['transfer'] = $this->mongo_db->where(array('transaction'=>'hold'))->get('transfer_log');
		// $this->realtime['card'] = $this->mongo_db->where(array('transaction_card'=>'hold'))->get('log_card_change');
		// $this->realtime['cart'] = $this->mongo_db->where(array('transaction_card'=>'hold'))->get('cart');
		$this->realtime['api_keys'] = $this->mongo_db->where(array('level'=>1))->get('api_keys');
		$this->r = array('status'=>true,'result'=>$this->realtime);
		$this->response($this->r);
	}
	
	public function system_info_get(){
		$this->obj['user'] = $this->mongo_db->count("ask_users");
		$this->obj['card_change'] = $this->mongo_db->count("log_card_change");
		$this->obj['withdrawal'] = $this->mongo_db->count("withdrawal");
		$this->obj['transfer_log'] = $this->mongo_db->count("transfer_log");
		$this->obj['history_payments'] = $this->mongo_db->count("history_payments");
		////////////////////
		$command_withdrawal = array('$group' => array('_id' => '$transaction','count' => array( '$sum' => 1,),),);
		$this->obj['withdrawal_group'] = $this->mongo_db->aggregate('withdrawal',$command_withdrawal);
		////////////////////
		$command_transfer_log = array('$group' => array('_id' => '$transaction','count' => array( '$sum' => 1,),),);
		$this->obj['transfer_log_group'] = $this->mongo_db->aggregate('transfer_log',$command_transfer_log);
		////////////////////
		$transfer_transaction = array('$group' => array('_id' => '$transaction','count' => array( '$sum' => '$total_transfer',),),);
		$this->obj['transfer_transaction'] = $this->mongo_db->aggregate('transfer_log',$transfer_transaction);
		////////////////////
		$withdrawal_transaction = array('$group' => array('_id' => '$transaction','count' => array( '$sum' => '$total_transfer',),),);
		$this->obj['withdrawal_transaction'] = $this->mongo_db->aggregate('withdrawal',$withdrawal_transaction);
		////////////////////	
		$history_payments_transaction = array('$group' => array('_id' => '$transaction','count' => array( '$sum' => '$total_amount',),),);
		$this->obj['history_payments_transaction'] = $this->mongo_db->aggregate('history_payments',$history_payments_transaction);
		////////////////////	
		$card_transaction = array('$group' => array('_id' => '$transaction_card','count' => array( '$sum' => '$total_transfer',),),);
		$this->obj['card_transaction'] = $this->mongo_db->aggregate('log_card_change',$card_transaction);
		////////////////////
		$command_transaction = array('$group' => array('_id' => '$transaction_card','count' => array( '$sum' => 1,),),);
		$this->obj['card_change_hod'] = $this->mongo_db->aggregate('log_card_change',$command_transaction);
		////////////////////
		$command_api = array('$group' => array('_id' => '$level','count' => array( '$sum' => 1,),),);
		$this->obj['api_group'] = $this->mongo_db->aggregate('api_keys',$command_api);
		////////////////////
		$this->response($this->obj);
	}
	
	public function SlugBlogs_get(){
		$p = $this->apps->_params($_GET['param'],$this->_api_key);
		try{
			if(!empty($p->alias)){
					$this->obj = $this->mongo_db->select(array('alias',))->where(array('alias' => $p->alias))->get("news");
					$this->r['result'] = $this->obj;
			}
		}catch (Exception $e) { }
		$this->response($this->r);
	}	
	public function alias_post(){
		$p = $this->apps->_params($_POST['param'],$this->_api_key);
		if(!empty($p->alias)){
			$this->obj = $this->mongo_db->where(array('alias'=>$p->alias))->get("news");
			if(!empty($this->obj)){
				$c = $this->obj[0]['categories'];
				$this->param = $this->mongo_db->where(array('categories'=>$c))->where_ne('alias',$p->alias)->order_by(array('time_create' => 'DESC'))->limit(7)->get("news");
			}
			$this->r['related'] = $this->param;
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function site_notifacation_top_post(){
		$this->obj = $this->mongo_db->where(array('categories'=>'faq'))->order_by(array('time_create' => 'DESC'))->limit(3)->get("news");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function transaction_payment_init_post(){
		if($this->_level ==2 || $this->_level ==3){
			if($this->_role ==1 || $this->_role ==2){
				$p = $this->apps->_params($_POST['param'],$this->_api_key);
				if(!empty($p->order_code)){ $this->param['order_code'] = $p->order_code; }
				if(!empty($p->id_clients)){ $this->param['id_clients'] = $p->id_clients; }
				if(!empty($p->payment_method)){ $this->param['payment_method'] = $p->payment_method; }
				if(!empty($p->buyer_fullname)){ $this->param['buyer_fullname'] = $p->buyer_fullname; }
				if(!empty($p->buyer_email)){ $this->param['buyer_email'] = $p->buyer_email; }
				if(!empty($p->buyer_mobile)){ $this->param['buyer_mobile'] = $p->buyer_mobile; }
				if(!empty($p->payment_type)){ $this->param['payment_type'] = $p->payment_type; }
				if(!empty($p->order_description)){ $this->param['order_description'] = $p->order_description; }
				if(!empty($p->total_amount)){ $this->param['total_amount'] = $p->total_amount; }
				if(!empty($p->bank_code)){ $this->param['bank_code'] = $p->bank_code; }
				if(!empty($p->date_create)){ $this->param['date_create'] = $p->date_create; }
				if(!empty($p->time_crate)){ $this->param['time_create'] = $p->time_crate; }
				if(!empty($p->service_name)){ $this->param['service_name'] = $p->service_name; }
				if(!empty($p->transaction)){ $this->param['transaction'] = $p->transaction; }
				if(!empty($p->status)){ $this->param['status'] = $p->status; }
				if(!empty($p->token_service)){ $this->param['token_service'] = $p->token_service; }
				if(!empty($p->error_code)){ $this->param['error_code'] = $p->error_code; }
				if(!empty($p->checkout_url)){ $this->param['checkout_url'] = $p->checkout_url; }
				$this->obj = $this->mongo_db->where(array('order_code'=>$p->order_code))->get('history_payments');
				if(empty($this->obj)){
					$obj = $this->mongo_db->insert('history_payments',$this->param);
					if($obj==true){
						$this->r['status'] = 1000;
						$this->r['result'] = getObjectId($obj);
					}
				}
			}
		}
		$this->response($this->r);
	}
	
	////////////////////////////////////
	public function info_client_payment_post(){
		if($this->_level ==2 || $this->_level ==3){
			if($this->_role ==1 || $this->_role ==2){
				$p = $this->apps->_params($_POST['param'],$this->_api_key);
				$this->result = $this->mongo_db->select(array('balancer'))->where(array('_id' => new \MongoId($p->client_id),))->get('ask_users');
				if(!empty($this->result)){
					if(!empty($this->result[0]['balancer'])){
						$this->objects = (int)$this->result[0]['balancer'];
					}
					$this->r = array('status'=>true,'result'=>$this->objects);
				}
			}
		}
		$this->response($this->r);
	}
	public function payment_buycard_orders_post(){
		if($this->_level ==2 || $this->_level ==3){
			if($this->_role ==1 || $this->_role ==2){
				$p = $this->apps->_params($_POST['param'],$this->_api_key);
				$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),))->get('ask_users');
				if(!empty($this->result)){
					$this->reseller = $this->apps->_token_reseller($p->token);
					////check lại thông tin user về tài khoản///
					$balancer_client = (int)$this->result[0]['balancer'];
					$money_transfer = (int)$p->MoneyTransfer;
					if($balancer_client > $money_transfer){
						$balancer_munis = $balancer_client - $money_transfer;
						if($balancer_munis > 1000){
							// $this->obj = $p;
							$this->objects['Telco'] = $p->Telco;
							$this->objects['CardQuantity'] = $p->CardQuantity;
								if(isset($p->CustMobile)){
								$this->objects['CustMobile'] = $p->CustMobile;
							}
							$this->objects['CardPrice'] = $p->CardPrice;
							$this->objects['Type'] = $p->Type;
							$this->objects['OrderID'] = $p->OrderID;
							$this->objects['PriceDiscount'] = $p->PriceDiscount;
							$this->objects['deduct'] = $p->deduct;
							$this->objects['rose'] = $p->rose;
							$this->objects['ProductCode'] = $p->ProductCode;
							$this->objects['CardName'] = $p->CardName;
							$this->objects['CardTitle'] = $p->CardTitle;
							$this->objects['PriceRose'] = $p->PriceRose;
							$this->objects['MoneyTransfer'] = $p->MoneyTransfer;
							$this->objects['TotalOder'] = $p->TotalOder;
							$this->objects['client_id'] = $p->client_id;
							$this->objects['full_name'] = $p->full_name;
							$this->objects['email'] = $p->email;
							$this->objects['phone'] = $p->phone;
							$this->objects['time_create'] = time();
							$this->objects['date_create'] = date("Y-m-d H:i:s",time());
							$this->objects['transaction_card'] = "hold";
							$check_OrderCode = $this->mongo_db->where(array('OrderID' => $p->OrderID,))->get('cart');
							if(empty($check_OrderCode)){
								$traking_orders = $this->mongo_db->insert('cart',$this->objects);
								if($traking_orders==true){
									$this->param = array(
										"Tracking_Orders" => getObjectId($traking_orders),
										"money_transfer" => (int)$p->MoneyTransfer,
										"date_create" => date("Y-m-d H:i:s",time()),
										"time_create" => time(),
										"fee" => 0,
										"total_transfer" => (int)$p->MoneyTransfer,
										"balancer_clients" => $balancer_client,
										"beneficiary_balancer" => 0,
										"balancer_plus" => 0,
										"balancer_munis" => (int)$balancer_munis,
										"payer_balancer" => 0,
										"payer_id" => 0,
										"beneficiary_id" => $this->reseller,
										"beneficiary" => "Buy Card",
										"payer_name" => "Buy Card",
										"client_id" => $p->client_id,
										"client_name" => $p->full_name,
										"password_transfer" => md5($p->client_id),
										"reseller" => $this->reseller,
										"transaction" => "done",
										"bank_id" => $p->client_id,
										"auth" => md5($p->client_id),
										"bank_name" => "Buy Card",
										"account_holders" => $p->full_name,
										"bank_account" => md5($p->client_id),
										"provinces_bank" => "Buy Card",
										"branch_bank" => "Buy Card",
										"type" => "buy",
									);
									$v1 = $this->apps->_transfer_minus($balancer_munis,$p->client_id,$this->param);
									if($v1==true){
										$this->r = array('status'=>1000,'result'=>getObjectId($traking_orders),'msg'=>$this->apps->_msg_response(1000));
									}else{ $this->r = array('status'=>2025,'result'=>null,'msg'=>$this->apps->_msg_response(2027)); }
								}else{ $this->r = array('status'=>2025,'result'=>null,'msg'=>$this->apps->_msg_response(2026)); }
							}else{ $this->r = array('status'=>2025,'result'=>null,'msg'=>$this->apps->_msg_response(2025)); }
						}else{ $this->r = array('status'=>2025,'result'=>null,'msg'=>$this->apps->_msg_response(2021)); }
					}else{ $this->r = array('status'=>2025,'result'=>null,'msg'=>$this->apps->_msg_response(2020)); }
				}else{ $this->r = array('status'=>2025,'result'=>null,'msg'=>$this->apps->_msg_response(1002)); }
			}else{ $this->r = array('status'=>2025,'result'=>null,'msg'=>$this->apps->_msg_response(1001)); }
		}
		$this->response($this->r);
	}
	////////////////////////////////////
	public function info_client_payment_discount_post(){
		if($this->_level ==2 || $this->_level ==3){
			if($this->_role ==1 || $this->_role ==2){
				$p = $this->apps->_params($_POST['param'],$this->_api_key);
				$this->result = $this->mongo_db->where(array('telco'=>$p->Telco,'status'=>'active'))->get('Telco');
				if(!empty($this->result)){
					if(!empty($this->result[0])){
						$this->objects = $this->result[0];
					}
					$this->r = array('status'=>true,'result'=>$this->objects);
				}
			}
		}
		$this->response($this->r);
	}
	//////////////////////////////
	public function cancel_payments_post(){
		if($this->_level ==2 || $this->_level ==3){
			if($this->_role ==1 || $this->_role ==2){
				$p = $this->apps->_params($_POST['param'],$this->_api_key);
				$this->obj = $this->mongo_db->where(array('order_code'=>$p->order_code,'id_clients'=>$p->id_clients))->get("history_payments");
				if(!empty($this->obj)){
					$_id = getObjectId($this->obj[0]['_id']);
					$this->result = $this->mongo_db->where(array('_id' => new \MongoId($_id),))->set(array('transaction'=>'reject'))->update('history_payments');
					if($this->result==true){
						$this->r['status'] = 1000;
						$this->r['result'] = $this->result;
					}
				}
			}
		}
		$this->response($this->r);
	}
	public function payment_confim_post(){
		if($this->_level ==2 || $this->_level ==3){
			if($this->_role ==1 || $this->_role ==2){
				$p = $this->apps->_params($_POST['param'],$this->_api_key);
				$cart = $this->mongo_db->where(array('order_code'=>$p->order_code,'id_clients'=>$p->client_id,'transaction'=>'hold',))->get("history_payments");
				if(!empty($cart)){
					if(!empty($cart[0]['id_clients'])){
						$client_id = (string)$cart[0]['id_clients'];
						$_card_id = getObjectId($cart[0]['_id']);
						///////////// CLIENT INFO ///////////////////////////////
						$reseller = $this->apps->_token_reseller($p->token);
						$client = $this->mongo_db->where(array('_id' => new \MongoId($client_id),))->get('ask_users');
						if(!empty($client)){
							$balancer_client = (int)$client[0]['balancer'];
							$money_transfer	= (int)$cart[0]['total_amount'];
							$buyer_fullname = (string)$cart[0]['buyer_fullname'];
							$fee	= 0;
							$total_transfer = (int)$money_transfer - (int)$fee;
							$balancer_plus = (int)$balancer_client + (int)$total_transfer;
							$this->param = array(
							"money_transfer" => $money_transfer,
							"date_create" => date("Y-m-d H:i:s",time()),
							"time_create" => time(),
							"fee" => 0, 
							"total_transfer"  => $total_transfer,
							"types" => "transfer",
							"balancer_clients" => $balancer_client,
							"beneficiary_balancer" => $balancer_client,
							"balancer_plus" => $balancer_plus,
							"balancer_munis" => 0,
							"payer_balancer" => 0,
							"payer_id" => $reseller,
							"payer_name" => "In System",
							"beneficiary_id" => $client_id,
							"beneficiary"=> $buyer_fullname,
							"client_id" => $client_id,
							"client_name" => $buyer_fullname,
							"Tracking" => $_card_id,
							"password_transfer" => md5($p->token_transfer),
							"reseller" => $reseller, 
							"type" => "refill",
							"transaction" => "done",
							"bank_name" => "Nap Tai Khoan",
							"account_holders" => $buyer_fullname,
							"bank_account" => $client_id,
							"provinces_bank" => "In system",
							"branch_bank" => "In system",
							
						);
						//////////////Ghi log ////////////
						$v1 = $this->mongo_db->where(array('_id' => new \MongoId($_card_id),))->set(array('transaction'=>'done','error_code'=>$p->error_code))->update('history_payments');
						if($v1==true){
							$this->result = $this->apps->_transfer_plus($balancer_plus,$client_id,$this->param);
							$this->r['result'] = $this->result;	
						}
						$this->r['status'] = true;	
						}
					}
				}
			}
		}
		$this->response($this->r);
	}
	public function payment_reject_post(){
		if($this->_level ==2 || $this->_level ==3){
			if($this->_role ==1 || $this->_role ==2){
				$p = $this->apps->_params($_POST['param'],$this->_api_key);
				$cart = $this->mongo_db->where(array('order_code'=>$p->order_code,'id_clients'=>$p->client_id,'transaction'=>'hold',))->get("history_payments");
				if(!empty($cart)){
					if(!empty($cart[0]['id_clients'])){
						$client_id = (string)$cart[0]['id_clients'];
						$_card_id = getObjectId($cart[0]['_id']);
						///////////// CLIENT INFO ///////////////////////////////
						$reseller = $this->apps->_token_reseller($p->token);
						$client = $this->mongo_db->where(array('_id' => new \MongoId($client_id),))->get('ask_users');
						if(!empty($client)){
						//////////////Ghi log ////////////
						$v1 = $this->mongo_db->where(array('_id' => new \MongoId($_card_id),'id_clients'=>$p->client_id,'transaction'=>'hold',))->set(array('transaction'=>'reject','error_code'=>$p->error_code))->update('history_payments');
						if($v1==true){
							$this->r['result'] = $this->result;	
						}
						$this->r['status'] = true;	
						}
					}
				}
			}
		}
		$this->response($this->r);
	}
	public function info_service_payments_post(){
		if($this->_level ==2 || $this->_level ==3){
			if($this->_role ==1 || $this->_role ==2){
					$p = $this->apps->_params($_POST['param'],$this->_api_key);
					$this->obj = $this->mongo_db->where(array('status'=>'active','name_service'=>$p->name_service))->get("payment_service");
				if(!empty($this->obj)){
					foreach($this->obj as $v){ 	}
						$this->param = array(
							'_id' => getObjectid($v['_id']),
							'name_service' => $v['name_service'],
							'url_api' => $v['url_api'],
							'receiver' => $v['receiver'],
							'merchant_id' => $v['merchant_id'],
							'merchant_pass' => $v['merchant_pass'],
							'status' => $v['status'],
							'title' => $v['title'],
						);
				
					$this->r['result'] = $this->param;
				}
			}
		}
		$this->response($this->r);
	}
	public function info_service_payments_get(){
		if($this->_level ==2 || $this->_level ==3){
			if($this->_role ==1 || $this->_role ==2){
				$this->obj = $this->mongo_db->where(array('status'=>'active'))->get("payment_service");
				
				if(!empty($this->obj)){
					foreach($this->obj as $v){
					
						$this->param[] = array(
							'_id' => getObjectid($v['_id']),
							'name_service' => $v['name_service'],
							'url_api' => $v['url_api'],
							'receiver' => $v['receiver'],
							'merchant_id' => $v['merchant_id'],
							'merchant_pass' => $v['merchant_pass'],
							'status' => $v['status'],
							'title' => $v['title'],
						);
					}
					$this->r['result'] = $this->param;
				}
				$this->response($this->r);
			}
		}
		
	}
	
	public function site_load_site_faq_post(){
		$this->obj = $this->mongo_db->where(array('categories'=>'faq'))->order_by(array('time_create' => 'DESC'))->limit(5)->get("news");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
		
	}
	public function new_categories_post(){
		$p = $this->apps->_params($_POST['param'],$this->_api_key);
		if(!empty($p->categories)){
			if(!empty($p->limit)){
				if((int)$p->limit <= 1){
					$limit = 1;
					$offset = 0;
				}else{
					$limit = (int)$p->limit + 1;
					$offset = (int)$p->limit * 10;
				}
			}else{
				$limit = 0;
				$offset = 0;
			}
			
			$this->obj = $this->mongo_db->where(array('categories'=>$p->categories))->order_by(array('time_create' => 'DESC'))->offset($offset)->limit(10)->get("news");
			$count = $this->mongo_db->where(array('categories'=>$p->categories))->count("news");
			if($count >= 10){
				$this->r['next'] = $limit;
			}else{
				$this->r['next'] = 0;
			}
			if(!empty($this->obj)){
				$this->r['result'] = $this->obj;
			}
		}
		
		$this->response($this->r);
		
	}
	public function site_load_card_post(){
		$this->obj = $this->mongo_db->order_by(array('card_type' => 'ASC'))->get("card");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function load_bycard_post(){
		$this->obj = $this->mongo_db->where(array('status'=>'active','type'=>"TELCO_PINCODE",'types'=>"DT"))->order_by(array('ProductCode' => 'ASC'))->get("Telco");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function load_bycardGame_post(){
		$this->obj = $this->mongo_db->where(array('status'=>'active','types'=>"GAME"))->order_by(array('ProductCode' => 'ASC'))->get("Telco");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function load_bycard_telco_info_post(){
		$p = $this->apps->_params($_POST['param'],$this->_api_key);
		$this->obj = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'status'=>'active'))->get("Telco");
		if(!empty($this->obj[0])){
			$this->r['result'] = $this->obj[0];
		}
		$this->response($this->r);
	}
	public function load_bycard_topup_post(){
		$this->obj = $this->mongo_db->where(array('status'=>'active','types'=>"DT"))->where_ne('type',"TELCO_PINCODE")->order_by(array('ProductCode' => 'ASC'))->get("Telco");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function site_load_bycard_post(){
		$this->obj = $this->mongo_db->where(array('status'=>'active','types'=>"DT"))->order_by(array('type' => 'ASC',))->get("Telco");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function site_load_bycardmage_post(){
		$this->obj = $this->mongo_db->where(array('status'=>'active','types'=>"GAME"))->order_by(array('type' => 'ASC',))->get("Telco");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}
	public function site_notification_post(){
		$this->obj = $this->mongo_db->select(array('alias','title','description','date_create'))->where(array('categories'=>'notification'))->order_by(array('time_create' => 'DESC'))->limit(1)->get("news");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj[0];
		}
		$this->response($this->r);
		
	}
	public function news_box_post(){
		$this->obj = $this->mongo_db->where_ne('categories','faq')->order_by(array('time_create' => 'DESC'))->limit(5)->get("news");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
		
	}
	
	public function BlogsAdd_post(){
		$p = $this->apps->_params($_POST['param'],$this->_api_key);
		try{
			
			if(!empty($p->alias)){ $this->param['alias'] = slugify(url_encoded($p->alias)); }else{ $this->param['alias'] = time(); }
			if(!empty($p->categories)){ $this->param['categories'] = $p->categories; }else{ $this->param['categories'] = time(); }
			if(!empty($p->title)){ $this->param['title'] = $p->title; }else{ $this->param['title'] = time(); }
			if(!empty($p->images)){ $this->param['images'] = $p->images; }else{ $this->param['images'] = time(); }
			if(!empty($p->keywords)){ $this->param['keywords'] = $p->keywords; }else{ $this->param['keywords'] = time(); }
			if(!empty($p->description)){ $this->param['description'] = $p->description; }else{ $this->param['description'] = time(); }
			if(!empty($p->description_seo)){ $this->param['description_seo'] = $p->description_seo; }else{ $this->param['description_seo'] = time(); }
			if(!empty($p->title_seo)){ $this->param['title_seo'] = $p->title_seo; }else{ $this->param['title_seo'] = time(); }
			if(!empty($p->contents)){ $this->param['contents'] = $p->contents; }
		
			$this->param['time_create'] = time();
			$this->param['date_create'] = date("Y-m-d H:i:s",time());
			$check_alias =  $this->mongo_db->where(array('alias'=>slugify(url_encoded($p->alias))))->get('news');
			if(empty($check_alias)){
				$this->obj = $this->mongo_db->insert('news',$this->param);
				$this->r['result'] = true;
			}else{
				$this->r['result'] = false;
			}
		}catch (Exception $e) { }
		$this->response($this->r);
	}
	public function blogs_update_post(){
		$p = $this->apps->_params($_POST['param'],$this->_api_key);
		try{
			if(!empty($p->alias)){ $this->param['alias'] = $p->alias; }
			if(!empty($p->categories)){ $this->param['categories'] = $p->categories;  }
			if(!empty($p->title)){ $this->param['title'] = $p->title; }
			if(!empty($p->images)){ $this->param['images'] = $p->images; }
			if(!empty($p->keywords)){ $this->param['keywords'] = $p->keywords; }
			if(!empty($p->description)){ $this->param['description'] = $p->description; }
			if(!empty($p->description_seo)){ $this->param['description_seo'] = $p->description_seo; }
			if(!empty($p->title_seo)){ $this->param['title_seo'] = $p->title_seo; }
			if(!empty($p->contents)){ $this->param['contents'] = (string)$p->contents; }
			$this->param['time_create'] = time();
			$this->param['date_create'] = date("Y-m-d H:i:s",time());
			if(!empty($p->keys)){
				$this->obj = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),))->set($this->param)->update('news');
				$this->r['result'] = true;
			}else{
				$this->r['result'] = false;
			}
		}catch (Exception $e) { }
		$this->response($this->r);
	}
	public function info_card_get(){
	
		$this->obj = $this->mongo_db->where(array('status'=>'active'))->get("card");
		if(!empty($this->obj)){
			$this->r['result'] = $this->obj;
		}
		$this->response($this->r);
	}	
	public function publisher_post(){
		$p = $this->apps->_params($_POST['param'],$this->_api_key);
		try{
			$publisher = $this->mongo_db->where(array('_id' => new \MongoId($p->publisher),))->get('ask_users');
			if(!empty($publisher)){
				$clients_publisher = $this->mongo_db->where(array('client_id'=>$p->client_id,))->get('publisher');
				if(empty($clients_publisher)){
						$clients_pp = $this->mongo_db->where(array('partner'=>$p->publisher,'client_id'=>$p->client_id,))->get('publisher');
						if(empty($clients_pp)){
							$this->param = array(
								'partner' => $p->publisher,
								'client_id' => $p->client_id,
								'full_name' => $p->profile->full_name,
								'username' => $p->profile->username,
								'email' => $p->profile->email,
								'phone' => $p->profile->phone,
								'reseller' => $p->profile->reseller,
								'balancer' => 0,
								'levels' => 1,
								'date_create' => date("Y-m-d H:i:s",time()),
								'time_create' => time(),
								'ip' => $this->input->ip_address(),
							);
							$install = $this->mongo_db->insert('publisher',$this->param);
							if($install==true){
								$object = array(
									'partner'=>$p->publisher,
									'publisher'=> getObjectId($install),
								);
								$this->mongo_db->where(array('_id' => new \MongoId($p->client_id),))->set($object)->update('ask_users');
								$this->r = array('status'=>1000,'result'=> getObjectId($install));
							}
						}
				}
			}
		}catch (Exception $e) { }
		$this->response($this->r);
	}
	public function min_transfer_get(){
	
		$this->obj = $this->mongo_db->select('min_withdraw')->get("config");
		if(!empty($this->obj)){
			$this->r['result'] = (int)$this->obj[0]['min_withdraw'];
		}
		$this->response($this->r);
	}
	public function info_clients_get(){
		$p = $this->apps->_params($_GET['param'],$this->_api_key);
		
		try{
			$this->obj = $this->mongo_db->select(array('username','full_name','reseller'))->where(array('_id' => new \MongoId($p->keys)))->get("ask_users");
			$this->r['result'] = $this->obj[0];
		}catch (Exception $e) { }
		$this->response($this->r);
	}
	public function info_publisher_get(){
		$p = $this->apps->_params($_GET['param'],$this->_api_key);
		try{
			$this->obj = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'partner'=>$p->partner))->get("publisher");
			$this->r['result'] = $this->obj[0];
		}catch (Exception $e) { }
		$this->response($this->r);
	}
		public function info_publisher_buy_get(){
		$p = $this->apps->_params($_GET['param'],$this->_api_key);
		try{
			$this->obj = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->get("publisher");
			$this->r['result'] = $this->obj[0];
		}catch (Exception $e) { }
		$this->response($this->r);
	}
	public function info_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->obj = $this->mongo_db->get("site");
					if(!empty($this->obj)){ $this->r = array('status'=>1000, "result"=> encrypt_obj(json_encode($this->obj),$this->_api_key,$this->_is_private_key)); }
				}
			}
		}
		$this->response($this->r);
	}
	public function info_rose_partner_post(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->r = array(
						'status' => true,
						'rose_partner' => $this->apps->_rose_partner(),
					);
				}
			}
		}
		$this->response($this->r);
	}
	public function info_transfer_wget_post(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->r = array(
						'status' => true,
						'transfer_fee' => $this->apps->_transfer_fee(),
					);
				}
			}
		}
		$this->response($this->r);
	}
	public function info_client_fogot_post(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$p = $this->apps->_params($_POST['param'],$this->_api_key);
					$this->objects = $this->mongo_db->where(array('email'=>$p->email))->get('ask_users');
					if(!empty($this->objects[0])){
						$this->r = array(
							'status' => true,
							'result' => $this->objects[0],
						);
					}
				}
			}
		}
		$this->response($this->r);
	}
	public function info_withdraw_wget_post(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->r = array(
						'status' => true,
						'withdrawal_fee' => $this->apps->_withdrawal_fee(),
					);
				}
			}
		}
		$this->response($this->r);
	}
	public function config_update_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					if(!empty($_GET['param'])){
						$p = $this->apps->_params($_GET['param'],$this->_api_key);
						if(!empty($p->transfer)){ $this->obj['transfer'] = (int)$p->transfer; }
						if(!empty($p->withdraw)){ $this->obj['withdraw'] = (int)$p->withdraw; }
						if(!empty($p->min_withdraw)){ $this->obj['min_withdraw'] = (int)$p->min_withdraw; }
						if(!empty($p->rose_reseller)){ $this->obj['rose_reseller'] = (int)$p->rose_reseller; }
						if(!empty($p->rose_client)){ $this->obj['rose_client'] = (int)$p->rose_client; }
						if(!empty($p->rose_partner)){ $this->obj['rose_partner'] = (int)$p->rose_partner; }
						if(!empty($p->keys)){
							$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),))->set($this->obj)->update('config');
							$this->r = array('status'=>1000, "result"=> $this->result);
						}
					}
				}
			}
		}
		$this->response($this->r);
	}		
	
	public function generic_update_post(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					if(!empty($_POST['param'])){
						$p = $this->apps->_params($_POST['param'],$this->_api_key);
						if(!empty($p->brand)){ $this->obj['brand'] = (string)$p->brand; }
						if(!empty($p->company)){ $this->obj['company'] = (string)$p->company; }
						if(!empty($p->phone)){ $this->obj['phone'] = (string)$p->phone; }
						if(!empty($p->hotline)){ $this->obj['hotline'] = (string)$p->hotline; }
						if(!empty($p->skype)){ $this->obj['skype'] = (string)$p->skype; }
						if(!empty($p->email_brand)){ $this->obj['email_brand'] = (string)$p->email_brand; }
						if(!empty($p->smtp_host)){ $this->obj['smtp_host'] = (string)$p->smtp_host; }
						if(!empty($p->smtp_port)){ $this->obj['smtp_port'] = (string)$p->smtp_port; }
						if(!empty($p->smtp_crypto)){ $this->obj['smtp_crypto'] = (string)$p->smtp_crypto; }
						if(!empty($p->smtp_user)){ $this->obj['smtp_user'] = (string)$p->smtp_user; }
						if(!empty($p->smtp_password)){ $this->obj['smtp_password'] = (string)$p->smtp_password; }
						if(!empty($p->address_brand)){ $this->obj['address_brand'] = (string)$p->address_brand; }
						if(!empty($p->brands_vat)){ $this->obj['brands_vat'] = (string)$p->brands_vat; }
						if(!empty($p->facebook_page_name)){ $this->obj['facebook_page_name'] = (string)$p->facebook_page_name; }
						if(!empty($p->google_plus_url)){ $this->obj['google_plus_url'] = (string)$p->google_plus_url; }
						if(!empty($p->google_recaptcha)){ $this->obj['google_recaptcha'] = (string)$p->google_recaptcha; }
						if(!empty($p->abouts_url)){ $this->obj['abouts_url'] = (string)$p->abouts_url; }
						if(!empty($p->twitter)){ $this->obj['twitter'] = (string)$p->twitter; }
						if(!empty($p->support_url)){ $this->obj['support_url'] = (string)$p->support_url; }
						if(!empty($p->privacy_url)){ $this->obj['privacy_url'] = (string)$p->privacy_url; }
						if(!empty($p->order_guide)){ $this->obj['order_guide'] = (string)$p->order_guide; }
						if(!empty($p->delivery_url)){ $this->obj['delivery_url'] = (string)$p->delivery_url; }
						if(!empty($p->developer_url)){ $this->obj['developer_url'] = (string)$p->developer_url; }
						if(!empty($p->contact_url)){ $this->obj['contact_url'] = (string)$p->contact_url; }
						if(!empty($p->infomation_url)){ $this->obj['infomation_url'] = (string)$p->infomation_url; }
						if(!empty($p->reseller_id)){ $this->obj['reseller_id'] = (string)$p->reseller_id; }
						if(!empty($p->website)){ $this->obj['website'] = (string)$p->website; }
						if(!empty($p->url_read)){ $this->obj['url_read'] = (string)$p->url_read; }
						if(!empty($p->logo)){ $this->obj['logo'] = (string)$p->logo; }
						if(!empty($p->img_details)){ $this->obj['img_details'] = (string)$p->img_details; }
						if(!empty($p->title)){ $this->obj['title'] = (string)$p->title; }
						if(!empty($p->title_main)){ $this->obj['title_main'] = (string)$p->title_main; }
						if(!empty($p->keywords)){ $this->obj['keywords'] = (string)$p->keywords; }
						if(!empty($p->description)){ $this->obj['description'] = (string)$p->description; }
						if(!empty($p->keys)){
							$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),))->set($this->obj)->update('site');
							$this->r = array('status'=>1000, "result"=> $this->result);
						}
					}
				}
			}
		}
		$this->response($this->r);
	}
		public function cardchage_cms_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 ){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								if($this->_role == 1 || (int)$this->_role == 2){ $k = $this->mongo_db->get('card');}
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['rose'])){ $rose = $v['rose'];}else{$rose = null;}
										if(!empty($v['deduct'])){ $deduct = $v['deduct'];}else{$deduct = null;}
										if(!empty($v['deduct_vip'])){ $deduct_vip = $v['deduct_vip'];}else{$deduct_vip = null;}
										if(!empty($v['status'])){ $status = $v['status'];}else{$status = null;}
										if(!empty($v['card_type'])){ $card_type = $v['card_type'];}else{$card_type = null;}
										if(!empty($v['name'])){ $name = $v['name'];}else{$name = null;}
										if(!empty($v['type_id'])){ $type_id = $v['type_id'];}else{$type_id = null;}
										if(!empty($v['title'])){ $title = $v['title'];}else{$title = null;}
										if(!empty($v['status'])){ $status = $v['status'];}else{$status = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'rose' => $rose,
											'deduct' => $deduct,
											'deduct_vip' => $deduct_vip,
											'card_type' => $card_type,
											'name' => $name,
											'type_id' => $type_id,
											'title' => $title,
											'status' => $status,
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
	public function card_buy_cms_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 ){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								if($this->_role == 1 || (int)$this->_role == 2){ $k = $this->mongo_db->get('Telco');}
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['rose'])){ $rose = $v['rose'];}else{$rose = null;}
										if(!empty($v['deduct'])){ $deduct = $v['deduct'];}else{$deduct = null;}
										if(!empty($v['status'])){ $status = $v['status'];}else{$status = null;}
										if(!empty($v['name'])){ $name = $v['name'];}else{$name = null;}
										if(!empty($v['ProductCode'])){ $ProductCode = $v['ProductCode'];}else{$ProductCode = null;}
										if(!empty($v['telco'])){ $telco = $v['telco'];}else{$telco = null;}
										if(!empty($v['title'])){ $title = $v['title'];}else{$title = null;}
										if(!empty($v['type'])){ $type = $v['type'];}else{$type = null;}
										if(!empty($v['types'])){ $types = $v['types'];}else{$types = null;}
										if(!empty($v['status'])){ $status = $v['status'];}else{$status = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'rose' => $rose,
											'deduct' => $deduct,
											'name' => $name,
											'telco' => $telco,
											'ProductCode' => $ProductCode,
											'title' => $title,
											'status' => $status,
											'type' => $type,
											'types' => $types,
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
	public function cardchage_cms_del_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'status'=>'disable'))->delete('card');
									if($del==true){
										$this->r = $this->apps->_msg_response(1000);
									}else{
										$this->r = $this->apps->_msg_response(2000);
									}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}	
	public function card_buy_cms_del_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->delete('Telco');
									if($del==true){
										$this->r = $this->apps->_msg_response(1000);
									}else{
										$this->r = $this->apps->_msg_response(2000);
									}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}		
	
	public function blockseri_cms_del_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									$card_seri = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->get('block_seri_card');
									if(!empty($card_seri)){
										foreach($card_seri as $vs){ }
										$seri = $vs['card_seri'];
										$card_type = $vs['card_type'];
										$loadSeri = $this->mongo_db->where(array('card_seri' => $seri,'card_type' => $card_type,'transaction_card'=>'reject'))->get('log_card_change');
										foreach($loadSeri as $value){
											$id = getObjectId($value['_id']);
											$del = $this->mongo_db->where(array('_id' => new \MongoId($id)))->delete('log_card_change');
										}
										$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->delete('block_seri_card');
										$this->r = $this->apps->_msg_response(1000);
									}else{ $this->r = $this->apps->_msg_response(2000);}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}	
	public function card_buy_config_add_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->rose)){ $this->param['rose'] = (float)$p->rose; }else{ $this->param['rose'] = 0;}
									if(!empty($p->deduct)){ $this->param['deduct'] = (float)$p->deduct; }else{ $this->param['deduct'] = 0;}
									if(!empty($p->telco)){ $this->param['telco'] = $p->telco; }else{ $this->param['telco'] = null;}
									if(!empty($p->type)){ $this->param['type'] = $p->type; }else{ $this->param['type'] = null;}
									if(!empty($p->types)){ $this->param['types'] = $p->types; }else{ $this->param['types'] = null;}
									if(!empty($p->name)){ $this->param['name'] = $p->name; }else{ $this->param['name'] = null;}
									if(!empty($p->ProductCode)){ $this->param['ProductCode'] = (int)$p->ProductCode; }else{ $this->param['ProductCode'] = null;}
									if(!empty($p->title)){ $this->param['title'] = $p->title; }else{ $this->param['title'] = '';}
									if(!empty($p->status)){ $this->param['status'] = $p->status; }else{ $this->param['status'] = 'disable';}
									$add = $this->mongo_db->insert('Telco',$this->param);
									if($add==true){
										$this->r = $this->apps->_msg_response(1000);
									}else{
										$this->r = $this->apps->_msg_response(2000);
									}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}	
	public function card_change_add_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->rose)){ $this->param['rose'] = (float)$p->rose; }else{ $this->param['rose'] = 0;}
									if(!empty($p->deduct)){ $this->param['deduct'] = (float)$p->deduct; }else{ $this->param['deduct'] = 0;}
									if(!empty($p->deduct_vip)){ $this->param['deduct_vip'] = (float)$p->deduct_vip; }else{ $this->param['deduct_vip'] = 0;}
									if(!empty($p->card_type)){ $this->param['card_type'] = (int)$p->card_type; }else{ $this->param['card_type'] = 100;}
									if(!empty($p->name)){ $this->param['name'] = $p->name; }else{ $this->param['name'] = 100;}
									if(!empty($p->type_id)){ $this->param['type_id'] = $p->type_id; }else{ $this->param['type_id'] = "5b916fc9f40e2ef99b80878c";}
									if(!empty($p->title)){ $this->param['title'] = $p->title; }else{ $this->param['title'] = '';}
									if(!empty($p->status)){ $this->param['status'] = $p->status; }else{ $this->param['status'] = 'disable';}
									$add = $this->mongo_db->insert('card',$this->param);
									if($add==true){
										$this->r = $this->apps->_msg_response(1000);
									}else{
										$this->r = $this->apps->_msg_response(2000);
									}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function card_change_edit_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->keys)){ 
										$this->param['keys'] = $p->keys; 
										$update_value = array();
										if(!empty($p->status)){
											$update_value['status'] = $p->status;
											if(!empty($p->deduct)){  $update_value['deduct'] = (float)$p->deduct;}		
											if(!empty($p->deduct)){ $update_value['deduct_vip'] = (float)$p->deduct_vip;}
											$update = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->set($update_value)->update('card');
											if($update==true){
												$this->r = $this->apps->_msg_response(1000);
											}else{$this->r = $this->apps->_msg_response(2000);}
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
	public function card_buy_cms_edit_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									if(!empty($p->keys)){ 
										$this->param['keys'] = $p->keys; 
										$update_value = array();
										if(!empty($p->status)){
											$update_value['status'] = $p->status;
											if(!empty($p->deduct)){ 
											$update_value['deduct'] = (float)$p->deduct;
											}
											$update = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->set($update_value)->update('Telco');
											if($update==true){
												$this->r = $this->apps->_msg_response(1000);
											}else{$this->r = $this->apps->_msg_response(2000);}
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
	public function config_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->obj = $this->mongo_db->get("config");
					if(!empty($this->obj)){
						$this->result = $this->obj[0];
					}
					if(!empty($this->obj)){ $this->r = array('status'=>1000, "result"=> $this->result); }
				}
			}
		}
		$this->response($this->r);
	}
	public function config_services_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->obj = $this->mongo_db->where(array('_id' => new \MongoId('5b8f6687f40e2ef99b808777'),'status'=>true))->get("service");
					if(!empty($this->obj)){
						$this->result = $this->obj[0];
					}
					if(!empty($this->obj)){ $this->r = array('status'=>1000, "result"=> $this->result); }
				}
			}
		}
		$this->response($this->r);
	}
	public function config_services_alego_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->obj = $this->mongo_db->where(array('_id' => new \MongoId('5b91b6e430a0590fb4accf68'),'status'=>true))->get("service");
					if(!empty($this->obj)){
						$this->result = $this->obj[0];
					}
					if(!empty($this->obj)){ $this->r = array('status'=>1000, "result"=> $this->result); }
				}
			}
		}
		$this->response($this->r);
	}
	public function config_nganluong_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->obj = $this->mongo_db->where(array('name_service'=>'NGANLUONG'))->get("payment_service");
					if(!empty($this->obj)){
						$this->result = $this->obj[0];
					}
					if(!empty($this->obj)){ $this->r = array('status'=>1000, "result"=> $this->result); }
				}
			}
		}
		$this->response($this->r);
	}	

	public function config_update_nganluong_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					if(!empty($_GET['param'])){
						$p = $this->apps->_params($_GET['param'],$this->_api_key);
						if(!empty($p->url_api)){ $this->obj['url_api'] = (string)$p->url_api; }
						if(!empty($p->receiver)){ $this->obj['receiver'] = (string)$p->receiver; }
						if(!empty($p->merchant_id)){ $this->obj['merchant_id'] = (int)$p->merchant_id; }
						if(!empty($p->merchant_pass)){ $this->obj['merchant_pass'] = (string)$p->merchant_pass; }
						if(!empty($p->keys)){
							$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),))->set($this->obj)->update('payment_service');
							$this->r = array('status'=>1000, "result"=> $this->result);
						}
					}
				}
			}
		}
		$this->response($this->r);
	}	
	public function config_update_shopdoithe_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					if(!empty($_GET['param'])){
						$p = $this->apps->_params($_GET['param'],$this->_api_key);
						if(!empty($p->merchant_id)){ $this->obj['merchant_id'] = (string)$p->merchant_id; }
						if(!empty($p->merchant_user)){ $this->obj['merchant_user'] = (string)$p->merchant_user; }
						if(!empty($p->merchant_password)){ $this->obj['merchant_password'] = (string)$p->merchant_password; }
						if(!empty($p->urlwebsite)){ $this->obj['urlwebsite'] = (string)$p->urlwebsite; }
						if(!empty($p->url)){ $this->obj['url'] = (string)$p->url; }
						if(!empty($p->keys)){
							$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),))->set($this->obj)->update('service');
							$this->r = array('status'=>1000, "result"=> $this->result);
						}
					}
				}
			}
		}
		$this->response($this->r);
	}
	public function config_update_alego_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					if(!empty($_GET['param'])){
						$p = $this->apps->_params($_GET['param'],$this->_api_key);
						if(!empty($p->Fnc)){ $this->obj['Fnc'] = (string)$p->Fnc; }
						if(!empty($p->agentid)){ $this->obj['agentid'] = (string)$p->agentid; }
						if(!empty($p->accid)){ $this->obj['accid'] = (string)$p->accid; }
						if(!empty($p->keymd5)){ $this->obj['keymd5'] = (string)$p->keymd5; }
						if(!empty($p->tripdes_key)){ $this->obj['tripdes_key'] = (string)$p->tripdes_key; }
						if(!empty($p->url)){ $this->obj['url'] = (string)$p->url; }
						if(!empty($p->keys)){
							$this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),))->set($this->obj)->update('service');
							$this->r = array('status'=>1000, "result"=> $this->result);
						}
					}
				}
			}
		}
		$this->response($this->r);
	}	
	public function addmoney_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$p = $this->apps->_params($_GET['param'],$this->_api_key);
					$client_id = $p->client_id;
					
					$balancer = array('balancer'=> 900000000);
					$this->result = $this->mongo_db->where(array('_id' => new \MongoId($client_id)))->set($balancer)->update('ask_users');
					$this->r = array('status'=>1000, "result"=> $this->result); 
				}
			}
		}
		$this->response($this->r);
	}	
	public function generic_get(){
		if(!empty($this->_level)){
			if($this->_level == 2){
				if($this->_role == 1 || $this->_role == 2){
					$this->obj = $this->mongo_db->get("site");
					if(!empty($this->obj)){
						$this->result = $this->obj[0];
					}
					if(!empty($this->obj)){ $this->r = array('status'=>1000, "result"=> $this->result); }
				}
			}
		}
		$this->response($this->r);
	}
	
	
}


?>