<?php

class Core_model extends CI_Model{
		function __construct(){
			parent::__construct();
				$this->load->driver('cache');
	}
	public function query_global($sql){
     $query = $this->db->query($sql);
          return $query->result_array();
	}
	
/////////////////// End Noi dung ////////////

}
?>