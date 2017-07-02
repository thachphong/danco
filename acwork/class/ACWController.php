<?php

/**
 * コントローラー
 *
 * 変更してはいけません。継承したら解決しませんか？
 *
 * @category   ACWork
 * @copyright  2013
 * @version    0.9
 */
class ACWController
{

	/**
	 * メイン実行処理
	 */
	public function main($commodel)
	{
		try {
			/**
			 * 初期化
			 */
			$this->init();
			/**
			 * ルーティング
			 */
			$this->get_routes($commodel);
			/**
			 * modelに振り分け
			 */
			$this->dispach();
		} catch (Exception $e) {
			/**
			 * 中止処理
			 */
			$this->cancel($e);
		}
	}

	/**
	 * 初期化
	 */
	protected function init()
	{
		/**
		 * autoloadの設定
		 */
		spl_autoload_register(array($this, 'loader'));
		/*
		 * エラーキャッチ
		 */
		set_error_handler(array($this, 'error_stop'));
	}

	/**
	 * ルート獲得
	 */
	protected function get_routes($commodel)
	{
		$dir_level = array();
		$exec_action_init = 'index';
		if (is_null($commodel)) {
			/**
			 * Webからの実行
			 */
			$exec_model_init = 'index';
			/**
			 * URL獲得
			 */
			$cmd_name = ACWCore::get_config('url_cmd');
			if (isset($_GET[$cmd_name]) == false) {
				// 直接指定index.php
				$this->error_404('url_cmd');
			}

			$url_level = explode('/', $_GET[$cmd_name]);
			ACWCore::set_var('url_level', $url_level);
			/**
			 * URLによるルーティング
			 */
			if (acw_routes($url_level, $exec_model_init, $exec_action_init, $dir_level) == false) {
				$this->error_404('acw_routes');
			}
		} else {
			/*
			 * コマンドラインからの実行モデル指定
			 */
			$moddir = explode('/', $commodel);
			$url_level = $moddir;
			/*
			$max = count($moddir);
			if ($max > 1) {
				for ($i = 1; $i < $max; $i ++) {
					$dir_level[] = array_shift($moddir);
				}
			}
			$exec_model_init = $moddir[0];
			*/

			if (acw_routes($url_level, $exec_model_init, $exec_action_init, $dir_level) == false) {
				$this->error_404('acw_routes');
			}
		}
		// 小文字化処理
		$exec_model = strtolower($exec_model_init);
		$exec_action = strtolower($exec_action_init);
		if ($exec_model == '') {
			$exec_model = 'index';
		}
		if ($exec_action == '') {
			$exec_action = 'index';
		}
		ACWCore::set_var('model', $exec_model);
		ACWCore::set_var('action', $exec_action);

		/**
		 * ディレクトリ
		 */
		$dir_name = (count($dir_level) > 0) ? '_' . implode('_', $dir_level) : '';

		/**
		 * _を抜いてみる
		 */
		ACWCore::set_var('dir_level', $dir_level);
		ACWCore::set_var('model_name', str_replace('_', '', ucwords($exec_model)) . $dir_name . '_model');
		ACWCore::set_var('action_method', 'action_' . str_replace('_', '', $exec_action));
		ACWCore::set_var('param_level', $url_level);

	}

	/**
	 * 振り分け
	 */
	protected function dispach()
	{
		$dir_level = ACWCore::get_var('dir_level');
		$model_name = ACWCore::get_var('model_name');
		$action_method = ACWCore::get_var('action_method');

		$dir_level[] = $model_name;
		/**
		 * クラスロードチェック
		 */
		if (class_exists($model_name) == false) {
			$this->error_404('class not found');
		}

		/**
		 * init実行
		 */
		call_user_func(array($model_name, 'init'));

		/**
		 * メソッドチェック
		 */
		if (method_exists($model_name, $action_method) == false) {
			$this->error_404('action not found');
		}

		/**
		 * action実行
		 */
		if (call_user_func(array($model_name, $action_method)) !== ACWView::OK) {
			$this->error_fatal('action method not return value');
		}
	}

	/**
	 * パラメタ獲得
	 */
	public function set_param_all($param_key)
	{
		$param = array();

		foreach ($param_key as $key) {
			if ($key == 'acw_url') {
				// ACWorkオリジナルURLパラメタを追加
				$param['acw_url'] = ACWCore::get_var('param_level');
			} else if (isset($_GET[$key])) {
				$param[$key] = $this->check_param($_GET[$key]);
			} else if (isset($_POST[$key])) {
				$param[$key] = $this->check_param($_POST[$key]);
			} else if (isset($_FILES[$key])) {
				$param[$key] = $this->check_param($_FILES[$key]);
			} else {
				$param[$key] = null;
			}
		}


		return $param;
	}

	// パラメタ
	private function check_param($value)
	{
		if (is_object($value) || is_array($value) || $value != '') {
			return $value;
		}

		return null;
	}

	/**
	 * エラー中止
	 */
	protected function cancel($e)
	{
		// エラーログ
		ACWLog::err('SYSERROR', $e);
		$model_name = ACWCore::get_var('model_name');
		$user_func = false;
		/**
		 * model定義が終わっている
		 */
		if (isset($model_name)) {
			if (method_exists($model_name, 'catch_error')) {
				$action_method = ACWCore::get_var('action');
				call_user_func(array($model_name, 'catch_error'), $action_method, $e);
				$user_func = true;
			}
		}

		if ($user_func == false) {
			/*
			 * ユーザーが何もしない
			 * 500エラー
			 */
			header('HTTP/1.0 500 Internal Server Error');
			echo $e;
		}
	}

	/**
	 * PHPエラーによる中止
	 */
	public function error_stop($errno, $errstr, $errfile, $errline)
	{
		$e = new ErrorException($errstr, $errno, 0, $errfile, $errline);
		$this->cancel($e);
		exit();	// 終わり
	}

	/**
	 * 内部エラー
	 */
	protected function error_fatal($memo)
	{
		echo 'fatal error!';
		$e = new Exception($memo);
		ACWLog::err('FATALERROR', $e);
		// とりあえず
		exit;
	}

	/**
	 * 404にしたい
	 */
	protected function error_404($memo)
	{
		// とりあえず
		$text = "404! not found\r\n";
		$text .= "HTTP_HOST:" . $_SERVER['HTTP_HOST'] . "\r\n";
		$text .= "REQUEST_URI:" . $_SERVER['REQUEST_URI'] . "\r\n";
		$text .= "url_level:" . var_export(ACWCore::get_var('url_level'), true);
		ACWLog::write_file('404ERROR', $text);
		header('HTTP/1.0 404 Not Found');
		die('Not Found (' . $memo . ')');
	}

	/**
	 * クラスの読み込み
	 */
	protected function loader($class_name)
	{
		/**
		 * クラス名を_で区切ります
		 */
		$name = explode('_', $class_name);
		/**
		 * 逆にします
		 */
		$r_name = array_reverse($name);
		/**
		 * ディレクトリとして結合します
		 */
		$dir = implode('/', $r_name);

		$dir_all = ACW_APP_DIR . '/' . $dir . '.php';
		ACWCore::set_var('model_php', $dir_all);
		/**
		 * 読み込み
		 */
		if (file_exists($dir_all)) {
			require_once $dir_all; // 複数のautoloadがある場合があるため、エラーを返さない
		}
	}

}

/* ファイルの終わり */