<?php


class Mail_lib extends Phpmailer_lib
{	
	// Construct function
	public function __construct($info = null) {
	    //Tell PHPMailer to use SMTP
	    /*$this->isSMTP();
	    
	    //Enable SMTP debugging
	    // 0 = off (for production use)
	    // 1 = client messages
	    // 2 = client and server messages
	    $this->SMTPDebug = 0;
	    //Whether to use SMTP authentication
	    $this->SMTPAuth = true;
	    $this->setFrom(AKAGANE_MAIL_ADMIN);
        $this->SMTPSecure="ssl"; // add phong
	    if(empty($info) == true) {
	        // default account
	        $this->Host = AKAGANE_MAIL_HOST;
	        $this->Username = AKAGANE_MAIL_USERNAME;
	        $this->Password = AKAGANE_MAIL_PASS;
	        $this->Port = AKAGANE_MAIL_PORT;
	    } else {      
	        // set up new account
	        if(isset($info['host']) && isset($info['username']) && isset($info['pass']) 
	            && !empty($info['host']) && !empty($info['username']) && !empty($info['pass'])) {
	                $this->Host = $info['host'];
	                $this->Username = $info['username'];
	                $this->Password = $info['pass'];
	                if(isset($info['port'])) {
	                    $this->Port = $info['port'];
	                }
	        }
	    }*/
	    
        $this->IsSMTP(); // set mailer to use SMTP 
        $this->SMTPDebug = 0;
        $this->Host = "smtp.gmail.com"; // specify main and backup server 
        $this->Port = 465; // set the port to use 
        $this->SMTPAuth = true; // turn on SMTP authentication 
		//$this->CharSet = "utf-8";	
        $this->SMTPSecure = 'ssl'; 
        //$this->WordWrap = 50; // set word wrap 
        //$this->IsHTML(true); // send as HTML 
		$this->Username = 'laptrinhphanmem2015@gmail.com';		
		//$this->mail->Username = "hoanghoangnguyen788@gmail.com";
		$this->Password ='thietkeweb2s.com';
		//$this->mail->Password  = "221292682";          

        $this->From = PHPMAIL_EMAIL_REPLYTO;

        $this->FromName = PHPMAIL_NAME_REPLYTO;                 

        $this->AddReplyTo(PHPMAIL_EMAIL_REPLYTO,PHPMAIL_NAME_REPLYTO); 
	}

	public function loadBody($file) {
	    $base_url = ACW_ROOT_DIR . "/app/template/";
	    $file = $base_url . $file;
	    $this->msgHTML(file_get_contents($file), $base_url);
	}
	
	public function AddListAddress($list_mail) {
	    if(count($list_mail) > 0) {
	        $mail_arr = array();
	        foreach ($list_mail as $item) {
	            if($this->checkReplaceEmail($item['mail_address'], $mail_arr) == true) {
	                array_push($mail_arr, $item['mail_address']);
	                $this->addAddress($item['mail_address']);
	            }
	        }#end foreach
	    }
	}
	
	public function checkReplaceEmail($check, $maillist) {
	    if(count($maillist) > 0) {
	        foreach ($maillist as $item) {
	            if($item == $check) {
	                return false;
	            }
	        }
	    }
	    return true;
	}
	public function replaceBody($replacements) {
	    foreach($replacements as $find => $replace)
	    {
	        $this->Body = preg_replace('/\[\[' . preg_quote($find, '/') . '\]\]/', $replace, $this->Body);
	    }
	}
}
