<?php
/**
 * 既定のView
 *
 * 変更してはいけません。継承したら解決しませんか？
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
 */
class ACWView
{
	const OK = "ViewOK";

	/*
	 * Smarty変数初期値
	 */
	protected static $smarty_var = array();

    /**
     * Smartyを作る Smartyを拡張したい場合は継承する
     */
	protected static function create_smarty($template_path, $param, $escape)
    {
		// 一回目だけSmartyインスタンスを作る
		$smarty = new Smarty();
		// crisework
		$smarty->left_delimiter = ACWCore::get_config('smarty_left_delimiter');
		$smarty->right_delimiter = ACWCore::get_config('smarty_right_delimiter');
		// 保留 $smarty->force_compile = CRWK_SMARTY_FORCE_COMPILE;
		$smarty->template_dir = ACW_TEMPLATE_DIR;
		$smarty->compile_dir = ACW_TEMPLATE_CACHE_DIR;
		//$smarty->plugins_dir = array('plugins', CRWK_CRISEWORK2 . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'plugins');
		$smarty->plugins_dir = array(ACW_SYSTEM_DIR . '/smarty/plugins', ACW_SMARTY_PLUGIN_DIR);
		$smarty->error_reporting = E_ALL;
		
		// キャッシュを消すとエラーが出るので抑制
		$smarty->muteExpectedErrors();
		// ここから毎回変更される設定
		if ($smarty->templateExists($template_path) == false) {
			// テンプレートが存在しない
			throw new Exception('Template "' . $template_path . '" is not exists.');
			exit;
		}

		// エスケープ設定
		$smarty->escape_html = $escape;
		// 階層が異なると同一名でエラーが出るためコンパイルIDを付ける
		// いまのところ不必要 $smarty->compile_id = md5($template_path);

		// クラスのオブジェクトを獲得
		foreach (static::$smarty_var as $key => $value) {
			$smarty->assign($key, $value);
		}
		$define_val =get_define_all();
		foreach ($define_val as $key => $value) {
			$smarty->assign($key, $value);
		}
		// パラメタ設定
		if (is_null($param) == false) {
			// クラスのオブジェクトを獲得
			foreach ($param as $key => $value) {
				$smarty->assign($key, $value);
			}
		}

		// 必ずerrorを入れる
		$smarty->assign('acw_error', ACWError::get_all());
		// 必要な情報
		$acw_info = array();
		$acw_info['model'] = ACWCore::get_var('model');
		$acw_info['action'] = ACWCore::get_var('action');
		// 必ずerrorを入れる
		$smarty->assign('acw_info', $acw_info);
		return $smarty;
	}

	/**
     * テンプレート変数初期値設定
	 * @param string $param テンプレートに設定する変数
     */
	public static function init_template_var($param)
    {
		static::$smarty_var = ACWArray::merge(static::$smarty_var, $param);
	}

    /**
     * テンプレートダイレクト表示
	 * @param string $template_path テンプレートパス
	 * @param string $param テンプレートに設定する変数
	 * @param string $escape HTMLエスケープするかどうか
     */
	public static function template($template_path, $param=null, $escape=true)
    {
		// エラーハンドラを設定
		//set_error_handler(array('ACWView', 'smarty_error'));
		try {
			// 最初に呼ばれた時点でSmartyオブジェクトを作る
			$smt = static::create_smarty($template_path, $param, $escape);
			// デバッグ設定
			$smt->debugging = false;
			$smt->debugging_ctrl = 'NONE';
			if (ACWCore::get_config('smarty_debug') === 1) {
				// 強制ON
				$smt->debugging = true;
			} else if (ACWCore::get_config('smarty_debug') === 2) {
				// パラメタ指定
				$smt->debugging_ctrl = 'URL';
			}

			// 直接表示する displayでないとデバッグ設定は効かない
			$smt->display($template_path);
		} catch (Exception $e) {
			echo 'smarty error!' . $e->getMessage();
			echo $e->getTraceAsString();
			exit();
		}
		return ACWView::OK;
	}
	
    /**
     * テンプレートに変数を設定した結果を文字列で獲得
	 * @param string $template_path テンプレートパス
	 * @param array $param テンプレートに設定する変数
	 * @param bool $escape HTMLエスケープするかどうか
     */
	public static function get_template($template_path, $param = null, $escape = true)
    {
		// エラーハンドラを設定
		//set_error_handler(array('ACWView', 'smarty_error'));
		try {
			// 最初に呼ばれた時点でSmartyオブジェクトを作る
			$smt = static::create_smarty($template_path, $param, $escape);
			// テンプレートを文字列で獲得
			$output_string = $smt->fetch($template_path);
		} catch (Exception $e) {
			echo 'smarty error!' . $e->getMessage();
			echo $e->getTraceAsString();
			exit();
		}
		return $output_string;
    }
	/**
     * Smarty Error!
     */
	/*public static function smarty_error($errno, $errstr, $errfile, $errline, $errcontext)
    {
		echo 'smarty warning!';
		echo "<br/>\r\n(" . $errno . ')' . $errstr;
		echo "<br/>\r\n" . $errfile . '(' . $errline . ')';
		exit();
	}*/
	
    /**
     * json形式出力
	 * @param string $param 変換値
	 * @param string $options json_encodeに設定するoptions
     */
	public static function json($param, $options=0)
    {
		echo json_encode($param, $options);
		return ACWView::OK;
    }

    /**
     * リダイレクトする
	 * @param string $url とび先
     */
	public static function redirect($url)
    {
		header('Location: ' . $url);
		return ACWView::OK;
    }

    /**
     * そのまま出力
	 * @param string $data 出力したい値
     */
	public static function raw($data)
    {
		echo $data;
		return ACWView::OK;
	}

	/**
	 * ヘッダを指定して出力
	 * 画像等に使います
	 * MIMEを自動識別します
	 * extension=php_fileinfo.dllを有効にしていないと使えません
	 * @param string $failename 出力したいファイル
	 */
	public static function file($filename)
	{
		$buffer = file_get_contents($filename);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_buffer($finfo, $buffer);
		finfo_close($finfo);
		header('Content-type: ' . $mime);
		echo $buffer;
		return ACWView::OK;
	}

	/**
	 * 指定コンテンツをダウンロードさせる
	 * @param string $file_name ダウンロード時のデフォルトファイル名
	 * @param string $contents ダウンロードコンテンツ
	 */
	public static function download($file_name, $contents)
	{
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") === false) {
			// IEの文字化け対策
		} else {
		    $file_name = mb_convert_encoding($file_name, 'sjis-win');
		}
		header('Content-Description: File Transfer');
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $file_name . '"');
		header('Content-Transfer-Encoding: binary');
		header("Cache-Control: public");
		header("Pragma: public");
		echo $contents;
		
		return ACWView::OK;
	}
	
	/**
	 * 指定ファイルをダウンロードさせる
	 * @param string $file_name ダウンロード時のデフォルトファイル名
	 */
	public static function download_file($file_name, $file_path, $delete = FALSE)//Edit NBKD-1033 TinVNIT 3/6/2015
	{
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") === false) {
			// IEの文字化け対策
		} else {
		    $file_name = mb_convert_encoding($file_name, 'sjis-win');
		}
		header('Content-Description: File Transfer');
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $file_name . '"');
		header('Content-Transfer-Encoding: binary');
		header("Cache-Control: public");
		header("Pragma: public");

		ob_clean();
		flush();
		readfile($file_path);
		//Add start NBKD-1033 TinVNIT 3/6/2015
		if($delete === TRUE)
		{
			unlink($file_path);
		}
		//Add end NBKD-1033 TinVNIT 3/6/2015
		return ACWView::OK;
	}
	/* CSV
		if (count($resultArray) > 0 && $header == true) {
			$lineBuf = '';
			$i = 0;
			// ヘッダの出力
			foreach ($header as $hdr) {
				if ($i > 0) {
					$lineBuf .= ',';
				}
				$lineBuf .= $hdr['name'];
				$i ++;
			}
			$lineBuf .= "\r\n";
			print mb_convert_encoding($lineBuf, "SJIS");
		}

		// データ出力
		foreach ($resultArray as &$row) {
			$lineBuf = '';
			$i = 0;
			// 一行の出力
			foreach ($header as $hdr) {
				if ($i > 0) {
					$lineBuf .= ',';
				}
				$key = $hdr['db'];
				$lineBuf .= $this->outputCSVColumn($row[$key]);
				$i ++;
			}
			$lineBuf .= "\r\n";
			print mb_convert_encoding($lineBuf, "SJIS");
		}
	*/
    
    /**
     * 404エラー
     */
	public static function http_error_404()
    {
		header("HTTP/1.0 404 Not Found");
		
		return ACWView::OK;
	}
	protected static function create_smarty_admin($template_path, $param, $escape)
    {
		// 一回目だけSmartyインスタンスを作る
		$smarty = new Smarty();
		// crisework
		$smarty->left_delimiter = ACWCore::get_config('smarty_left_delimiter');
		$smarty->right_delimiter = ACWCore::get_config('smarty_right_delimiter');
		// 保留 $smarty->force_compile = CRWK_SMARTY_FORCE_COMPILE;
		$smarty->template_dir = ACW_TEMPLATE_DIR_ADMIN;
		$smarty->compile_dir = ACW_TEMPLATE_CACHE_DIR;
		//$smarty->plugins_dir = array('plugins', CRWK_CRISEWORK2 . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'plugins');
		$smarty->plugins_dir = array(ACW_SYSTEM_DIR . '/smarty/plugins', ACW_SMARTY_PLUGIN_DIR);
		$smarty->error_reporting = E_ALL;
		
		// キャッシュを消すとエラーが出るので抑制
		$smarty->muteExpectedErrors();
		// ここから毎回変更される設定
		if ($smarty->templateExists($template_path) == false) {
			// テンプレートが存在しない
			throw new Exception('Template "' . $template_path . '" is not exists.');
			exit;
		}

		// エスケープ設定
		$smarty->escape_html = $escape;
		// 階層が異なると同一名でエラーが出るためコンパイルIDを付ける
		// いまのところ不必要 $smarty->compile_id = md5($template_path);

		// クラスのオブジェクトを獲得
		foreach (static::$smarty_var as $key => $value) {
			$smarty->assign($key, $value);
		}

		// パラメタ設定
		if (is_null($param) == false) {
			// クラスのオブジェクトを獲得
			foreach ($param as $key => $value) {
				$smarty->assign($key, $value);
			}
		}

		// 必ずerrorを入れる
		$smarty->assign('acw_error', ACWError::get_all());
		// 必要な情報
		$acw_info = array();
		$acw_info['model'] = ACWCore::get_var('model');
		$acw_info['action'] = ACWCore::get_var('action');
		// 必ずerrorを入れる
		$smarty->assign('acw_info', $acw_info);
		return $smarty;
	}
	public static function template_admin($template_path, $param=null, $escape=true)
    {
		// エラーハンドラを設定
		//set_error_handler(array('ACWView', 'smarty_error'));
		try {
			// 最初に呼ばれた時点でSmartyオブジェクトを作る
			$smt = static::create_smarty_admin($template_path, $param, $escape);
			// デバッグ設定
			$smt->debugging = false;
			$smt->debugging_ctrl = 'NONE';
			if (ACWCore::get_config('smarty_debug') === 1) {
				// 強制ON
				$smt->debugging = true;
			} else if (ACWCore::get_config('smarty_debug') === 2) {
				// パラメタ指定
				$smt->debugging_ctrl = 'URL';
			}

			// 直接表示する displayでないとデバッグ設定は効かない
			$smt->display($template_path);
		} catch (Exception $e) {
			echo 'smarty error!' . $e->getMessage();
			echo $e->getTraceAsString();
			exit();
		}
		return ACWView::OK;
	}
}
/* ファイルの終わり */