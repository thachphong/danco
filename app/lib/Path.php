<?php

/**
 * パス操作ライブラリ
 */
class Path_lib
{
	private $path_stack;	// 配列でパスを持つ

	/**
	 * コンストラクタ
	 */
	public function __construct($init_path)
	{
		$this->path_stack = $this->_to_array($init_path);
	}

	/*
	 * 今のパス
	 * 引数は一時的に追加するもの
	 */
	public function get_full_path($combine_path=null)
	{
		if (is_null($combine_path) == false) {
			// 仮に取っておく
			$temp_path = $this->path_stack;
			$this->combine($combine_path);
		}

		$full = implode('/', $this->path_stack);
		if (ACW_WINDOWS == false) {
			$full = '/' . $full;
		}

		if (is_null($combine_path) == false) {
			// 取っておいたものを元に戻す
			$this->path_stack = $temp_path;
		}
		
		return $full;
	}

	/*
	 * PHPのpathinfoを行う
	 * $path_parts['dirname'] - ディレクトリ名
	 * $path_parts['basename'] - ファイル名
	 * $path_parts['extension'] - 拡張子
	 * $path_parts['filename']  - ファイル名拡張子なし // PHP 5.2.0 以降
	 * 
	 */
	public function get_pathinfo($key=null)
	{
		$pi = pathinfo($this->get_full_path());
		if (is_null($key)) {
			return $pi;
		}
		if (isset($pi[$key]) == false) {
			return '';
		}
		return $pi[$key];
	}

	/*
	 * パスを結合
	 */
	public function combine($path)
	{
		$add_pa = $this->_to_array($path);

		foreach ($add_pa as $pa) {
			$this->path_stack[] = $pa;
		}

		return $this;	// combine('aaa')->combine('bbb')とつなげられるように
	}

	/*
	 * ランダムなファイル名を作成して後ろに追加
	 */
	public function add_random_filename($prefix, $ext,$flg_23char = false) //edit start LIXD-9 Phong VNIT - 20150623
	{
		$random_id = uniqid();
		if($flg_23char){
			$random_id =str_replace(".","",uniqid('',$flg_23char)); 
		}
		$filename = $prefix . $random_id . '.' . $ext; //edit end LIXD-9 Phong VNIT - 20150623
		$this->combine($filename);
		return $filename;
	}	
	/*
	 * ランダムなフォルダ名を作成して後ろに追加
	 */
	public function add_random_dir($prefix)
	{
		$filename = $prefix . '_' . uniqid();
		$this->combine($filename);
		return $filename;
	}

	/*
	 * 親へパス移動
	 */
	public function move_parent()
	{
		if (count($this->path_stack) > 0) {
			array_pop($this->path_stack);
		}

		return $this;	// メソッドをとつなげられるように
	}

	/*
	 * 文字列を配列へ
	 */
	private function _to_array($str)
	{
		$pa = explode('/', $str);
		$new_pa = array();
		foreach ($pa as $dname) {
			if ($dname == '') {	// 空文字は不要
				continue;
			}
			$new_pa[] = $dname;
		}
		return $new_pa;
	}

	/*
	 * path_info関数が日本語のパスを含む解析を正しく行わないため自力で実装
	 * $path_parts['dirname'] - ディレクトリ名
	 * $path_parts['basename'] - ファイル名
	 * $path_parts['extension'] - 拡張子
	 * $path_parts['filename']  - ファイル名拡張子なし
	 */
	public static function info($file)
	{
		$path_parts = array();

		$exfile = explode('/', $file);
		$exfile2 = explode("\\", $file);

		$spl = '/';
		if (count($exfile2) > count($exfile)) {
			$exfile = $exfile2;	// 正解
			$spl = "\\";
		}

		if (count($exfile) == 1) {
			// ファイル名のみ
			$path_parts['basename'] = $exfile[0];
			$path_parts['dirname'] = '';
		} else {
			$path_parts['basename'] = array_pop($exfile);
			$path_parts['dirname'] = implode($spl, $exfile);
		}

		// 拡張子
		$exbase = explode('.', $path_parts['basename']);
		if (count($exbase) == 1) {
			// ファイル名のみ
			$path_parts['filename'] = $exbase[0];
			$path_parts['extension'] = '';
		} else {
			$path_parts['extension'] = array_pop($exbase);
			$path_parts['filename'] = implode('.', $exbase);
		}

		return $path_parts;
	}
}

/* ファイルの終わり */