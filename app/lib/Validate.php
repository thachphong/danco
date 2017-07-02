<?php
/**
 * エラーチェックライブラリ
*/
class Validate_lib
{
	/*
	 * メッセージを直したい場合は以下を直して下さい
	 */
	const ERR_ZENKANA = '%sは全角カナで入力してください。';
	const ERR_ZENKANASPACE = '%sは全角カナ、スペースで入力してください。';
	const ERR_HANKAKUSUUJI = '%sは半角数字で入力してください。';
	const ERR_BIGSUUJI = '%sは大きすぎる数値です。';
	const ERR_HANISUUJI = '%sは%d～%dの範囲の数値を入力してください。';
	const ERR_IJOUSUUJI = '%sは%d以上の数値を入力してください。';
	const ERR_IKASUUJI = '%sは%d以下の数値を入力してください。';
	const ERR_YYYYMMDD = '%sはYYYY/MM/DD形式の日付で入力してください。';
	const ERR_FUSEI = '%sが正しくありません。';
	const ERR_DATE1900 = '%sに1900年以前の日付は入力できません。';
	const ERR_YEAR = '%sの年が間違っています。';
	const ERR_MONTH = '%sの月が間違っています。';
	const ERR_DAY = '%sの日が間違っています。';
	const ERR_HANKAKUEISUU = '%sは半角英数字で入力してください。';
	const ERR_SEISUU = '%sの整数部は%d桁以内で入力してください。';
	const ERR_SYOUSUU = '%sの小数部は%d桁以内で入力してください。';
	const ERR_MOJIIKA = '%sは%dVui';
	const ERR_MOJIIJOU = '%sは%d文字以上で入力してください。';
	const ERR_MOMJISUU = '%sは%d文字で入力してください。';
	const ERR_MOJIIJOUMOJIIKA = '%sは%d文字以上%d文字以下で入力してください。';
	const ERR_NYUURYOKU = '%sが入力されていません。';

	/**
	 * 文字列チェック
	*/
	public function type_str($key, $name, $value, $not_null, $max=null, $min=null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}
		
		// 長さチェック
		return $this->_check_length($key, $name, $value, $max, $min);
	}

	/**
	 * 全角カナ文字列チェック
	*/
	public function zen_kana($key, $name, $value, $not_null, $max=null, $min=null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}
		
		// 長さチェック
		if ($this->_check_length($key, $name, $value, $max, $min) == false) {
			return false;
		}
		
		// カナのみチェック
		if (!preg_match("/^[ァ-ヶー]+$/u", $value)) {  
			ACWError::add($key, sprintf(self::ERR_ZENKANA, $name));
			return false;
		}
		return true;
	}

	/**
	 * 全角カナ文字列スペースありチェック
	*/
	public function zen_kana_space($key, $name, $value, $not_null, $max=null, $min=null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}
		
		// 長さチェック
		if ($this->_check_length($key, $name, $value, $max, $min) == false) {
			return false;
		}
		
		// カナチェック
		if (!preg_match("/^[ァ-ヶー　 ]+$/u", $value)) {  
			ACWError::add($key, sprintf(self::ERR_ZENKANASPACE, $name));
			return false;
		}
		return true;
	}

	/**
	 * 数値チェック 最大値　最小値制限
	*/
	public function type_int($key, $name, $value, $not_null, $max=null, $min=null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}

		if (!preg_match('/^\d+$/', $value)) {
			ACWError::add($key, sprintf(self::ERR_HANKAKUSUUJI, $name));
			return false;
		}
		
		if (settype($value, 'integer') == false) {
			ACWError::add($key, sprintf(self::ERR_BIGSUUJI, $name));
			return false;
		}
		
		if (is_null($min) == false && is_null($max) == false) {
			if ($min > $value || $value > $max) {
				ACWError::add($key, sprintf(self::ERR_HANISUUJI, $name, $min, $max));
				return false;
			}
		}

		if (is_null($min) == false) {
			if ($min > $value) {
				ACWError::add($key, sprintf(self::ERR_IJOUSUUJI, $name, $min));
				return false;
			}
		}

		if (is_null($max) == false) {
			if ($value > $max) {
				ACWError::add($key, sprintf(self::ERR_IKASUUJI, $name, $max));
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * 日付チェック YYYY/MM/DD
	*/
	public function type_date($key, $name, $value, $not_null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}

		if (!preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $value)) {
			ACWError::add($key, sprintf(self::ERR_YYYYMMDD, $name));
			return false;
		}
		
		$numArray = explode("/", $value);
		if (checkdate($numArray[1], $numArray[2], $numArray[0]) == false || $numArray[0] > 2100) {
			ACWError::add($key, sprintf(self::ERR_FUSEI, $name));
			return false;
		}

		if ($numArray[0] < 1900) {
			ACWError::add($key, sprintf(self::ERR_DATE1900, $name));
			return false;
		}
		
		return true;
	}

	/**
	 * 日付チェック YYYY MM DD バラで
	*/
	public function type_date_3($key, $name, $valueY, $valueM, $valueD, $not_null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $valueY, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}
		switch ($this->_check_null($key, $name, $valueM, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}
		switch ($this->_check_null($key, $name, $valueD, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}

		if (!preg_match('/^\d{4}$/', $valueY)) {
			ACWError::add($key, sprintf(self::ERR_YEAR, $name));
			return false;
		}
		if (!preg_match('/^\d{1,2}$/', $valueM)) {
			ACWError::add($key, sprintf(self::ERR_MONTH, $name));
			return false;
		}
		if (!preg_match('/^\d{1,2}$/', $valueD)) {
			ACWError::add($key, sprintf(self::ERR_DAY, $name));
			return false;
		}
		
		if (checkdate($valueM, $valueD, $valueY) == false || $valueY > 2100) {
			ACWError::add($key, sprintf(self::ERR_FUSEI, $name));
			return false;
		}

		if ($valueY < 1900) {
			ACWError::add($key, sprintf(self::ERR_DATE1900, $name));
			return false;
		}
		
		return true;
	}
	
	/**
	 * 数値または文字列
	 */
	public function alpha_number($key, $name, $value, $not_null, $max=null, $min=null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}

		if (!preg_match("/^[a-zA-Z0-9]+$/", $value)) {
			ACWError::add($key, sprintf(self::ERR_HANKAKUEISUU, $name));
			return false;
		}
			
		// 長さチェック
		return $this->_check_length($key, $name, $value, $max, $min);
	}

	/**
	 * 小数点あり数値
	 */
	public function type_float($key, $name, $value, $not_null, $intNum, $decNum) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}

		if (!preg_match("/^(\d+)(\.\d+)?$/", $value, $match)) {
			ACWError::add($key, sprintf(self::ERR_HANKAKUSUUJI, $name));
			return false;
		}

		if ($intNum < strlen($match[1])) {
			ACWError::add($key, sprintf(self::ERR_SEISUU, $name, $intNum));
			return false;
		}
		if ($decNum < strlen($match[2])) {
			ACWError::add($key, sprintf(self::ERR_SYOUSUU, $name, $decNum));
			return false;
		}			
		
		return true;
	}

	/**
	 * 数字のみ　桁数チェック
	 */
	public function type_number($key, $name, $value, $not_null, $max=null, $min=null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}

		if (!preg_match("/^\d+$/", $value)) {
			ACWError::add($key, sprintf(self::ERR_HANKAKUSUUJI, $name));
			return false;
		}
			
		// 長さチェック
		return $this->_check_length($key, $name, $value, $max, $min);
	}

	/**
	 * 電話番号
	 */
	public function tel($key, $name, $value, $not_null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}

		if (!preg_match("/^0\d{9,10}$/ ", $value)) {
			ACWError::add($key, sprintf(self::ERR_FUSEI, $name));
			return false;
		}
			
		return true;
	}

	/**
	 * 郵便番号
	 */
	public function post_code($key, $name, $value, $not_null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}

		if (!preg_match("/^\d{7}$/", $value)) {
			ACWError::add($key, sprintf(self::ERR_FUSEI, $name));
			return false;
		}

		return true;
	}

	/**
	 * メール
	 */
	public function mail($key, $name, $value, $not_null, $max=null, $min=null) {
		// NULLチェック
		switch ($this->_check_null($key, $name, $value, $not_null)) {
			case -1:
				return false;	// NULLエラー
			case 1:
				return true;	// NULLなのでチェックしない
		}

//		if (!preg_match("/^[^@]+@[^.]+\..+/", $value)) {
		if (!preg_match("/^[a-zA-Z0-9]+[a-zA-Z0-9\._-]*@[a-zA-Z0-9_-]+\.[a-zA-Z0-9\._-]+$/", $value)) {
			ACWError::add($key, sprintf(self::ERR_FUSEI, $name));
			return false;
		}
			
		// 長さチェック
		return $this->_check_length($key, $name, $value, $max, $min);
	}

	// プロバイダメール
	/*
	public function provider_mail($key, $name, $value) {
		$freempb = array(
			// フリー
			'nifmail.jp',
			'excite.co.jp',
			'gmail.com',
			'mail.goo.ne.jp',
			'infoseek.jp',
			'livedoor.com',
			'hotmail.co.jp',
			'hotmail.com',
			'yahoo.co.jp',
			'yahoo.com',
			// 携帯
			'docomo.ne.jp',
			'ezweb.ne.jp',
			'softbank.ne.jp',
			'c.vodafone.ne.jp',
			'd.vodafone.ne.jp',
			'h.vodafone.ne.jp',
			'i.softbank.ne.jp',
			'k.vodafone.ne.jp',
			'n.vodafone.ne.jp',
			'r.vodafone.ne.jp',
			's.vodafone.ne.jp',
			't.vodafone.ne.jp',
			'q.vodafone.ne.jp',
			// 追加
			'x.vodafone.ne.jp',
			'disney.ne.jp',
			'i.softbank.jp',
			'pdx.ne.jp',
			'willcom.com',
			'emnet.ne.jp'
		);
		
		$mpb = explode('@', $value);

		foreach ($freempb as $fmpb) {
			if ($fmpb == $mpb[1]) {
				ACWError::add($key, $name, "にフリーメールまたは携帯メールのアドレスは使用できません。");
				return false;
			}
		}
		return true;
	}*/

	// 長さをチェック
    public function _check_length($key, $name, $value, $max, $min)
    {
		$error_flag = false;
		
		// 改行を1文字として数える
		//$vstr = str_replace("\r\n", "\n", $value);
		$vstr = $value;
		$vlen = mb_strlen($vstr, 'UTF-8');	// utf-8専用
		
		if (!is_null($min) && $min > $vlen) {
			$error_flag = true;
		}
		if (!is_null($max) && $max < $vlen) {
			$error_flag = true;
		}
		
		if ($error_flag == true) {
			if (is_null($min)) {
				ACWError::add($key, sprintf(self::ERR_MOJIIKA, $name, $max));
			} else if (is_null($max)) {
				ACWError::add($key, sprintf(self::ERR_MOJIIJOU, $name, $min));
			} else if ($max == $min) {
				ACWError::add($key, sprintf(self::ERR_MOMJISUU, $name, $max));
			} else {
				ACWError::add($key, sprintf(self::ERR_MOJIIJOUMOJIIKA, $name, $min, $max));
			}
			return false;
		}
		
		return true;
    }
	
	/**
	 * 文字列の前後の空白を削除
	 * のちにタブなどを削除する場合のため、共通で作成
	 */
	public static function trim_ext($str)
	{
		// 前の半角、全角スペースを削除
		$rep_str = preg_replace('/^[ 　]+/u', '', $str);
		// 後の半角、全角スペースを削除
		$rep_str = preg_replace('/[ 　]+$/u', '', $rep_str);
		// タブなどを削除
		return trim($rep_str);
	}

	/////////////////////////////////////////////////////////////////////////
	// 内部関数
	/////////////////////////////////////////////////////////////////////////
	
	// NULLかどうかの判定
	protected function _is_null($value) {
		if (is_null($value)) {
			return true;
		}
	
		if (!is_string($value)) {
			settype($value, "string");
		}
	
		if ($value == '') {
			return true;
		}
	
		return false;
	}
	
	public function _check_null($key, $name, $value, $not_null) {
		if ($not_null === true) {
			if ($this->_is_null($value) == true) {
				ACWError::add($key, sprintf(self::ERR_NYUURYOKU, $name));
				return -1;
			}
		} else if ($this->_is_null($value) == true) {
			// NULL可の時にNULLなので処理を継続しない
			return 1;
		}
		return 0;	// 処理継続
	}
        
        
        //Add Start - Trung VNIT - 2014/08/20
        public function check_max_lenght_item_string($limit_category, $limit_name, $value, $key = 'key_limit', $limit_detail = '最大') {
            $limit = new Limit_common_model();
            $limit->select_category($limit_category);
            $key_value = $this->trim_ext($value);
            if ($this->type_str($key, $limit_name, $key_value, true, $limit->get($limit_name.'_'.$limit_detail)) == false) {
                return FALSE;
            }
        }
        //Add End - Trung VNIT - 2014/08/20
    
}
/* ファイルの終わり */