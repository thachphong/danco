<?php
require_once ACW_APP_DIR . '/vendor/PHPMailer/class.phpmailer.php';
class Mail_lib 
{	
	private $mail;
	public function __construct($info = null) {	    
	    $this->mail = new PHPMailer();
        $this->mail->IsSMTP(); // set mailer to use SMTP 
        $this->mail->Host = "smtp.gmail.com"; // specify main and backup server 
        $this->mail->Port = 465; // set the port to use 
        $this->mail->SMTPAuth = true; // turn on SMTP authentication 
		$this->mail->CharSet = "utf-8";	
        $this->mail->SMTPSecure = 'ssl'; 
        $this->mail->WordWrap = 50; // set word wrap 
        $this->mail->IsHTML(true); // send as HTML 
		$this->mail->Username = 'laptrinhphanmem2015@gmail.com';		
		//$this->mail->Username = "hoanghoangnguyen788@gmail.com";
		$this->mail->Password ='thietkeweb2s.com';
		//$this->mail->Password  = "221292682";          

        $this->mail->From = PHPMAIL_EMAIL_REPLYTO;

        $this->mail->FromName = PHPMAIL_NAME_REPLYTO;                 

        $this->mail->AddReplyTo(PHPMAIL_EMAIL_REPLYTO,PHPMAIL_NAME_REPLYTO); 
	}
	public function setSubject($subject){
		 $this->mail->Subject = $subject; 
	}
	public function setBody($body){
		$this->mail->Body = $body;
	}
	public function AddMailTo($address,$name=''){
		if($name == ''){
			$name = $address;
		}
		$this->mail->AddAddress($address,$name); 
	}
	public function send(){
		return $this->mail->Send();
	}
}
