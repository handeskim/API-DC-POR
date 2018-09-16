<?php 
class MY_Cart extends CI_Cart { 
	public function MY_Cart() { 
			parent::__construct(); $this->product_name_rules = '\d\D'; 
	} 
	} 
?>