<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH .'/libraries/Core.php';
class Documents extends REST_Controller {
	protected $rest_format = 'xml';
   
	function __construct(){
		parent::__construct();
		$this->apps = new core;
		$this->provide_service = $this->config->item('provide_service');
		$this->api_name = 'API-DOICARD-V1';
		$this->_level = $this->apps->_level_api($this->_api_key());
		$this->r = array('status'=>200,'result'=>null);
		$this->obj = array( );
	}
	public function index_get(){
		$this->r['PHP_DEMO'] = base_url('example.zip');
		$this->r['SERVER _ API'] = base_url('card');
		$this->r['description'] = 'đặc tả kết nối hệ thống API';
		$this->r['hello'] = $this->doc_hello();
		$this->r['token'] = $this->doc_token();
		$this->r['card'] = $this->doc_card();
		$this->r['token_check'] = $this->doc_token_check();
		$this->r['balancer'] = $this->doc_balancer();
		$this->r['shop_card'] = $this->doc_shop_card();
		
		$this->r['status_message'] = $this->doc_status_msg();
		
		$this->response($this->r);
	}
	private function doc_card(){
		$doc_card  = array(
			'url' => base_url('card').'?'.$this->api_name .'={merchant_id}',
			
			'param' => array(
				'token' => 'Token đã lấy được từ việc lấy token', 
				'card_seri' => '(int) Số seri ', 
				'card_code' => '(int) Mã thẻ  ', 
				'card_type' => '(int) Kiểu mã (1,2,3 lấy ở type card cho phép) hoạt động', 
				'card_amount' => '(int) Số tiền của thẻ ', 
			),
			'response' => array(
				'doctype'=>'Trả về Json',
				'status' => 'dữ liệu status trả về', 
				'result' => 'nếu tồn tại giá trị thẻ đúng, hệ thống tự xử lý thẻ và đưa bạn và nạp tiền cho bạn vào tài khoản nếu thẻ đúng', 
			),
			'example_response' => 'object(stdClass)#31 (4) { ["status"]=> int(200) ["msg"]=> string(24) "kết nối thành công" ["result"]=> object(stdClass)#32 (2) { ["status"]=> int(4023) ["msg"]=> string(31) "Thẻ đã được sử dụng" } ["transaction_id"]=> string(24) "5b91626158e693cb128b4645" }',
			'description_token' => array(
				'bạn có thể tham khảo lấy token tại', 
				'document_token' => $this->doc_token()),
		);
		return $doc_card;
	}
	private function doc_hello(){
		$doc  = array(
			'url' => base_url('').'hello?'.$this->api_name .'={merchant_id}',
			'description' => 'để kiểm tra server API và kết nối có thành công hay không',
			'response' => array(
				'doctype'=>'Trả về Json',
			),
		);
		return $doc;
	}
	private function doc_token(){
		$doc  = array(
			'url' => base_url('card').'/token?'.$this->api_name .'={merchant_id}',
			'param' => array(
				'username' => 'Tài khoản', 
				'password' => 'Mật khẩu cấp 1', 
			),
			'response' => array(
				'doctype'=>'Trả về Json',
				'status' => 'dữ liệu status trả về', 
				'msg' => 'dữ liệu  msg trả về', 
				'result' => 'mã hóa dữ liệu dùng hàm giải mã dữ liệu có Secret key  để dọc data có mảng token = (string)', 
			),
			'example_response' => 'object(stdClass)#31 (4) {
				["status"]=>
					int(1000)
					["msg"]=>
					string(12) "thành công"
					["result"]=>
					string(460) "eU52T2d0WGR0QW9T......3dTY3B6eWV5QkRVRUh2OXVlNGJ"
					["transaction_id"]=>
					string(24) "5b915e4375058b463b"
				}
				/// sau khi giải mã dữ liệu ta có đoạn token sau để thực hiện thao tác với hệ thống.. 
				string(248) "{"token":"NFJEQ1pMSVF......oMTYyeTJoaXhjPQ%3D%3D"}"',
		);
		return $doc;
	}
	private function doc_token_check(){
		$doc  = array(
			'url' => base_url('card').'/token/check?'.$this->api_name .'={merchant_id}',
			'description' => array(
				'bạn có thể tham khảo lấy token tại', 
				'document_token' => $this->doc_token()),
			'param' => array(
				'token' => 'Token đã lấy được từ việc lấy token', 
			),
			'response' => array(
				'doctype'=>'Trả về Json',
				'status' => 'dữ liệu status trả về', 
				'token' => 'true/false', 
			),
			'example_response' => 'object(stdClass)#33 (4) { ["status"]=> int(200) ["msg"]=> object(stdClass)#34 (2) { ["status"]=> int(1000) ["msg"]=> string(12) "thành công" } ["token"]=> bool(true) ["transaction_id"]=> string(24) "5b9160a0b688b4687" }',
		);
		return $doc;
	}
	private function doc_balancer(){
		$doc  = array(
			'url' => base_url('card').'/balancer?'.$this->api_name .'={merchant_id}',
			
			'param' => array(
				'token' => 'Token Lấy từ hàm Token ', 
			),
			'response' => array(
				'doctype'=>'Trả về Json',
				'status' => 'dữ liệu status trả về', 
				'msg' => 'dữ liệu  msg trả về', 
				'blancer' => 'dữ liệu  blancer trả về', 
			),
			'example_response' => 'object(stdClass)#31 (4) { ["status"]=> object(stdClass)#32 (2) { ["status"]=> int(1000) ["msg"]=> string(12) "thành công" } ["msg"]=> string(24) "kết nối thành công" ["balancer"]=> int(54830) ["transaction_id"]=> string(24) "5b91615f53a8b4585" }',
			'description' => array(
				'bạn có thể tham khảo lấy token tại', 
				'document_token' => $this->doc_token()),
		);
		return $doc;
	}
	private function doc_shop_card(){
		$k = $this->mongo_db->where(array('types'=>'DT'))->order_by(array('type'=>'ASC'))->get('Telco');
		$r = array();
		foreach($k as $v){
			$r[] = array(
				'keys' => getObjectId($v['_id']),
				// 'telco' => $v['telco'],
				'type' => $v['type'],
				'name' => $v['name'],
				// 'types' => $v['types'],
				'discount' => $v['deduct'],
			);
		}
		return $r;
	}
	private function doc_status_msg(){
		$k = $this->mongo_db->get('msg_reponse');
		$r = array();
		foreach($k as $v){
			$r[] = array(
				'status' => $v['code'],
				'msg' => $v['msg'],
			);
		}
		return $r;
	}
	
	
	
}

?>

