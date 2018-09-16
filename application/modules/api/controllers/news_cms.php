<?php
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class News_cms extends REST_Controller {
	function __construct(){
		parent::__construct();
		$this->r = array('status'=>true,'result'=>null);
		$this->param = array();
		$this->result = array();
		$this->objects = array();
		$this->params = array();
		$this->reseller = null;
		$this->confim = array();
		$this->obj = array();
		$this->_param = array();
		$this->apps = new core;
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->_role = $this->apps->_role($this->_api_key());
		$this->r = $this->apps->_msg_response(200);
		$this->_api_key = $this->_api_key();
		$this->_is_private_key = $this->apps->_is_private_key($this->_api_key());	
	
	}
	public function del_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$del = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->delete('news');
								$this->r = $this->apps->_msg_response(1000);
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}	
	public function info_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								if(!empty($p->keys)){
									$k = array();
									if((int)$this->_role == 1 || (int)$this->_role == 2){
										$k = $this->mongo_db->where(array('_id' => new \MongoId($p->keys)))->get('news');;
									}
									if(!empty($k)){
										foreach($k as $v){ }
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['alias'])){ $alias = $v['alias'];}else{$alias = null;}
										if(!empty($v['title'])){ $title = $v['title'];}else{$title = null;}
										if(!empty($v['categories'])){ $categories = $v['categories'];}else{$categories = null;}
										if(!empty($v['images'])){ $images = $v['images'];}else{$images = null;}
										if(!empty($v['keywords'])){ $keywords = $v['keywords'];}else{$keywords = null;}
										if(!empty($v['description'])){ $description = $v['description'];}else{$description = null;}
										if(!empty($v['title_seo'])){ $title_seo = $v['title_seo'];}else{$title_seo = null;}
										if(!empty($v['description_seo'])){ $description_seo = $v['description_seo'];}else{$description_seo = null;}
										if(!empty($v['contents'])){ $contents = $v['contents'];}else{$contents = null;}
										if(!empty($v['time_create'])){ $time_create = $v['time_create'];}else{$time_create = null;}
										$this->obj = array(
											'id' => getObjectId($v['_id']),
											'alias' => $alias,
											'title' => $title,
											'categories' => $categories,
											'images' => $images,
											'keywords' => $keywords,
											'description' => $description,
											'title_seo' => $title_seo,
											'description_seo' => $description_seo,
											'contents' => $contents,
											'time_create' => $time_create,
											'date_create' => $date_create,
										);
									
									}
									$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->obj);
								}
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	
	public function index_get(){
		if(!empty($this->_level)){
			if(!empty($this->_role)){
				if((int)$this->_level == 2 || (int)$this->_level == 3){
					if((int)$this->_role == 1 || (int)$this->_role == 2 || (int)$this->_role == 3){
						if(!empty($_GET['param'])){
							$p = $this->apps->_params($_GET['param'],$this->_api_key);
							if(!empty($p->token)){
								$this->reseller = $this->apps->_token_reseller($p->token);
								if(!empty($p->date_start)){$dstart = date("Y-m-d ",strtotime($p->date_start)).'00:00:00';}else{$dstart = date("Y-m-d ",time()).'00:00:00';}
								if(!empty($p->date_end)){$dend = date("Y-m-d ",strtotime($p->date_end)).'23:59:59';}else{$dend = date("Y-m-d ",time()).'23:59:59';}
								$date_start = strtotime($dstart);
								$date_end =  strtotime($dend);
								if($this->_role == 1 || (int)$this->_role == 2){
									$k = $this->mongo_db->where_gte('time_create',$date_start)->where_lte('time_create',$date_end)->get('news');
								}else{ }
								if(!empty($k)){
									foreach($k as $v){
										if(!empty($v['date_create'])){ $date_create = $v['date_create'];}else{$date_create = null;}
										if(!empty($v['alias'])){ $alias = $v['alias'];}else{$alias = null;}
										if(!empty($v['title'])){ $title = $v['title'];}else{$title = null;}
										if(!empty($v['categories'])){ $categories = $v['categories'];}else{$categories = null;}
										if(!empty($v['images'])){ $images = $v['images'];}else{$images = null;}
										if(!empty($v['keywords'])){ $keywords = $v['keywords'];}else{$keywords = null;}
										if(!empty($v['description'])){ $description = $v['description'];}else{$description = null;}
										if(!empty($v['title_seo'])){ $title_seo = $v['title_seo'];}else{$title_seo = null;}
										if(!empty($v['description_seo'])){ $description_seo = $v['description_seo'];}else{$description_seo = null;}
										if(!empty($v['contents'])){ $contents = $v['contents'];}else{$contents = null;}
										if(!empty($v['time_create'])){ $time_create = $v['time_create'];}else{$time_create = null;}
								
										$this->result[] = array(
											'id'=> getObjectId($v['_id']),
											'alias' => $alias,
											'title' => $title,
											'categories' => $categories,
											'images' => $images,
											'keywords' => $keywords,
											'description' => $description,
											'title_seo' => $title_seo,
											'description_seo' => $description_seo,
											'contents' => $contents,
											'time_create' => $time_create,
											'date_create' => $date_create,
										);
									}
								}
								$this->r = array( 'status'=> $this->apps->_msg_response(1000), 'result'=> $this->result);
							}else{ $this->r = $this->apps->_msg_response(2011);}
						}else{ $this->r = $this->apps->_msg_response(2000);}
					}else{ $this->r = $this->apps->_msg_response(1002);}
				}else{ $this->r = $this->apps->_msg_response(1001);}
			}else{ $this->r = $this->apps->_msg_response(1002);}
		}else{ $this->r = $this->apps->_msg_response(1001);}
		$this->response($this->r);
	}
	
}


?>