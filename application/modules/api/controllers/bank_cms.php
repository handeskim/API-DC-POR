<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Bank_cms extends REST_Controller {
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
									$this->result = $this->apps->_del_bank_account($p);
								$this->r = $this->result;
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
		public function AddNewBank_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2){
					if((int)$this->_role == 1 || (int)$this->_role == 2){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
									$this->objects = array(
										'name'=>$p->name,
										'type_bank'=>$p->type_bank,
									);
									$id = $this->mongo_db->insert('bank_config',$this->objects);
									$this->r = array('status'=>$this->apps->_msg_response(1000),'result'=>$id);
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	public function bank_config_cms_del_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								 $this->result = $this->mongo_db->where(array('_id' => new \MongoId($p->keys),'reseller'=> $reseller,))->delete('bank_config');
								 $this->r = $this->apps->_msg_response(1000);
								 $this->r['result'] = $this->result;
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
									$k = $this->mongo_db->where_gte('time_created',$date_start)->where_lte('time_created',$date_end)->get('ask_bank');
								}else{
									$k = $this->mongo_db->where(array('reseller'=>$this->reseller,))->where_gt('role',$this->_role)->where_gte('time_created',$date_start)->where_lte('time_created',$date_end)->get('ask_bank');
								}
							
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['reseller'])){ $v_reseller = $v['reseller'];}else{$v_reseller = null;}
										if(!empty($v['client_id'])){ $client_id = $v['client_id'];}else{$client_id = null;}
										if(!empty($v['bank_id'])){ $bank_id = $v['bank_id'];}else{$bank_id = null;}
										if(!empty($v['bank_name'])){ $bank_name = $v['bank_name'];}else{$bank_name = null;}
										if(!empty($v['bank_account'])){ $bank_account = $v['bank_account'];}else{$bank_account = null;}
										if(!empty($v['account_holders'])){ $account_holders = $v['account_holders'];}else{$account_holders = null;}
										if(!empty($v['branch_bank'])){ $branch_bank = $v['branch_bank'];}else{$branch_bank = null;}
										if(!empty($v['provinces_bank'])){ $provinces_bank = $v['provinces_bank'];}else{$provinces_bank = null;}
										if(!empty($v['date_created'])){ $date_created = $v['date_created'];}else{$date_created = null;}
										if(!empty($v['time_created'])){ $time_created = $v['time_created'];}else{$time_created = null;}
										if(!empty($v['bank_option'])){ $bank_option = $v['bank_option'];}else{$bank_option = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'client_id' => $client_id,
											'reseller'=> $v_reseller,
											'bank_id' => $bank_id,
											'bank_name' => $bank_name,
											'account_holders'=> $account_holders,
											'bank_account'=> $bank_account,
											'branch_bank'=> $branch_bank,
											'provinces_bank'=> $provinces_bank,
											'date_created'=> $date_created,
											'time_created'=> $time_created,
											'bank_option'=> $bank_option
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
	public function config_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								if($this->_role == 1 || (int)$this->_role == 2){
									$k = $this->mongo_db->get('bank_config');
								}
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['name'])){ $name = $v['name'];}else{$name = null;}
										if(!empty($v['type_bank'])){ $type_bank = $v['type_bank'];}else{$type_bank = null;}
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'name' => $name,
											'type_bank' => $type_bank
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
	
}


?>