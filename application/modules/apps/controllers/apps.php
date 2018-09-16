<?php
require APPPATH . '/libraries/Core.php';
class Apps extends MY_Controller{
	private $auth;
	function __construct(){
		parent::__construct();
		$this->apps = new Core;
		$this->private_key = $this->config->item('encryption_key');
		$this->r = array('status'=>false,'connect'=>200,'result'=>null);
		$this->obj = array();
		$this->url = '';
		
	}
	
	
	
}


?>