<?php 
class Test extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->obj_core = array();
	}
	
	public function index(){
		$string = '5b81733e798eef33a03c9440';
		$private_key = $this->config->item('encryption_key');
		echo encrypt_obj($string,$private_key);
		// var_dump($private_key);
		
	}
}

?>