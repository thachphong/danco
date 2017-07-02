<?php

/**
 * ファイル操作ライブラリ(Windows 限定、というか Windows の 0x5C 対策)
 */
class FileWindows_lib
{
	protected $_file;

	function __construct() {

		$this->_file = new COM ('Scripting.FileSystemObject', null, CP_UTF8);
                
	}


	function __destruct() {
		$this->_file = null;
	}

	/**
		<MSDN>記述内容
		ファイルを別の場所へコピーします。
		source
			必ず指定します。コピーするファイルを表す文字列を指定します。1 つ以上のファイルを指定するためにワイルドカード文字を使用することもできます。
		destination
			必ず指定します。引数 source で指定したファイルのコピー先を表す文字列を指定します。ワイルドカード文字は使用できません。
		overwrite
			省略可能です。既存ファイルを上書きするかどうかを示すブール値を指定します。真 (true) を指定すると既存フォルダ内のファイルは上書きされ、偽 (false) を指定すると上書きされません。既定値は、真 (true) です。引数 destination に指定したコピー先が読み取り専用の属性を持っていた場合は、引数 overwrite に指定した値とは関係なく CopyFile メソッドの処理は失敗するので、注意する必要があります。
	 */
	public function CopyFile($source, $destination, $overwrite = true) {
		try {
			$source1 = new VARIANT($source, VT_BSTR, CP_UTF8);
			$destination1 = new VARIANT($destination, VT_BSTR, CP_UTF8);
			$this->_file->CopyFile($source1, $destination1, $overwrite);
			return TRUE;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('CopyFile(%s,%s,%d):%s', $source, $destination, $overwrite, $e->getMessage()));
		}
		return FALSE;
	}

	/**
		<MSDN>記述内容
		フォルダを再帰的に別の場所へコピーします。
		source
			必ず指定します。フォルダを表す文字列を指定します。1 つ以上のフォルダを指定するためにワイルドカード文字を使用できます。
		destination
			必ず指定します。引数 source で指定したフォルダおよびサブフォルダのコピー先を表す文字列を指定します。ワイルドカード文字は使用できません。
		overwrite
			省略可能です。既存フォルダを上書きするかどうかを示すブール値を指定します。真 (true) を指定すると既存フォルダ内のファイルは上書きされ、偽 (false) を指定すると上書きされません。既定値は、真 (true) です。
	 */
	public function CopyFolder($source, $destination, $overwrite = true) {
		try {
			$source1 = new VARIANT($source, VT_BSTR, CP_UTF8);
			$destination1 = new VARIANT($destination, VT_BSTR, CP_UTF8);
			$this->_file->CopyFolder($source1, $destination1, $overwrite);
			return TRUE;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('CopyFolder(%s,%s,%d):%s', $source, $destination, $overwrite, $e->getMessage()));
		}
		return FALSE;
	}



	/**
		<MSDN>記述内容
		1 つまたは複数のファイルを、ある場所から別の場所に移動します。
		source
			必ず指定します。移動するファイルのパスを指定します。パスの最後の構成要素内ではワイルドカード文字を使用できます。
		destination
			必ず指定します。ファイルの移動先のパスを指定します。ワイルドカード文字は使用できません。

		解説
			引数 source にワイルドカード文字を使用したとき、および引数 destination がパスの区切り文字 (\) で終わるとき、引数 destination には既存フォルダを指定したと判断され、条件に一致するファイルがそのフォルダ内へ移動されます。それ以外のときは、引数 destination には作成するファイルの名前を指定したと判断されます。いずれの場合も、移動される各ファイルで発生する処理の実行は 3 種類あります。
			引数 destination に指定したファイルが存在しない場合、ファイルが移動します。これが通常の場合です。
			引数 destination に指定したファイルが存在する場合、エラーが発生します。
			引数 destination がディレクトリの場合、エラーが発生します。

			このメソッドを使用してボリューム間でファイルを移動できるのは、オペレーティング システムでボリューム間のファイル移動がサポートされている場合だけです。
	*/
	public function MoveFile($source, $destination) {
		try {
			$source1 = new VARIANT($source, VT_BSTR, CP_UTF8);
			$destination1 = new VARIANT($destination, VT_BSTR, CP_UTF8);
			$this->_file->MoveFile($source1, $destination1);
			return TRUE;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('MoveFile(%s,%s):%s', $source, $destination, $e->getMessage()));
		}
		return FALSE;
	}

	/**
		<MSDN>記述内容
		1 つまたは複数のフォルダを、ある場所から別の場所に移動します。
		source
			必ず指定します。移動するフォルダのパスを指定します。パスの最後の構成要素内ではワイルドカード文字を使用できます。
		destination
			必ず指定します。フォルダの移動先のパスを指定します。ワイルドカード文字は使用できません。
		解説
			引数 source にワイルドカード文字を使用したとき、および引数 destination がパスの区切り文字 (\) で終わるとき、引数 destination には既存フォルダを指定したと判断され、条件に一致するファイルがそのフォルダ内へ移動されます。それ以外のときは、引数 destination に作成するフォルダの名前を指定したと判断されます。いずれの場合でも、移動される各フォルダで発生する処理の実行は 3 種類あります。
			引数 destination に指定したフォルダが存在しない場合、フォルダが移動します。これが通常の場合です。
			引数 destination に指定したファイルが存在する場合、エラーが発生します。
			引数 destination がディレクトリの場合、エラーが発生します。
			引数 source でワイルドカード文字を使用した指定がどのフォルダとも一致しなかった場合も、エラーが発生します。MoveFolder メソッドは、最初のエラーが発生した時点で処理を中止します。エラーが発生するまでに行った処理を取り消したり元に戻したりする処理は一切行われません。

		重要 このメソッドを使用してボリューム間でフォルダを移動できるのは、オペレーティング システムでボリューム間のフォルダ移動がサポートされている場合だけです。
	 */
	public function MoveFolder($source, $destination) {
		try {
			$source1 = new VARIANT($source, VT_BSTR, CP_UTF8);
			$destination1 = new VARIANT($destination, VT_BSTR, CP_UTF8);
			$this->_file->MoveFolder($source1, $destination1);
			return TRUE;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('MoveFolder(%s,%s):%s', $source, $destination, $e->getMessage()));
		}
		return FALSE;
	}



	/**
		<MSDN>記述内容
		フォルダを作成します。
		foldername
			必ず指定します。作成するフォルダを表す文字列式を指定します。
		解説
			指定したフォルダが既に存在していた場合は、エラーが発生します。
	 */
	public function CreateFolder($foldername) {
		try {
			$foldername1 = new VARIANT($foldername, VT_BSTR, CP_UTF8);
			$this->_file->CreateFolder($foldername1);
			return TRUE;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('CreateFolder(%s):%s', $foldername, $e->getMessage()));
		}
		return FALSE;
	}


	/**
		<MSDN>記述内容
		指定されたファイルを削除します。
		filespec
			必ず指定します。削除するファイルの名前を指定します。パスの最後の構成要素内ではワイルドカード文字も使用できます。
		force
			省略可能です。真 (true) を指定すると、読み取り専用の属性を持つファイルも削除されます。偽 (false) を指定すると、読み取り専用ファイルは削除されません (既定値)。
	 */
	public function DeleteFile($filespec, $force = false) {
		try {
			$filespec1 = new VARIANT($filespec, VT_BSTR, CP_UTF8);
			$this->_file->DeleteFile($filespec1, $force);
			return TRUE;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('CreateFolder(%s):%s', $filespec, $e->getMessage()));
		}
		return FALSE;
	}


	/**
		<MSDN>記述内容
		指定されたフォルダおよびそのフォルダ内のフォルダとファイルを削除します。
		folderspec
			必ず指定します。削除するフォルダの名前を指定します。パスの最後の構成要素内ではワイルドカード文字を使用できます。
		force
			省略可能です。真 (true) を指定すると、読み取り専用の属性を持つフォルダも削除されます。偽 (false) を指定すると、読み取り専用フォルダは削除されません (既定値)。
	 */
	public function DeleteFolder($folderspec, $force = false) {
		try {
			$folderspec1 = new VARIANT($folderspec, VT_BSTR, CP_UTF8);
			$this->_file->DeleteFolder($folderspec1, $force);
			return TRUE;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('DeleteFolder(%s, %d):%s', $folderspec, $force, $e->getMessage()));
		}
		return FALSE;
	}



	/**
		<MSDN>記述内容
		指定したファイルが存在する場合は、真 (true) を返します。存在しない場合は、偽 (false) を返します。
		filespec
			必ず指定します。存在するかどうかを調べるファイルの名前を指定します。カレント フォルダ内にないファイルを調べる場合は、絶対パスを指定する必要があります。絶対パスまたは相対パスのどちらかを指定できます。
	 */
	public function FileExists($filespec) {
		try {
			$filespec1 = new VARIANT($filespec, VT_BSTR, CP_UTF8);
			return $this->_file->FileExists($filespec1) === true;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FileExists(%s):%s', $filespec, $e->getMessage()));
			throw $e;
		}

		return FALSE;
	}

	/**
		<MSDN>記述内容
		指定されたフォルダが存在する場合は、真 (true) を返します。存在しない場合は、偽 (false) を返します。
		folderspec
			必ず指定します。存在するかどうかを調べるフォルダの名前を指定します。カレント フォルダ内にないフォルダを調べる場合は、絶対パスを指定する必要があります。絶対パスまたは相対パスのどちらかを指定できます。
	 */
	public function FolderExists($folderspec) {
		try {
			$folderspec1 = new VARIANT($folderspec, VT_BSTR, CP_UTF8);
			return $this->_file->FolderExists($folderspec1) === true;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FolderExists(%s):%s', $folderspec, $e->getMessage()));
			throw $e;
		}

		return FALSE;
	}
	
	//Add Start Minh Vnit 2014/11/27
	public function DeleteFolderCommandLine($folderspec) {
		try {
			if ($this->_file->FolderExists($folderspec)) {
	            $comm = sprintf('rd  /S /Q "%s"', $folderspec);
				$comm_sjis = mb_convert_encoding($comm, 'sjis-win', 'UTF-8');
				$result = exec($comm_sjis, $output, $return_var);
				return TRUE;
	        }
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FolderExists(%s):%s', $folderspec, $e->getMessage()));
			throw $e;
			
		}
		return FALSE;
	}
	//Add Start Minh Vnit 2014/11/27


	/**
	 * ファイル一覧
	 * @param   $filespec  チェック対象のフォルダ
	 * @param   $fill      (未指定可)フィルタ文字指定、指定した文字列を含むもののみが列挙される
	 * @return  取得結果の配列
	 */
	public function FileList($filespec, $fill = '') {
		$list = array();

		try {
			$filespec1 = new VARIANT($filespec, VT_BSTR, CP_UTF8);
			$folderobject = $this->_file->GetFolder($filespec1);

			// ファイル一覧
			foreach ($folderobject->Files as $f)
			{
				//$tmp = mb_convert_encoding($f->Name, 'UTF-8', 'SJIS-win');
				$tmp = $f->Name;
				if ($fill == '' || mb_strpos($tmp, $fill) !== FALSE) {
					$list[] = $tmp;
				}
			}
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FileList(%s, %s):%s', $filespec, $fill, $e->getMessage()));
		}

		return $list;
	}

	/**
	 * フォルダ一覧
	 * @param   $filespec  チェック対象のフォルダ
	 * @param   $fill      (未指定可)フィルタ文字指定、指定した文字列を含むもののみが列挙される
	 * @return  取得結果の配列
	 */
	public function FolderList($filespec, $fill = '') {

		$list = array();
		try {
			$filespec1 = new VARIANT($filespec, VT_BSTR, CP_UTF8);

			$folderobject = $this->_file->GetFolder($filespec1);

			// フォルダ一覧
			foreach ($folderobject->SubFolders as $f)
			{
				//$tmp = mb_convert_encoding($f->Name, 'UTF-8', 'SJIS-win');
				$tmp = $f->Name;
				if ($fill == '' || mb_strpos($tmp, $fill) !== FALSE) {
					$list[] = $tmp;
				}
			}
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FolderList(%s, %s):%s', $filespec, $fill, $e->getMessage()));
		}

		return $list;
	}

	/**
	 * ファイル＆フォルダ一覧
	 * @param   $filespec  チェック対象のフォルダ
	 * @param   $sub       (未指定可)サブフォルダ内も検索を続けるなら true、デフォルト false
	 * @param   $fill      (未指定可)ファイルへのフィルタ文字指定、指定した文字列を含むもののみが列挙される
	 * @param   $ffill     (未指定可)フォルダへのフィルタ文字指定、指定した文字列を含むもののみが列挙される
	 * @return  取得結果の配列
	 *          [0 ～]  現フォルダ内のファイル、フォルダ文字列
	 *          ['/nnnn'] nnnn フォルダ内データの配列
	 */
	public function FileFolderList($filespec, $sub = false, $fill = '', $ffill = '') {

		$list = array();
		$sublist = array();
		try {
			$filespec1 = new VARIANT($filespec, VT_BSTR, CP_UTF8);
			$folderobject = $this->_file->GetFolder($filespec1);

			// ファイル一覧
			foreach ($folderobject->Files as $f)
			{
				//$tmp = mb_convert_encoding($f->Name, 'UTF-8', 'SJIS-win');
				$tmp = $f->Name;
				if ($fill == '' || mb_strpos($tmp, $fill) !== FALSE) {
					$list[] = $tmp;
				}
			}
			// フォルダ一覧
			foreach ($folderobject->SubFolders as $f)
			{
				//$tmp = mb_convert_encoding($f->Name, 'UTF-8', 'SJIS-win');
				$tmp = $f->Name;
				if ($ffill == '' || mb_strpos($tmp, $ffill) !== FALSE) {
					$list[] = $tmp;
					$sublist[] = $tmp;
				}
			}
			
			// フォルダ一覧（サブフォルダ検索）
			foreach ($sublist as $s)
			{
				$subpath = $this->BuildPath($filespec, $s);
				$list[ '/' . $s ] = $this->FileFolderList($subpath  , $sub,  $fill, $ffill);
			}
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FileFolderList(%s, %d, %s, %s):%s', $filespec, $sub, $fill, $ffill, $e->getMessage()));
		}

		return $list;
	}
	//add startLIXD-422 Phong VNIT-20160101
	public function FileListFull($filespec, $sub = false, $fill = '', $ffill = '') 
	{
		$list = $this->FileFolderList($filespec,$sub,$fill,$ffill);
		$new_list=array();
		$this->get_list($new_list, $list,'');
		return 	$new_list;
	}
	private function get_list(&$list, $array, $sub='')
	{		
		if(count($array)>0){			
			foreach($array as $key => $val )
			{
				if(is_array($val)  ){
					$this->get_list($list, $val,$sub . $key.'/');	
				}else{
					if($sub==''){
						$val ='/'.$val;
					}
					$list[]=$sub . $val;
				}
			}		
		}
	}
	//add end LIXD-422 Phong VNIT-20160101
	/**
		既存のパスの末尾に名前を追加します。
		path
			必ず指定します。引数 name で指定した名前を末尾に追加する既存パスを指定します。絶対パスまたは相対パスのどちらかを指定できます。また、必ず既存のフォルダを指定する必要はありません。
		name
			必ず指定します。引数 path で指定したパスの末尾に追加する名前を指定します。

		※ \ で結合されます
	 */
	public function BuildPath($path, $name) {
		try {
			$path1 = new VARIANT($path, VT_BSTR, CP_UTF8);
			$name1 = new VARIANT($name, VT_BSTR, CP_UTF8);
			$tmp = $this->_file->BuildPath($path1, $name1);
			//return mb_convert_encoding($tmp, 'UTF-8', 'SJIS-win');
			return $tmp;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('BuildPath(%s,%s):%s', $path, $name, $e->getMessage()));
		}

		return FALSE;
	}

	/**
		指定されたパス内に含まれるファイルのベース名 (ファイル拡張子を除いたもの) を表す文字列を返します。
		path
			必ず指定します。ベース名を取得するファイルまたはフォルダのパスを指定します。
		解説
			引数 path に指定された文字列でベース名に該当するファイルまたはフォルダがない場合は、GetBaseName メソッドは長さ 0 の文字列 ("") を返します。
	 */
	public function GetBaseName($path) {
		try {
			$path1 = new VARIANT($path, VT_BSTR, CP_UTF8);
			//return mb_convert_encoding($this->_file->GetBaseName($path1), 'UTF-8', 'SJIS-win');
			return $this->_file->GetBaseName($path1);
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('GetBaseName(%s):%s', $path, $e->getMessage()));
		}

		return FALSE;
	}

	/**
		指定されたパスの拡張子を表す文字列を返します。
		path
			必ず指定します。拡張子を取り出す構成要素のパスを指定します。
		解説
			ネットワーク ドライブの場合は、ルート ディレクトリ (\) が構成要素であると見なされます。
			引数 path に指定された文字列で拡張子に該当するものがない場合は、長さ 0 の文字列 ("") が返されます。
	 */
	public function GetExtensionName($path) {
		try {
			$path1 = new VARIANT($path, VT_BSTR, CP_UTF8);
			//return mb_convert_encoding($this->_file->GetExtensionName($path1), 'UTF-8', 'SJIS-win');
			return $this->_file->GetExtensionName($path1);
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('GetExtensionName(%s):%s', $path, $e->getMessage()));
		}

		return FALSE;
	}

	/**
		指定されたパスの最後のファイル名またはフォルダ名を返します。
		pathspec
			必ず指定します。指定したファイルの絶対パスまたは相対パスです。
		解説
			引数 pathspec に指定した文字列の最後が名前付きの構成要素になっていない場合は、長さ 0 の文字列 ("") を返します。
		メモ   GetFileName は、パスに指定された文字列に対してのみ処理を行います。指定されたパスを解決したり、指定されたパスが実際に存在するかどうかを確認したりしません。
	 */
	public function GetFileName($pathspec) {
		try {
			$pathspec1 = new VARIANT($pathspec, VT_BSTR, CP_UTF8);
			//return mb_convert_encoding($this->_file->GetFileName($pathspec1), 'UTF-8', 'SJIS-win');
			return $this->_file->GetFileName($pathspec1);
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('GetFileName(%s):%s', $pathspec, $e->getMessage()));
		}

		return FALSE;
	}
        
        public function sevenzip_compress($zip_file_name, $the_folder) {
            try {
                $comm = sprintf(AKAGANE_WEB_PATH . '/7za.exe a "%s" "%s"', $zip_file_name, $the_folder);
                $comm_sjis = mb_convert_encoding($comm, 'sjis-win', 'UTF-8');
                $result_exec = exec($comm_sjis, $output, $return_var);
                return TRUE;
            } catch (exception $e) {
                return FALSE;
            }
        }

        public function sevenzip_extract($file_path, $folder_unzip) {
            try {
                $comm = sprintf(AKAGANE_WEB_PATH . '/7za.exe x "%s" -o"%s"', $file_path, $folder_unzip);
                //$comm_sjis = mb_convert_encoding($comm, 'sjis-win', 'UTF-8');//Remove LIXD-18 MinhVnit for extract file zip UTF-8 
                $result_exec = exec($comm, $output, $return_var);
                return TRUE;
            } catch (exception $e) {
                return FALSE;
            }
        }
        
}

// テスト
// 		$t = new File_lib();
// 		$r = $t->FileFolderList('D:/xampp/usys/cms_test/app/model', false, 'A', '');
// 		ACWLog::debug($r);
// 		ACWLog::debug('BuildPath =' . $t->BuildPath('c:/aaa/bbb/ccc', 'ddd'));
// 		ACWLog::debug('GetBaseName =' . $t->GetBaseName('c:/aaa/bbb/ccc/ddd.txtxtxt.txt'));
// 		ACWLog::debug('GetFileName =' . $t->GetFileName('c:/aaa/bbb/ccc/ddd.txtxtxt.txt'));
// 		ACWLog::debug('GetExtensionName =' . $t->GetExtensionName('c:/aaa/bbb/ccc/ddd.txtxtxt.txt'));
// 		ACWLog::debug(
// 			"TEST:"
// 			. "\n1=" . ($t->FileExists('d:/test/test.txt')     ? 'true' : 'false') // ある
// 			. "\n2=" . ($t->FileExists('d:/test/')             ? 'true' : 'false') // ある（※フォルダ）
// 			. "\n3=" . ($t->FolderExists('d:/test/test.txt')   ? 'true' : 'false') // ある（※ファイル）
// 			. "\n4=" . ($t->FolderExists('d:/test/')           ? 'true' : 'false') // ある
// 			. "\n5=" . ($t->FileExists('d:/test/test2.txt')    ? 'true' : 'false') // ない
// 			. "\n6=" . ($t->FileExists('d:/test2/')            ? 'true' : 'false') // ない
// 			. "\n7=" . ($t->FolderExists('d:/test/test2.txt')  ? 'true' : 'false') // ない
// 			. "\n8=" . ($t->FolderExists('d:/test2/')          ? 'true' : 'false') // ない
// 		);
// 		$r = $t->FileFolderList('D:/xampp/usys/cms_test2/app/model', true, 'i');
// 		ACWLog::debug($r);


//
//	出力内容
//		Array
//		(
//		    [0] => Approval.php
//		    [1] => Approval2.php
//		)
//
//		BuildPath =c:/aaa/bbb/ccc\ddd
//
//		GetBaseName =ddd.txtxtxt
//
//		GetFileName =ddd.txtxtxt.txt
//
//		GetExtensionName =txt
//
//		TEST:
//		1=true
//		2=false
//		3=false
//		4=true
//		5=false
//		6=false
//		7=false
//		8=false
//		Array
//		(
//		    [0] => AutoKumihan.php
//		    [1] => BackKumihan.php
//		    [2] => History.php
//		    [3] => ItemExportHistory.php
//		    [4] => Itemfile.php
//		    [5] => ItemImportHistory.php
//		    [6] => LinkItem.php
//		    [7] => Login.php
//		    [8] => Media.php
//		    [9] => Mediatype.php
//		    [10] => Relation.php
//		    [11] => Series.php
//		    [12] => SeriesTagInput.php
//		    [13] => Trigger.php
//		    [14] => common
//		    [/common] => Array
//		        (
//		            [0] => Kumihan.php
//		            [1] => Limit.php
//		            [2] => Section.php
//		        )
//		
//		)



/* ファイルの終わり */