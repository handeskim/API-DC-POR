<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Code extends REST_Controller {
	protected $rest_format = 'xml';
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->param = array();
		$this->obj = array();
		$this->apps = new core;
	}
	public function index_get(){
		$obj = $this->mongo_db->get('msg_reponse');
		foreach($obj as $v){
			$this->param[] = array(
				'code' => $v['code'],
				'msg' => $v['msg'],
				);
		}
		$this->r = array($this->apps->_msg_response(1000),'code'=>$this->param);
		$this->response($this->r);
	}
	
	
}


?>