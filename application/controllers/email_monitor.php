<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Email_monitor extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('monitor_model', 'MonitorMD');	
		$this->load->library('email');
	}
	public function crond(){
		
		
		
	 //***************************//
	   // Chạy Email Gửi mail 		//
		$this->sender_email();
     //************************** //
	}
	
	public function sender_email(){
		$result = $this->MonitorMD->data_email_sender();
		if(!empty($result)){
			foreach($result as $value){
				$_id = $value['_id']->{'$id'};
				$params = array(
					'title' => $value['subject'],
					'body' => $value['body'],
					'email_to' => $value['email_to'],
					'email_from' => $value['email_from'],
				);
				$sender = $this->EmailSender($params);
				
				if($sender==true){
					$params = array(
						'id_update'=> $_id,
						'status'=> 2,
						'message'=>  'send oke',
						'date_send'=>  date("Y-m-d H:i:s",time()),
					);
					$this->MonitorMD->update_email_sender($params);
				}
			}
		}	
	
	}
	
	private function EmailSender($params){
		$subject = $params['title'];
		$body =  $params['body'];
		$email_to = $params['email_to'];
		$email_from = $params['email_from'];
		try{
			$result = $this->email->from('uidfacebookconver@gmail.com')->to($email_to)->subject($subject)->message($body)->send();
			$params_log = array(
				'time_send'=> date("Y-m-d h:i:s",time()),
				'from'=> $email_from,
				'email_to'=> $email_to,
				'body'=> $body,
				'subject'=> $subject,
				'logs' => $result,
			);
			$this->mongo_db->insert('logs_system_sendmail',$params_log);
			return $result;
		}catch (Exception $e) {
			$params_log = array(
				'time_send'=> date("Y-m-d h:i:s",time()),
				'from'=> $email_from,
				'email_to'=> $email_to,
				'body'=> $body,
				'subject'=> $subject,
				'logs' => false,
			);
			$this->mongo_db->insert('logs_system_sendmail',$params_log);
		}
		
		
	}
}
?>