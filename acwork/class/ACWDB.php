<?php
/**
 * データベースコネクション基本クラス
 *
 * 基本クラスであり、継承して使います
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/
class ACWDB
{
	/**
	 * DBコネクション
	 */
	private $dbh;
	/**
	 * トランザクションが開始されたかどうか
	 */
	private $in_transaction;

	private $_last_param;	// デバッグ用
	private $_last_sql;	// デバッグ用


	/**
	 * コンストラクタ
	 * @param string $target 接続先 パラメタが空の場合は既定の接続先へ
	 */
	public function __construct($target='')
	{
		// 既に接続済み
		if (isset(ACWCore::$db_conn[$target])) {
			$this->dbh = ACWCore::$db_conn[$target];
			return;
		}
		
		// 接続パラメタ initalize_xxx_db.phpに設定
		$dpparm = acwork_db($target);
		try {
			$this->dbh = new PDO($dpparm['dsn'], $dpparm['username'], $dpparm['password'], $dpparm['driver_options']);
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			$this->_db_error($e);
		}
		ACWCore::$db_conn[$target] = $this->dbh;
		$this->in_transaction = false;
	}
	
	/**
	 * トランザクション開始
	 */
	protected function begin_transaction() {
		try {
			$this->dbh->beginTransaction();
		} catch (PDOException $e) {
			$this->_db_error($e);
		}
		$this->in_transaction = true;
	}

	/**
	 * トランザクションコミット
	 */
	protected function commit() {
		try {
			$this->dbh->commit();
		} catch (PDOException $e) {
			$this->_db_error($e);
		}
		$this->in_transaction = false;
	}

	/**
	 * トランザクションロールバック
	 */
	protected function rollback() {
		if ($this->in_transaction == false) {
			return;	// 2回やらない
		}
		$this->in_transaction = false;
		try {
			$this->dbh->rollBack();
		} catch (PDOException $e) {
			$this->_db_error($e);
		}
	}

	/**
	 * 参照系実行
	 */
	protected function query($sql, $param=null) {
		// パラメタフィルタ
		//$this->acw_param_filter($param);
		// ログ
		ACWLog::sql($sql, $param);
		// PREPARE
		$stmt = $this->_internal_prepare($sql);
		// 実行
		$this->_internal_execute($stmt, $param);
		// FETCH
		return $this->_internal_fetch($stmt);
	}
	protected function query_first($sql, $param=null) {
		$res = $this->query($sql, $param);
		if(count($res)>0) return $res[0];
		return array();
	}

	/**
	 * 更新系実行
	 */
	protected function execute($sql, $param=null) {
		// パラメタフィルタ
		//$this->acw_param_filter($param);
		// ログ
		ACWLog::sql($sql, $param);
		// PREPARE
		$stmt = $this->_internal_prepare($sql);
		// 実行
		$this->_internal_execute($stmt, $param);
	}

	/**
	 * パラメタ出力があるプロシージャ実行
	 * @param string $out_param 入出力用パラメタ 省略不可
	 * @param string $in_param 入力専用パラメタ
	 * @param string $out_length 出力用パラメタの長さ
	 */
	protected function execute_procedure($sql, &$out_param, $in_param=null, $out_length=4000) {
		// パラメタフィルタ
		//$this->acw_param_filter($in_param);
		// ログ
		ACWLog::sql($sql, $in_param);
		// PREPARE
		$stmt = $this->_internal_prepare($sql);
		try {
			if (is_null($in_param) == false) {
				// パラメタ設定
				foreach ($in_param as $key => $val) {
					if ($this->_is_db_null($val) == true) {
						// NULLの場合
						$stmt->bindValue(':' . $key, null, PDO::PARAM_NULL);
					} else {
						$stmt->bindValue(':' . $key, $val);
					}
				}
			}
			// パラメタ設定
			foreach ($out_param as $key => $val) {
				if ($this->_is_db_null($val) == true) {
					$out_param[$key] = null;
				}
				$stmt->bindParam(':' . $key, $out_param[$key], PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, $out_length);
			}

			$stmt->execute(); // プロシージャ実行
		} catch (PDOException $e) {
			$this->_db_error($e);
		}
	}

	/**
	 * SQLを登録しておく
	 */
	protected function prepare($sql) {
		// ログ
		ACWLog::sql_statement($sql);
		return $this->_internal_prepare($sql);
	}

	/**
	 * 既に定義したSQLを実行する（更新系）
	 */
	protected function prepared_execute($stmt, $param=null) {
		// パラメタフィルタ
		//$this->acw_param_filter($param);
		// ログ
		ACWLog::sql('prepared_execute', $param);
		// 実行
		$this->_internal_execute($stmt, $param);
	}

	/**
	 * 既に定義したSQLを問い合わせする（参照系）
	 */
	protected function prepared_query($stmt, $param=null) {
		// パラメタフィルタ
		//$this->acw_param_filter($param);
		// ログ
		ACWLog::sql('prepared_query', $param);
		// 実行
		$this->_internal_execute($stmt, $param);
		// FETCH
		return $this->_internal_fetch($stmt);
	}

	/**
	 * パラメタからacw_xxxを取る
	 */
	/*private function acw_param_filter(&$param) {
		if (is_null($param)) {
			return;
		}

		$new_param = array();

		// パラメタ設定
		foreach ($param as $key => $val) {
			if (substr($key, 0, 4) === 'acw_') {
				// acw_xxxは無視
			} else {
				$new_param[$key] = $val;
			}
		}

		$param = $new_param;
	}*/


	/**
	 * SQLを登録（内部関数）
	 */
	private function _internal_prepare($sql) {
		try {
			$this->_last_sql = $sql;	// デバッグ用
			$this->_last_param = null;	// デバッグ用
			$stmt = $this->dbh->prepare($sql);
		} catch (PDOException $e) {
			$this->_db_error($e);
		}
		return $stmt;
	}

	/**
	 * SQL実行（内部関数）
	 */
	private function _internal_execute($stmt, $param) {
		try {
			if (is_null($param) == false) {
				$this->_last_param = $param;	// デバッグ用

				// パラメタ設定
				foreach ($param as $key => $val) {
					if ($this->_is_db_null($val) == true) {
						// NULLの場合
						$stmt->bindValue(':' . $key, null, PDO::PARAM_NULL);
					} else {
						$stmt->bindValue(':' . $key, $val);
					}
				}
			}
			$stmt->execute(); // クエリ実行
		} catch (PDOException $e) {
			$this->_db_error($e);
		}
	}

	/**
	 * データ取得（内部関数）
	 */
	private function _internal_fetch($stmt) {
		$all = array();
		try {
			//配列で返すように変更 $all = $stmt->fetchAll(PDO::FETCH_NAMED);	//
			for (;;) {
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($result === false) {
					break;
				}
				$all[] = $result;
			}
		} catch (PDOException $e) {
			$this->_db_error($e);
		}

		return $all;
	}



	/**
	 * NULL判定
	 */
	private function _is_db_null($value) {
		if (is_null($value)) {
			return true;
		}
		
		if (is_string($value) == false) {
			settype($value, "string");
		}
		
		if ($value == '') {
			return true;
		}
		
		return false;
	}

	/**
	 * DBエラー
	 */
	private function _db_error($e) {
		if ($this->in_transaction == true) {
			$this->in_transaction = false;
			$this->dbh->rollBack();
		}

		// エラーを追加する
		ACWError::add('db_error', $e->getMessage());
		// エラーログを吐く
		$line = $e->getMessage();
		$line .= $this->_last_sql . "\r\n";
		if (is_null($this->_last_param) == false) {
			$line .= var_export($this->_last_param, true) . "\r\n";
		}
		ACWLog::sql_err($line);
		
		throw $e;
	}
}
/* ファイルの終わり */