<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Core.php';
class Hello extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>100,'result'=>null);
		$this->obj = array();
		$this->apps = new core;
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_is_private_key = $this->apps->_is_private_key($this->_api_key());
		
	}
	public function index_get(){
		$this->r['level'] = $this->_level;
		$this->r['service'] = base_url('card');
		$this->r['api_name'] = $this->apps->_api_name();
		$this->response($this->r);
	}
	
	
}


?>