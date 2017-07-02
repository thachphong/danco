<?php
class Mailer
{
	public $ext = 'txt';	// メールテンプレートの拡張子

	protected $mailTo;
	protected $mailCC;
	protected $mailBCC;
	protected $headerString;
	protected $subjectString;
	protected $pre_bodyString;
	protected $bodyString;
	protected $template;

	// コンストラクタ
	public function Mailer($template, $mailTo, $mailFrom, $subject, $fromName=null) {
		$this->template = $template;
		$this->mailTo = $mailTo;
		$this->mailFrom = $mailFrom;
		$this->subjectString = $subject;
		$this->mailFromName = $fromName;
	}

	// Smartyテンプレート名
	public function &getTemplate() {
		return $this->template;
	}
	
	// 拡張用ヘッダ作成
	public function makeExtHeader($CCParam, $BCCParam=null) {
		$this->mailCC = $CCParam;
		$this->mailBCC = $BCCParam;
	}

	// 文字列によるメール送信
	public function output($mailBody) {
		// デバッグのため一度格納する
		$this->bodyString = &$mailBody;
		$this->headerString = $this->getHeader();
//		mb_send_mail($this->mailTo, $this->subjectString, $this->bodyString, $this->headerString);
// 2008.11.27 エラーメールがfromに返るようにする
		@mb_send_mail($this->mailTo, $this->subjectString, $this->bodyString, $this->headerString, '-f' . $this->mailFrom);
	}

	// ヘッダー情報セット
	protected function &getHeader(){
		if (is_null($this->mailFromName) == false) {
			// エンコード
			$encodeFromName = mb_encode_mimeheader($this->mailFromName);
		}
		
		$headerString = "From: $encodeFromName <" . $this->mailFrom .  ">\r\n";
		if (is_null($this->mailCC) == false) {
			$headerString .= "Cc: " . mb_encode_mimeheader($this->mailCC) . "\r\n";
		}
		if (is_null($this->mailBCC) == false) {
			$headerString .= "Bcc: " . mb_encode_mimeheader($this->mailBCC) . "\r\n";
		}
		
		return $headerString;
	}
}
?>
