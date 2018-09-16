<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Bank extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->param = array();
		$this->obj = array();
		$this->apps = new core;
	}
	
	public function index_get(){
		if(!empty($_GET['param'])){
			$p = $this->apps->_params($_GET['param'],$this->_api_key());
			$this->obj = $this->mongo_db->where(array('type_bank'=>$p->type_bank))->get('bank_config');
		}else{
				$this->obj = $this->mongo_db->select('name')->get('bank_config');
		}
		foreach($this->obj as $v){
			$this->param[] = array(
				'bank_id' => getObjectId($v['_id']),
				'name' => $v['name'],
				'type_bank' => $v['type_bank'],
				);
		}
		$this->r = array($this->apps->_msg_response(1000),'bank_option'=>$this->param);
		$this->response($this->r);
	}
	public function option_get(){
		$this->obj = $this->mongo_db->select('name')->get('bank_option');
		if(!empty($this->obj)){
			foreach($this->obj as $v){
				$this->param[] = array(
					'type_bank' => getObjectId($v['_id']),
					'name' => $v['name'],
					);
			}
		}
		$this->r = $this->param;
		$this->response($this->r);
	}
	
}


?>