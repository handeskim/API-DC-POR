<?php
class Run extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->library('rest');
		$this->load->model('global_model', 'GlobalMD');	
		
	}
	
	public function load(){
		
		$this->country();
		$this->vat_percent();
		
	}
	public function remove(){
		
		$this->del_cached_country();
		$this->del_cached_vat_percent();
	}
	
	///////////////////////////////////////

	private function del_cached_country(){
		$key_cached = md5('key_coutry_data');
		$delCached =  $this->redis->del($key_cached);
		if($delCached==true){
			echo "Del cached data country successfully! This Del Key = ".$key_cached;
			$this->country();	
		}
	}
	private function country(){
		$result = $this->GlobalMD->country();
		$dataJson = json_encode($result);
		$key_cached = md5('key_coutry_data');
		$setCached = $this->redis->set($key_cached,$dataJson);
		if($setCached==true){
			echo "Save cached data country successfully! This Key = ".$key_cached;
		}
	}
	
	private function del_cached_vat_percent(){
		$key_cached = md5('key_vat_percent_data');
		$delCached =  $this->redis->del($key_cached);
		if($delCached==true){
			echo "Del cached data country successfully! This Del Key = ".$key_cached;
			$this->vat_percent();	
		}
	}
	private function vat_percent(){
		$result = $this->GlobalMD->vat_percent();
		$dataJson = json_encode($result);
		$key_cached = md5('key_vat_percent_data');
		$setCached = $this->redis->set($key_cached,$dataJson);
		if($setCached==true){
			echo "Save cached data vat_percent successfully! This Key = ".$key_cached;
		}
	}
	
	
}
?>