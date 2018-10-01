<?php 
class Core extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->obj_core = $this->_msg_response(2000);
		$this->confirm = array();
		$this->obj = array();
		$this->res = array();
		$this->param = array();
		$this->limits_obj = array();
		$this->objects = array();
		$this->prams = array();
		$this->_token_reseller = null;
		$this->_token = null;
		$this->transfer_p = 0;
		$this->level = 0;
		$this->role = 0;
		$this->is_private_key = null;
		$this->private_key = $this->config->item('private_key');
		$this->load->config('rest');
		$this->api_name = '';
		$this->code = 100;
		$this->balancer = 0;
		$this->msg = null;
		
	}
	public function _token($p){
		$param = array('username'=> $p->username,'password'=> md5($p->password));
		try{
			$this->obj_core = $this->mongo_db->select(array('role','email'))->where($param)->get('ask_users');
			if(!empty($this->obj_core)){ 
				return  array('token'=>handesk_encode(json_encode($this->obj_core[0])) );
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) {  return $this->_msg_response(2000);}
	}
	public function _api_name(){
		return $this->config->item('rest_key_name');
	}
	public function _action_insert_user($param){
	
		try{
			$this->obj_core = $this->mongo_db->insert('ask_users',$param);
			$this->_insert_log($param,'ask_users');
			return $this->obj_core;
		}catch (Exception $e) { return $this->obj_core; }
	}
	public function _balancer_users($user_id,$token){
	
		$reseller = $this->_token_reseller($token);
		try{
			$this->balancer = $this->mongo_db->where(array('_id' => new \MongoId($user_id),'reseller'=>$reseller))->get('ask_users');
			if(!empty($this->balancer)){
				if(isset($this->balancer[0]["balancer"])){
					return $this->balancer[0]["balancer"];
				}else{ return $this->_msg_response(2000); }
			}else{  return $this->_msg_response(2000); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _user_login($p){
		try{
			$reseller = (string)$this->_token_reseller($p->token);
			$this->obj_core = $this->mongo_db->where(array('username'=>$p->username,'password'=>md5($p->password),'reseller'=>$reseller,'status'=>true))->get('ask_users');
			if(!empty($this->obj_core[0]['_id'])){
					return $this->obj_core[0]['_id'];
			}else{  return $this->_msg_response(2000); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _users_bank($p){
	
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
				$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
				if(!empty(	$this->obj_core)){
					$this->obj_core = $this->mongo_db->where(array( 'client_id' => $p->client_id,'reseller'=> $reseller))->get('ask_bank');
					if(!empty($this->obj_core)){
							$info = array();
							foreach($this->obj_core as $v){
									$info[] = array(
										'bank_id' => getObjectId($v['_id']),
										'bank_name' => $v['bank_name'],
										'account_holders' => $v['account_holders'],
										'bank_account' => $v['bank_account'],
										'branch_bank' => $v['branch_bank'],
										'provinces_bank' => $v['provinces_bank'],
										'reseller' => $v['reseller'],
										'client_id' => $v['client_id'],
									);
							}
							return $info;
					}else{ return $this->obj ; }
				}else{ return $this->_msg_response(1001); }
		}catch (Exception $e) {  return $this->_msg_response(2000); }
	}
	public function _users_bank_del($p){
		
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
				$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
				if(!empty(	$this->obj_core)){
					$this->obj_core = $this->mongo_db->where(array( '_id' => new \MongoId($p->bank_id),'client_id' => $p->client_id,'reseller'=> $reseller))->get('ask_bank');
					if(!empty($this->obj_core)){
							$del = $this->mongo_db->where(array('_id' => new \MongoId($p->bank_id),'client_id'=>$p->client_id,'reseller'=> $reseller,))->delete('ask_bank');
							if(!empty($del)){
								return $this->_msg_response(2016);
							}else{ return $this->_msg_response(2014); }
					}else{ return $this->_msg_response(1002); }
				}else{ return $this->_msg_response(1001); }
		}catch (Exception $e) {  return $this->_msg_response(2000); }
	}
	public function _users_bank_add($p){
		
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'auth'=>md5($p->auth),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
			if(!empty($this->obj_core)){
				$array_bank = array( 'client_id' => $p->client_id,'bank_account' => $p->bank_account,'reseller'=> $reseller,);
				$this->obj_core = $this->mongo_db->where($array_bank)->get('ask_bank');
				if(empty(	$this->obj_core)){
						$this->obj = array();
						if(!empty($p->client_id)){ $this->obj['client_id'] = $p->client_id; }
						if(!empty($p->bank_id)){ $this->obj['bank_id'] = $p->bank_id; }
						if(!empty($p->bank_name)){ $this->obj['bank_name'] = $p->bank_name; }
						if(!empty($p->bank_option)){ $this->obj['bank_option'] = $p->bank_option; }
						if(!empty($p->account_holders)){ $this->obj['account_holders'] = $p->account_holders; }
						if(!empty($p->bank_account)){ $this->obj['bank_account'] = $p->bank_account; }
						if(!empty($p->branch_bank)){ $this->obj['branch_bank'] = $p->branch_bank; }
						if(!empty($p->provinces_bank)){ $this->obj['provinces_bank'] = $p->provinces_bank; }
						$this->obj['time_created'] = time();
						$this->obj['date_created'] = date("Y-m-d H:i:s",time());
						if(!empty($reseller)){ $this->obj['reseller'] = $reseller; }
						$this->obj_core = $this->mongo_db->insert('ask_bank',$this->obj);
						if(!empty($this->obj_core)){
							return array('bank_id'=> getObjectId($this->obj_core));
						}else{  return $this->_msg_response(100); }
				}else{  return $this->_msg_response(2015); }
			}else{ return $this->_msg_response(1001); }
		}catch (Exception $e) {  return $this->_msg_response(2000); }
	}
	public function _users_change_password($p){
		$reseller = (string)$this->_token_reseller($p->token);
		
		$check_array = array('_id' => new \MongoId($p->client_id),'password'=>md5($p->password_old),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
				
			if(!empty($this->obj_core)){
				
				if(!empty($p->password_new)){ 
					$this->obj['password'] = md5($p->password_new); 
				}
				$this->obj['date_update'] = date("Y-m-d H:i:s",time());	
				$update = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),'password'=> md5($p->password_old),'reseller'=>$reseller, ))->set($this->obj)->update('ask_users');
				if($update==true){
					return  $this->_msg_response(1000);
				}else{return $this->_msg_response(100);}
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}

	public function _users_change_auth($p){
		$reseller = (string)$this->_token_reseller($p->token);
		
		$check_array = array('_id' => new \MongoId($p->client_id),'auth'=>md5($p->password_old),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
				
			if(!empty($this->obj_core)){
				
				if(!empty($p->password_new)){ 
					$this->obj['auth'] = md5($p->password_new); 
				}
				$this->obj['date_update'] = date("Y-m-d H:i:s",time());	
				$update = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),'auth'=> md5($p->password_old),'reseller'=>$reseller, ))->set($this->obj)->update('ask_users');
				if($update==true){
					return  $this->_msg_response(1000);
				}else{return $this->_msg_response(100);}
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _users_update($p){
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
			if(!empty($this->obj_core)){
					if(!empty($p->email)){ $this->obj['email'] = $p->email; }
					if(!empty($p->full_name)){ $this->obj['full_name'] = $p->full_name; }
					if(!empty($p->phone)){ $this->obj['phone'] = $p->phone; }
					if(!empty($p->address)){ $this->obj['address'] = $p->address; }
					if(!empty($p->city)){ $this->obj['city'] = $p->city; }
					if(!empty($p->country)){ $this->obj['country'] = $p->country; }
					if(!empty($p->birthday)){ $this->obj['birthday'] = date("d/m/Y",strtotime($p->birthday)); }
					if(!empty($p->auth)){ $this->obj['auth'] = md5($p->auth); }
					if(!empty($p->password)){ $this->obj['password'] = md5($p->password); }
					$this->obj['reseller'] = $reseller;
					$this->obj['date_created'] = date("Y-m-d H:i:s",time());
					$this->obj['time_created'] = time();
					$update = $this->mongo_db->where(array('_id' => new \MongoId($p->client_id),'reseller'=>$reseller, ))->set($this->obj)->update('ask_users');
					if($update==true){
						return $update;
					}else{return $this->_msg_response(100);}
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _users_developer($p){
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
			if(!empty($this->obj_core)){
				$this->obj_core = $this->mongo_db->where(array('users'=>$p->client_id,))->get('api_keys');
				if(!empty($this->obj_core)){
					foreach($this->obj_core as $k){
						$level = $k['level'];
						$level_p = null;
						if($level==1){
							$level = "Not Active Limit Client";
						}else if($level==2){
							$level = "Active Supper";
						}else if($level==3){
							$level = "Active Limit Resller";
						}
						$this->obj_core['developer'] = array(
								'level'=> $level,
								'date_created'=> $k['date_created'],
								'merchant_id'=> $k['key'],
								'secret_key'=> $k['is_private_key'],
						);
					}
					return $this->obj_core['developer'];
				}else{  return $this->_msg_response(1002); }
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _users_developer_create($p){
		$reseller = (string)$this->_token_reseller($p->token);
		$check_array = array('_id' => new \MongoId($p->client_id),'reseller'=> $reseller,);
		try{
			$this->obj_core = $this->mongo_db->where($check_array)->get('ask_users');
			if(!empty($this->obj_core)){
				$this->obj_core = $this->mongo_db->where(array('users'=>$p->client_id,'reseller'=> $reseller,))->get('api_keys');
				if(empty($this->obj_core)){
						if(!empty($p->client_id)){ $this->obj['users'] = $p->client_id; }	
						if(!empty($p->website)){ $this->obj['website'] = $p->website; }	
						$this->obj['reseller'] = $reseller;
						$hash = core_encrypt($this->private_key .'-'.time().'-'.$reseller.'-'.$p->client_id);
						$key = md5(sha1($hash));
						$this->obj['key'] = $key;
						$this->obj['is_private_key'] = $hash;
						$this->obj['ignore_limits'] = true;
						$this->obj['level'] = 1;
						$this->obj['role'] = 4;
						$this->obj['date_created'] = date("Y-m-d H:i:s",time());
						$this->obj['time_created'] = time();
						$this->obj['ip_addresses'] = $this->input->ip_address();
						$this->limits_obj['api_key'] = $key;
						$this->limits_obj['date_created'] = date("Y-m-d H:i:s",time());
						$this->limits_obj['time_created'] = time();
						$this->limits_obj['count'] = 100000;
						$this->limits_obj['uri'] = 'api/check';
						$this->limits_obj['hour_started'] = time();
						$this->obj_core = $this->mongo_db->insert('api_keys',$this->obj);
						$this->mongo_db->insert('api_limits',$this->limits_obj);
						return $this->obj_core;
				}else{  return $this->_msg_response(2017); }
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _user_info($user_id){
		try{
			$this->obj_core = $this->mongo_db->where(array('_id' => new \MongoId($user_id)))->get('ask_users');
			if(!empty($this->obj_core)){
				return $this->obj_core[0];
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _user_create_check($username,$email){
		try{
			$this->obj_core = $this->mongo_db->where_or(array('username'=>$username, 'email'=>$email))->get('ask_users');
			if(!empty($this->obj_core)){
				return true;
			}else{ return false; }
		}catch (Exception $e) { return false; }
	}
	public function _logs_user_api($param,$action){
		try{
			$this->prams = array(
				'action' => $action,
				'date_insert'=> date("Y-m-d H:i:s A"),
				'time_insert'=> time(),
				'param'=> $param,
			);
			$this->mongo_db->insert('logs_user_api',$this->prams);
		}catch (Exception $e) {  }
	}

	public function _transfer_plus($balancer,$client_id,$param){
		try{
			$update = $this->mongo_db->where(array('_id' => new \MongoId($client_id),))->set(array('balancer'=>$balancer))->update('ask_users');
			$status = false;
			if($update){ $status = true; }else{ $status = false; }
			$this->confirm = $param;
			$this->confirm['status'] = $status;
			$this->confirm['action'] = 'plus';
			$this->confirm['date_update_transfer'] = date("Y-m-d H:i:s A");
			$this->confirm['time_update_transfer'] = time();
			$w =  $this->_transfer_log($this->confirm);
			if($w==true){
				return true;
			}else{
				return false;
			}
		}catch (Exception $e) {  
			return false;
		}
	}
	public function _transfer_minus($balancer,$client_id,$param){
		try{
		$update = $this->mongo_db->where(array('_id' => new \MongoId($client_id),))->set(array('balancer'=>$balancer))->update('ask_users');
		$status = false;
			if($update){ $status = true; }else{ $status = false; }
		$this->confirm = $param;
		$this->confirm['status'] = $status;
		$this->confirm['action'] = 'minus';
		$this->confirm['date_update_transfer'] = date("Y-m-d H:i:s A");
		$this->confirm['time_update_transfer'] = time();
		$w = $this->_transfer_log($this->confirm);
		if($w==true){
			return true;
		}else{
			return false;
		}
		}catch (Exception $e) {  
			return false;
		}
	}	
	public function _transfer_withdrawal_minus($balancer,$client_id,$param){
		try{
		$update = $this->mongo_db->where(array('_id' => new \MongoId($client_id),))->set(array('balancer'=>$balancer))->update('ask_users');
		$status = false;
		if($update){ $status = true; }else{ $status = false; }
		$this->confirm = $param;
		$this->confirm['status'] = $status;
		$this->confirm['action'] = 'minus';
		$this->confirm['date_update_transfer'] = date("Y-m-d H:i:s A");
		$this->confirm['time_update_transfer'] = time();
		$w = $this->_transfer_log($this->confirm);
		$this->confirm['transfer_transaction'] = $w;
		$this->_withdrawal_log($this->confirm);
		if($w==true){
			return true;
		}else{
			return false;
		}
		}catch (Exception $e) {  
			return false;
		}
	}
	
	private function _transfer_log($param){
		try{
			return $this->mongo_db->insert('transfer_log',$param);
		}catch (Exception $e) { 
			return false;
		}
	}
	private function _withdrawal_log($param){
		try{
			return $this->mongo_db->insert('withdrawal',$param);
		}catch (Exception $e) { 
			return false;
		}
	}
	
	private function _insert_log($params,$collection){
		try{
			$this->prams = array(
				'collect_insert' => $collection,
				'date_insert'=> date("Y-m-d H:i:s A"),
				'time_insert'=> time(),
				'param'=>$params,
			);
			$this->mongo_db->insert('log_insert',$this->prams);
		}catch (Exception $e) {  }
	}
	public function _del_developer($p){
		$reseller = (string)$this->_token_reseller($p->token);
		try{
			$this->prams = array(
				'key_del' => $p->keys,
				'date_insert'=> date("Y-m-d H:i:s A"),
				'time_insert'=> time(),
				'param'=>$p,
			);
			$check = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $reseller,))->where_gt('role',2)->get('api_keys');
			if(!empty($check)){
				$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $reseller,))->where_gt('role',2)->delete('api_keys');
				$this->prams['status'] = $del;
				$this->prams['action'] = 'delete-api';
				$this->mongo_db->insert('log_action_developer',$this->prams);
				return $this->_msg_response(1000);
			}else{
				$this->prams['status'] = false;
				$this->prams['action'] = 'delete-api';
				$this->mongo_db->insert('log_action_developer',$this->prams);
				return $this->_msg_response(1002);
			}
			
		}catch (Exception $e) {  }
	}	
	public function _del_staff_cms($p){
		$reseller = (string)$this->_token_reseller($p->token);
		try{
			$this->prams = array(
				'key_del' => $p->keys,
				'date_insert'=> date("Y-m-d H:i:s A"),
				'time_insert'=> time(),
				'param'=>$p,
			);
			$check = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $reseller,))->where_gt('role',2)->get('ask_users');
			if(!empty($check)){
				$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $reseller,))->where_gt('role',2)->delete('ask_users');
				$this->prams['status'] = $del;
				$this->prams['action'] = 'delete';
				$this->mongo_db->insert('log_action_developer',$this->prams);
				return $this->_msg_response(1000);
			}else{
				$this->prams['status'] = false;
				$this->prams['action'] = 'delete-account';
				$this->mongo_db->insert('log_action_developer',$this->prams);
				return $this->_msg_response(1002);
			}
			
		}catch (Exception $e) {  }
	}
	public function _del_bank_account($p){
		$reseller = (string)$this->_token_reseller($p->token);
		try{
			$this->prams = array(
				'key_del' => $p->keys,
				'date_insert'=> date("Y-m-d H:i:s A"),
				'time_insert'=> time(),
				'param'=>$p,
			);
			$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $reseller,))->delete('ask_bank');
			$this->prams['status'] = $del;
			$this->mongo_db->insert('log_action_developer',$this->prams);
			$this->prams['action'] = 'delete-bank';
			return $this->_msg_response(1000);
		}catch (Exception $e) {  }
	}	
	public function _del_transfer_log($p){
		$reseller = (string)$this->_token_reseller($p->token);
		try{
			$this->prams = array(
				'key_del' => $p->keys,
				'date_insert'=> date("Y-m-d H:i:s A"),
				'time_insert'=> time(),
				'param'=>$p,
			);
			$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $reseller,'transaction'=>'done'))->delete('transfer_log');
			$this->prams['status'] = $del;
			$this->mongo_db->insert('log_transactions',$this->prams);
			$this->prams['action'] = 'delete-transfer_log';
			return $this->_msg_response(1000);
		}catch (Exception $e) {  }
	}
	public function _del_withdrawal_cms($p){
		$reseller = (string)$this->_token_reseller($p->token);
		try{
			$this->prams = array(
				'key_del' => $p->keys,
				'date_insert'=> date("Y-m-d H:i:s A"),
				'time_insert'=> time(),
				'param'=>$p,
			);
			$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $reseller,'transaction'=>'done'))->delete('withdrawal');
			$this->prams['status'] = $del;
			$this->mongo_db->insert('log_transactions',$this->prams);
			$this->prams['action'] = 'delete-withdrawal';
			return $this->_msg_response(1000);
		}catch (Exception $e) {  }
	}
	
	public function _token_reseller($token){
		try{
			$token = json_decode(handesk_decode($token));
			return getObjectId($token->_id);
		}catch (Exception $e) { return $this->_token_reseller; }
	}
	public function _level_api($api_key){
		try{
			$this->obj_core = $this->mongo_db->select(array('level'))->where(array('key'=>$api_key))->get('api_keys');
			if(!empty($this->obj_core[0])){
				return $this->level = $this->obj_core[0]['level'];
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }	
	}
	public function _role($api_key){
		try{
			$this->obj_core = $this->mongo_db->select(array('role'))->where(array('key'=>$api_key))->get('api_keys');
			if(!empty($this->obj_core[0]['role'])){
				return $this->obj_core[0]['role'];
			}else{  return $this->_msg_response(1001); }
		}catch (Exception $e) { return $this->_msg_response(2000); }
	}
	public function _params($string,$api_key){
		return json_decode(decrypt_obj($string,$api_key,$this->_is_private_key($api_key)));
	}
	public function _result($status,$prams,$api_key){
		$this->obj  = $this->_msg_response($status);
		$this->obj['result'] = encrypt_obj(json_encode($prams),$api_key,$this->_is_private_key($api_key));
		return $this->obj;
	}
	public function _msg_response($code=null){
		try{
			if(!empty($code)){
				$this->code = (int)$code;
				$k = $this->mongo_db->where(array('code'=>$this->code))->get('msg_reponse');
				if(!empty($k)){
					$x = array();
					foreach($k as $v){
						$x['status'] = $v['code'];$x['msg'] = $v['msg'];}
					return $x;
				}else{ return array('status'=>101,'msg'=>'thiếu trạng thái trả về, không xác định');}
			}else{ return array('status'=>101,'msg'=>'thiếu trạng thái trả về, không xác định');}
		}catch (Exception $e) { 
			return array('status'=>100,'msg'=>'lỗi không xác định');
		}
		
	}
	public function _is_private_key($api_key){
		try{
			$this->obj_core = $this->mongo_db->select(array('is_private_key'))->where(array('key'=>$api_key))->get('api_keys');
			if(!empty($this->obj_core[0])){
				return $this->is_private_key = $this->obj_core[0]['is_private_key'];
			}
		}catch (Exception $e) { $this->is_private_key; }
		return $this->is_private_key;
		
	}
	
	
	private function _doithe_transaction_create($param){
		try{
			$param['ip'] = $this->input->ip_address();
			$param['time_created'] = time();
			$param['date_created'] = date("Y-m-d H:i:s",time());
			$set_transaction = $this->mongo_db->insert('doithe_transaction',$param);
			if($set_transaction==true){
				return getObjectId($set_transaction);
			}
		}catch (Exception $e) { $this->obj; }
		return $this->obj;
	}
	public function _Service_Card_Change_Sendding($p){
		$this->card_change = $this->_Service_Card_Change_Init();
		if(!empty($p['card_seri'])){ $this->card_change['card_seri'] = $p['card_seri']; }else{ $this->card_change['card_seri'] = 1;}
		if(!empty($p['card_code'])){ $this->card_change['card_code'] = $p['card_code']; }else{ $this->card_change['card_code'] = 1;}
		if(!empty($p['card_type'])){ $this->card_change['card_type'] = $p['card_type']; }else{ $this->card_change['card_type'] = 1;}
		if(!empty($p['card_amount'])){ 
			if((int)$this->card_change['card_type'] = 1){
				$this->card_change['card_amount'] = (int)$p['card_amount']; 
			}else{
				$this->card_change['card_amount'] = null;
			}
		}else{ 
			$this->card_change['card_amount'] = null;
		}
	
		$note = $this->_doithe_transaction_create($p);
		$this->card_change['note'] = $note;
		$plaintext = $this->card_change['merchant_id']."|".$this->card_change['merchant_user']."|".$this->card_change['merchant_password']."|". $this->card_change['card_type']."|".$this->card_change['card_amount']."|".$this->card_change['card_seri']."|".$this->card_change['card_code'];
		$this->card_change['sign'] = strtoupper(hash('sha256', $plaintext));
		$this->url = $this->card_change['url'].'?merchant_id='.$this->card_change['merchant_id'].'&merchant_user='.$this->card_change['merchant_user'].'&merchant_password='.$this->card_change['merchant_password'].'&card_type='.$this->card_change['card_type'].'&card_amount='.$this->card_change['card_amount'].'&card_seri='.$this->card_change['card_seri'].'&card_code='.$this->card_change['card_code'].'&note='.urlencode($this->card_change['note']).'&sign='.$this->card_change['sign'];
		
		$this->obj = getcURL($this->url);
		$this->obj = str_replace("\xEF\xBB\xBF",'',$this->obj); 
		$this->obj = json_decode($this->obj);
		
		
		/////////// TEST
		// $this->obj = (object) array('status' => 2, 'amount' => (int)$p['card_amount']);
		////////////////Card Insert////////////////
		if(!empty($p['card_type'])){$type = $p['card_type'];}else{$type = 1;}
		/////////////// Card Fee ///////////////////
		$card_fee = $this->_card_fee_change($type,$p['client_id']);
		if(!empty($card_fee)){
			$card_deduct = (int)$card_fee['deduct'];
			$card_rose = 0;
		}else{ $card_deduct = 99; $card_rose = 0; }
	
		/////////////////////////////////////////////
		$this->objects['tracking'] = $note;
		$this->objects['card_seri'] = $p['card_seri'];
		$this->objects['card_code'] = $p['card_code'];
		$this->objects['card_type'] = (int)$p['card_type'];
		$this->objects['card_amount'] = (int)$p['card_amount'];
		$this->objects['client_id'] = $p['client_id'];
		$this->objects['publisher'] = $p['publisher'];
		$this->objects['reseller'] = $p['reseller'];
		$this->objects['card_deduct'] = (int)$card_deduct;
		$this->objects['card_rose'] = (int)$card_rose;
		$this->objects['note'] = $note;
		$this->objects['date_create'] = date("Y-m-d H:i:s",time());
		$this->objects['time_create'] = time();
	
		if(isset($this->obj->status)){
			if(isset($this->obj->transaction_id)){
				$transaction_service = (string)$this->obj->transaction_id;
			}else{
				$transaction_service = null;
			}
			$status = $this->shopnapthe($this->obj->status);
			$this->objects['card_status'] = $status['status'];
			$this->objects['card_message'] = $status['msg'];
			// $this->objects['tracking'] = $note;
			if($this->obj->status==23){
				$this->mongo_db->insert('block_seri_card',array('card_seri'=>$p['card_seri'],'card_type'=>(int)$p['card_type'],'time_create'=>time(),'client_id'=>$p['client_id']));
			}
			if($this->obj->status==2){
				$this->mongo_db->insert('block_seri_card',array('card_seri'=>$p['card_seri'],'card_type'=>(int)$p['card_type'],'time_create'=>time(),'client_id'=>$p['client_id']));
				///////////////////Client Info ////////////////////////////
				$beneficiary_id = $p['client_id'];
				$publisher_id = $p['publisher'];
				$reseller_id = $p['reseller'];
				$check_beneficiary = array( '_id' => new \MongoId($beneficiary_id),);
				$beneficiary = $this->mongo_db->select(array('full_name','balancer'))->where($check_beneficiary)->get('ask_users');
				///////////////////////////////////////////////////////////
				if(!empty($this->obj->amount)){
					$card_amount = (int)$this->obj->amount;
				}else{  $card_amount = 0; }
				if($card_deduct == 0){ $fee = 0; }else{
					$fee = (int)$card_amount * ($card_deduct/100);
				}
				
				$money_transfer =  $card_amount - (int)$fee;
				if($card_rose==0){ $money_rose = 0; }else{
					$money_rose =  $money_transfer * ($card_rose/100);
				}
				$this->objects['money_transfer'] = (int)$card_amount;
				$this->objects['money_rose'] = (int)$money_rose;
				$total_transfer = (int)$money_transfer + (int)$money_rose;
				////////////////////Transaction Insert///////////////////////////
				$balancer_plus = (int)$beneficiary[0]['balancer'] + (int)$total_transfer;
				$this->objects['fee']	= (int)$fee;
				$this->objects['total_transfer']	= (int)$total_transfer;
				$this->objects['transaction_card'] = 'done';
				$this->objects['transaction_service'] = $transaction_service;
				$this->mongo_db->insert('log_card_change',$this->objects);
				$this->objects['types']	= 'transfer';
				$this->objects['balancer_clients']	= (int)$beneficiary[0]['balancer'];
				$this->objects['beneficiary_balancer']	= $beneficiary[0]['balancer'];
				$this->objects['balancer_plus']	= (int)$balancer_plus;
				$this->objects['balancer_munis']	=  0;
				$this->objects['payer_balancer'] =  0;
				$this->objects['payer_id'] =  'In system';
				$this->objects['payer_name']	=  'In system';
				$this->objects['beneficiary_id']	= 'In system';
				$this->objects['beneficiary']	=  'In system';
				$this->objects['client_name']	= $beneficiary[0]['full_name'];
				$this->objects['password_transfer']	= md5($beneficiary_id);
				$this->objects['type'] = 'card_transfers';
				$this->objects['note'] = $note;
				$this->objects['transaction']	= 'done';
				$this->objects['bank_name'] = 'Internal Card ';
				$this->objects['account_holders'] = $beneficiary[0]['full_name'];
				$this->objects['bank_account'] = $beneficiary_id;
				$this->objects['provinces_bank'] = 'In system';
				$this->objects['branch_bank'] = 'In system';
				$this->_transfer_plus($balancer_plus,$beneficiary_id,$this->objects);
				
				return $this->shopnapthe((int)$this->obj->status);
				
			}else{
				$this->objects['transaction_service'] = $transaction_service;
				$this->objects['transaction_card'] = 'reject';
				$this->mongo_db->insert('log_card_change',$this->objects);
				return $this->shopnapthe((int)$this->obj->status);
			}
		}else{
			$this->objects['transaction_card'] = 'reject';
			$this->objects['card_status'] = 4098;
			$this->objects['card_message'] = 'lỗi hệ thống';
			$this->mongo_db->insert('log_card_change',$this->objects);
			return $this->shopnapthe(99);
		}
	}
	
	public function _Service_Card_Change_Init(){
		try{
			$this->obj_core = $this->mongo_db->select(array('merchant_id','merchant_user','merchant_password','urlwebsite','url'))->where(array('type'=>1))->get('service');
			if(!empty($this->obj_core)){
				$this->obj_core = array(
					'url' => $this->obj_core[0]['url'],
					'urlwebsite' => $this->obj_core[0]['urlwebsite'],
					'merchant_id' => $this->obj_core[0]['merchant_id'],
					'merchant_user' => $this->obj_core[0]['merchant_user'],
					'merchant_password' => $this->obj_core[0]['merchant_password'],
				);
			}
		}catch (Exception $e) { $this->obj_core; }
		return $this->obj_core;
	}
	public function _card_config(){
		try{
			$obj_core = $this->mongo_db->select(array('name','value','card_amount'))->get('card_option');
			if(!empty($obj_core)){
				foreach($obj_core as $v){
					$this->obj_core[] = array(
						'name' => $v['name'],
						'value' => $v['value'],
						'card_amount' => $v['card_amount'],
					);
				}
			}
		}catch (Exception $e) { $this->obj_core; }
		return $this->obj_core;
	}
		public function _card_fee($type){
			try{
			$objects = $this->mongo_db->where(array('card_type'=> (int)$type,))->get('card');
			if(!empty($objects)){
				return $objects[0];
			}
		}catch (Exception $e) {
			return array();
		}
		
	}
	public function _card_fee_change($type,$client_id){
			try{
			$clients = $this->mongo_db->where(array('_id' => new \MongoId($client_id)))->get('ask_users');
			if(!empty($clients)){
				if(isset($clients[0]['type'])){
					$objects = $this->mongo_db->where(array('card_type'=> (int)$type,))->get('card');
					if(!empty($objects)){
						if($clients[0]['type']==='VIP'){
							return (int)$objects[0]['deduct_vip'];
						}else{
							return (int)$objects[0]['deduct'];
						}
					}
				}else{return 99;}
			}else{ return 99;}
			
		}catch (Exception $e) {
			return 99;
		}
		
	}
	public function _rose_reseller(){
			try{
			$obj_core = $this->mongo_db->select(array('rose_reseller'))->get('config');
			if(!empty($obj_core)){
				return (int)$obj_core[0]['rose_reseller'] / 100;
			}
		}catch (Exception $e) { $this->transfer_p; }
		return $this->transfer_p;
	}
	public function _rose_client(){
			try{
			$obj_core = $this->mongo_db->select(array('rose_client'))->get('config');
			if(!empty($obj_core)){
				return (int)$obj_core[0]['rose_client'] / 100;
			}
		}catch (Exception $e) { $this->transfer_p; }
		return $this->transfer_p;
	}
	public function _rose_partner(){
			try{
			$obj_core = $this->mongo_db->select(array('rose_partner'))->get('config');
			if(!empty($obj_core)){
				return (int)$obj_core[0]['rose_partner'] / 100;
			}
		}catch (Exception $e) { $this->transfer_p; }
		return $this->transfer_p;
	}
	public function _transfer_fee(){
			try{
			$obj_core = $this->mongo_db->select(array('transfer'))->get('config');
			if(!empty($obj_core)){
				return (int)$obj_core[0]['transfer'];
			}
		}catch (Exception $e) { $this->transfer_p; }
		return $this->transfer_p;
	}
	public function _withdrawal_fee(){
		try{
			$obj_core = $this->mongo_db->select(array('withdraw'))->get('config');
			if(!empty($obj_core)){
				return (int)$obj_core[0]['withdraw'];
			}
		}catch (Exception $e) { $this->transfer_p; }
		return $this->transfer_p;
	}	
	
	public function _Service_Alego_ByCard_Sendding($param,$Func){
		$alego_conf = $this->_alego_config();
		$AccID = $alego_conf['accid'];
		$this->objects = $param;
		$this->objects['date_create'] = date("Y-m-d H:i:s",time());
		$this->objects['time_create'] = time();
		$transaction = $this->_alego_transaction_create($this->objects);
		$connectKey = $alego_conf['keymd5'];
		$data = json_encode($param);
		$EncData = $this->_alego_encrypt($data);
		$ver = '1.0';
    $agentId = $alego_conf['agentid'];
		$CheckSum = md5($Func . $ver . $agentId . $AccID . $EncData . $connectKey);
		$inputs = array(
		'Fnc' => $Func,'Ver' => $ver,'AgentID' => $agentId,
		'AccID' => $AccID,'EncData' => $EncData,'Checksum' => $CheckSum,
		);
		$input = json_encode($inputs);
		$url = $alego_conf['url'];
		$this->result = $this->_alego_postUrl($url,$input);
		if(!empty($this->result['RespCode'])){
			if($this->result['RespCode']==='00'){
				$this->comfim($this->objects);
				if(!empty($this->result['EncData'])){
					$this->obj = json_decode($this->_alego_decrypt($this->result['EncData']),true);
					if(!empty($this->obj)){
						$this->res = array(
							'RespCode' => $this->result['RespCode'],
							'transaction' => 'done',
							'ProductCode' => $this->obj["ProductCode"],
							'RefNumber' => $this->obj["RefNumber"],
							'TransID' => $this->obj["TransID"],
							'TransDate' => $this->obj["TransDate"],
							'ResType' => $this->obj["ResType"],
							'CardInfo' => $this->obj["CardInfo"],
						);
						$this->mongo_db->where(array('_id' => new \MongoId($transaction)))->push('response',$this->res)->update('alego_transaction');
						return $this->res;
					}else{return $this->result;}
				}else{
					return $this->result;
				}
			}else{ 
				$this->reject_buycard($this->objects); 
				return $this->obj; 
			}	
		}else{ $this->reject_buycard($this->objects); return $this->obj;  }
		
	}
	private function reject_buycard($p){
		$orders = $this->mongo_db->where(array('Tracking_Orders' => $p['RefNumber']))->get('transfer_log');
		if(!empty($orders)){
			$this->objects = $orders[0];
			$clients = $this->mongo_db->where(array('_id' => new \MongoId($this->objects['client_id'])))->get('ask_users');
			$balancer_client = $this->objects['client_id'];
			$balancer_plus = (int)$clients[0]['balancer'] + (int)$this->objects['total_transfer'];
			$this->param = array(
				'Tracking_Orders' =>  $this->objects['Tracking_Orders'],
				'money_transfer' =>	$this->objects['money_transfer'],
				'date_create' => date("Y-m-d H:i:s",time()),
				'time_create' =>	time(),
				'fee' =>	0,
				'total_transfer' =>	$this->objects['total_transfer'],
				'balancer_clients' =>	$this->objects['balancer_clients'],
				'beneficiary_balancer' =>	$this->objects['beneficiary_balancer'],
				'balancer_plus' =>	$balancer_plus,
				'balancer_munis' => 0,
				'payer_balancer' =>	$this->objects['payer_balancer'],
				'payer_id' =>	$this->objects['payer_id'],
				'beneficiary_id' =>	$this->objects['beneficiary_id'],
				'beneficiary' => 'Buy Card Reject',
				'payer_name' => 'Buy Card Reject',
				'client_id' =>	$this->objects['client_id'],
				'client_name' =>	$this->objects['client_name'],
				'reseller' =>	$this->objects['reseller'],
				'transaction' => 'done',
				'bank_id' => $this->objects['bank_id'],
				'auth' =>	$this->objects['auth'],
				'bank_name' => 'Buy Card Reject',
				'account_holders' => $this->objects['account_holders'],
				'bank_account' =>	$this->objects['bank_account'],
				'provinces_bank' => 'Buy Card Reject',
				'branch_bank' => 'Buy Card Reject' ,
				'type' => 'buy',
			);
			$this->_transfer_plus($balancer_plus,$balancer_client,$this->param);
			$this->mongo_db->where(array('_id' => new \MongoId($p['RefNumber']),'transaction_card'=>'hold'))->set(array('transaction_card'=>'reject'))->update('cart');
		}
	}
	private function comfim($p){
		if(!empty($p)){
			if(!empty($p['RefNumber'])){
				$RefNumber = $p['RefNumber'];
				$this->mongo_db->where(array('_id' => new \MongoId($RefNumber),'transaction_card'=>'hold'))->set(array('transaction_card'=>'done'))->update('cart');
			}
		}
	}
	private function _alego_postUrl($url, $data) {
    $headerArray = array('Content-Type: application/json; charset=UTF-8',);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			$result = curl_exec($ch);
			$result = json_decode($result, true);
			return $result;
	}
	private function _alego_transaction_create($param){
		try{
			
			$set_transaction = $this->mongo_db->insert('alego_transaction',$param);
			if($set_transaction==true){
				return getObjectId($set_transaction);
			}
		}catch (Exception $e) { $this->obj; }
		return $this->obj;
	}
	public function _alego_config(){
		try{
			$obj_core = $this->mongo_db->where(array('type'=>3))->get('service');
			if(!empty($obj_core)){
				return $obj_core[0];
			}
		}catch (Exception $e) { $this->obj; }
		return $this->obj;
	}
	
	public function _alego_encrypt($input){
		$alego_conf = $this->_alego_config();
		$key_seed = $alego_conf['tripdes_key'];
		$input = trim($input);
    $block = mcrypt_get_block_size('tripledes', 'ecb');
    $len = strlen($input);
    $padding = $block - ($len % $block);
    $input .= str_repeat(chr($padding), $padding);
    $key = substr(md5($key_seed), 0, 24);
    $iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $encrypted_data = mcrypt_encrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, $iv);
    $encrypted_data = base64_encode($encrypted_data);
    return $encrypted_data;
	}
	public function _alego_decrypt($input){
		$alego_conf = $this->_alego_config();
		$key_seed = $alego_conf['tripdes_key'];
		$input = base64_decode($input);
    $key = substr(md5($key_seed), 0, 24);
    $text = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, 'Mkd34ajdfka5');
    $block = mcrypt_get_block_size('tripledes', 'ecb');
    $packing = ord($text{strlen($text) - 1});
    if ($packing and ( $packing < $block)) {for ($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--) {if (ord($text{$P}) != $packing) {$packing = 0;}}}
    $text = substr($text, 0, strlen($text) - $packing);
    return $text;
	}
	public function _min_withdraw(){
			try{
			$obj_core = $this->mongo_db->select(array('min_withdraw'))->get('config');
			if(!empty($obj_core)){
				return (int)$obj_core[0]['min_withdraw'];
			}
		}catch (Exception $e) { $this->transfer_p; }
		return $this->transfer_p;
	}
	private function shopnapthe($code){
		switch ((int)$code) {
				case 2:
					return $this->_msg_response(4002);
					break;
				case 10:
					return $this->_msg_response(4010);
					break;
				case 11:
					return $this->_msg_response(4010);
					break;
				case 12:
					return $this->_msg_response(4012);
					break;
				case 13:
					return $this->_msg_response(4013);
					break;
				case 14:
					return $this->_msg_response(4014);
					break;
				case 15:
					return $this->_msg_response(4015);
					break;
				case 16:
					return $this->_msg_response(4016);
					break;
				case 17:
					return $this->_msg_response(4017);
					break;
				case 18:
					return $this->_msg_response(4018);
					break;
				case 19:
					return $this->_msg_response(4018);
					break;
				case 20:
					return $this->_msg_response(4020);
					break;
				case 21:
					return $this->_msg_response(4021);
					break;
				case 22:
					return $this->_msg_response(4022);
					break;
				case 23:
					return $this->_msg_response(4023);
					break;
				case 24:
					return $this->_msg_response(4024);
					break;
				case 98:
					return $this->_msg_response(4098);
					break;
				case 99:
					return $this->_msg_response(4099);
					break;
				case 101:
					return $this->_msg_response(4099);
					break;
				default:
					return $this->_msg_response(2000);
		}
	}
	public function _isValidDomainName($domain) {
		  return (preg_match('/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain));
	}
	public function _isValidMx($mx) {
		  return (preg_match('/^(0?[0-9]|[0-5][0-0])$/', $mx));
	}
	public function _isValidEmailName($email) {
		  return (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email));
	}
	///////////////// Zone Validator ///////////////////////////
	
	public function _isValidIpAddressRegex($string){
		return (preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $string));
	}
	public function _isValidIpHostnameRegex($string){
		return (preg_match('/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/', $string));
	}
}

?>