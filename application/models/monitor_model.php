<?php

class Monitor_model extends CI_Model{
		function __construct(){
			parent::__construct();
				
	}
	public function update_email_sender($params){
		try{
			$response =$this->mongo_db->where(array('_id'=>  new \MongoId($params['id_update'])))->set($params)->update('ureg_email_monitor');
				return $response;
		}catch (Exception $e) {
			return false;
		}
	}
	public function data_email_sender(){
		try{
			$response =	$this->mongo_db->where(array('status'=>1,))->get('ureg_email_monitor');
			if(!empty($response)){
				return $response;
			}else{ return false;}
		}catch (Exception $e) {
			return false;
		}
	}
	
/////////////////// End Noi dung ////////////

}
?>