<?php
class Global_model extends CI_Model {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>false,'result'=>null);
		
	}
	
	public function select($params,$collection){
		try{
			$result = $this->mongo_db->get_where($collection,$params);
			return $this->r = array('status'=>true,'result'=>$result);
		}catch (Exception $e) { return $this->r; }
	}
	public function select_where($params,$collection){
		try{
			$result = $this->mongo_db->get_where($collection,$params);
			return $this->r = array('status'=>true,'result'=>$result);
		}catch (Exception $e) { return $this->r; }
	}
	public function insert($params,$collection){
		try{
			$result = $this->mongo_db->insert($collection, $params);
			return $this->r = array('status'=>true,'result'=>$result);
		}catch (Exception $e) { return $this->r; }
	}
	public function update($key,$params,$collection){
		try{
			$w = array('_id'=>  new \MongoId($key));
			$result = $this->mongo_db->where($w)->set($params)->update($collection);
			return $this->r = array('status'=>true,'result'=>$result);
		}catch (Exception $e) { return $this->r; }
	}
	public function remove($key,$params,$collection){
		try{
			$result = $this->mongo_db->where(array('_id'=>  new \MongoId($key)))->delete($collection);
			return $this->r = array('status'=>true,'result'=>$result);
		}catch (Exception $e) {
			return $this->r;
		}
	}
	
/////////////////// End Noi dung ////////////

}
?>