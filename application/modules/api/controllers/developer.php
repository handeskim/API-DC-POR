<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Developer extends REST_Controller {
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
				if((int)$this->_level == 2 ){
					if((int)$this->_role == 1 || (int)$this->_role == 2 ){
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
				if((int)$this->_level == 2){
					if((int)$this->_role == 1 || (int)$this->_role == 2){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->result = $this->apps->_del_developer($p);
								$this->r = $this->result;
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
				if((int)$this->_level == 2 ){
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
									$k = $this->mongo_db->where_gt('role',$this->_role)->where_gte('time_created',$date_start)->where_lte('time_created',$date_end)->get('api_keys');
								}else{
									$k = $this->mongo_db->where(array('reseller'=>$this->reseller,))->where_gt('role',$this->_role)->where_gte('time_created',$date_start)->where_lte('time_created',$date_end)->get('api_keys');
								}
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['reseller'])){ $v_reseller = $v['reseller'];}else{$v_reseller = null;}
										if(!empty($v['users'])){ $v_users = $v['users'];}else{$v_users = null;}
										if(!empty($v['key'])){ $v_key = $v['key'];}else{$v_key = null;}
										if(!empty($v['level'])){ $v_level = $v['level'];}else{$v_level = null;}
										if(!empty($v['role'])){ $v_role = $v['role'];}else{$v_role = null;}
										if(!empty($v['date_created'])){ $v_date_created = $v['date_created'];}else{$v_date_created = null;}
										if(!empty($v['ip_addresses'])){ $v_ip_addresses = $v['ip_addresses'];}else{$v_ip_addresses = null;}
										if(!empty($v['is_private_key'])){ $v_private_key = $v['is_private_key'];}else{$v_private_key = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'users' => $v_users,
											'reseller'=> $v_reseller,
											'key' => $v_key,
											'level' => $v_level,
											'role'=> $v_role,
											'date_created'=> $v_date_created,
											'ip_addresses'=> $v_ip_addresses
										);
									}
								}
								$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->result,'role'=> $this->_role,);
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