<?php

/**
 * ファイル操作ライブラリ
 */

class FilePHPDebug_lib //extends FileWindows_lib
{
	
	//Add Start Minh Vnit 2014/11/27
	public function DeleteFolderCommandLine($folderspec, $shift_jis = true) {
		if($shift_jis ==  true)
		{
			$folderspec = mb_convert_encoding($folderspec, "Shift_JIS", "UTF-8");
		}
		try {
			if ($this->FolderExists($folderspec, false)) {	            		
				 $this->remove($folderspec);
			return true;
	        }
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FolderExists(%s):%s', $folderspec, $e->getMessage()));
			throw $e;
			
		}
		return FALSE;
	}
	//Add Start Minh Vnit 2014/11/27


	

	/**
		既存のパスの末尾に名前を追加します。
		path
			必ず指定します。引数 name で指定した名前を末尾に追加する既存パスを指定します。絶対パスまたは相対パスのどちらかを指定できます。また、必ず既存のフォルダを指定する必要はありません。
		name
			必ず指定します。引数 path で指定したパスの末尾に追加する名前を指定します。

		※ \ で結合されます
	 */
	public function BuildPath($path, $name, $shift_jis = true) {		
		try {
			if($shift_jis ==  true)
			{
				$path = mb_convert_encoding($path, "Shift_JIS", "UTF-8");
				$name = mb_convert_encoding($name, "Shift_JIS", "UTF-8");
			}			
			$tmp = $path.'/'.$name;			
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
	public function GetBaseName($path, $shift_jis = true) {
		if($shift_jis ==  true)
		{
			$path = mb_convert_encoding($path, "Shift_JIS", "UTF-8");
		}	
		try {
			$fileinfo = pathinfo($path);
			return $fileinfo['filename'];
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
	public function GetExtensionName($path, $shift_jis = true) {
		if($shift_jis ==  true)
		{
			$path = mb_convert_encoding($path, "Shift_JIS", "UTF-8");
		}
		try {
			$fileinfo = pathinfo($path);
			return $fileinfo['extension'];
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
	public function GetFileName($pathspec, $shift_jis = true) {
		try {
			if($shift_jis ==  true)
			{
				$pathspec = mb_convert_encoding($pathspec, "Shift_JIS", "UTF-8");
			}
			$fileinfo = pathinfo($pathspec);
			return $fileinfo['filename'];			
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('GetFileName(%s):%s', $pathspec, $e->getMessage()));
		}

		return FALSE;
	}
        //------chua sua-----------
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
                $comm_sjis = mb_convert_encoding($comm, 'sjis-win', 'UTF-8');
                $result_exec = exec($comm_sjis, $output, $return_var);
                return TRUE;
            } catch (exception $e) {
                return FALSE;
            }
        }
        //------chua sua----end------/>-
        //-------------******************-----------------
        
    public function FileList($filespec, $fill = '', $shift_jis = true) {
    	if($shift_jis == true)
		{
			$filespec = mb_convert_encoding($filespec, "Shift_JIS", "UTF-8");
		}
		$list = array();

		try {
			//$filespec1 = new VARIANT($filespec, VT_BSTR, CP_UTF8);
			//$folderobject = $this->_file->GetFolder($filespec1);
			if(!$this->FolderExists($filespec)){
				return $list;
			}
			foreach ($this->toIterator($filespec) as $dir) {
			$folderobject = scandir($dir, 1);
			// ファイル一覧
				//print_r($folderobject);
				foreach ($folderobject as $f)
				{
					//$tmp = mb_convert_encoding($f->Name, 'UTF-8', 'SJIS-win');
					//$tmp = $f->Name;
					if($f != '.' && $f !='..'){
						$tmp= $f;
						if ($fill == '' || mb_strpos($tmp, $fill) !== FALSE) {
							$list[] = $tmp;
						}
					}
				}
			}	
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FileList(%s, %s):%s', $filespec, $fill, $e->getMessage()));
		}
		return $list;
	}  
	/**
		FolderList 
	 */ 
	public function FolderList($filespec, $fill = '', $shift_jis = true) {

		$list = array();
		try {
			if($shift_jis == true)
			{
				$filespec = mb_convert_encoding($filespec, "Shift_JIS", "UTF-8");
			}
			if(!$this->FolderExists($filespec)){
				return $list;
			}
			$folderobject = scandir($filespec, 1);
			foreach ($folderobject as $f)
			{	
				if($f != '.' && $f !='..'){
					if(is_dir($filespec.'/'.$f)){
						$tmp = $f;
						if ($fill == '' || mb_strpos($tmp, $fill) !== FALSE) {
							$list[] = $tmp;
						}
					}
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
	public function FileFolderList($filespec, $sub = false, $fill = '', $ffill = '', $shift_jis = true) {
		if($shift_jis == true)
		{
			$filespec = mb_convert_encoding($filespec, "Shift_JIS", "UTF-8");
		}
		$list = array();
		$sublist = array();
		try {
			if(!$this->FolderExists($filespec)){
				return $list;
			}
			$folderobject = $filespec1 = scandir($filespec, 1);

			// ファイル一覧
			foreach ($folderobject as $f)
			{
				if($f != '.' && $f !='..'){
					//$tmp = mb_convert_encoding($f->Name, 'UTF-8', 'SJIS-win');
					if(is_dir($filespec.'/'.$f)){  // is sub folder
						$tmp = $f;
						if ($ffill == '' || mb_strpos($tmp, $ffill) !== FALSE) {
							$list[] = $tmp;
							$sublist[] = $tmp;
						}
					}else{   // is file
						$tmp = $f;
						if ($fill == '' || mb_strpos($tmp, $fill) !== FALSE) {
							$list[] = $tmp;
						}	
					}
				}
			}
			
			
			// フォルダ一覧（サブフォルダ検索）
			foreach ($sublist as $s)
			{
				$subpath =$filespec.'/'.$s; //$this->BuildPath($filespec, $s);
				$list[ '/' . $s ] = $this->FileFolderList($subpath  , $sub,  $fill, $ffill);
			}
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FileFolderList(%s, %d, %s, %s):%s', $filespec, $sub, $fill, $ffill, $e->getMessage()));
		}

		return $list;
	}  
    /**
		FolderExists : chek absolute  path
	 */    
    public function FolderExists($folderspec, $shift_jis = true) {
		try {
			//$folderspec1 = new VARIANT($folderspec, VT_BSTR, CP_UTF8);
			//return $this->_file->FolderExists($folderspec1) === true;
			if($shift_jis == true)
				$folderspec = mb_convert_encoding($folderspec, "Shift_JIS", "UTF-8");
			return is_dir($folderspec);
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('FolderExists(%s):%s', $folderspec, $e->getMessage()));
			throw $e;
		}

		return FALSE;
	}
    /**
		MoveFolder
	 */
    public function MoveFile($source, $destination, $shift_jis = true) {
		try {
			if($shift_jis == true)
			{
				$source = mb_convert_encoding($source, "Shift_JIS", "UTF-8");
				$destination = mb_convert_encoding($destination, "Shift_JIS", "UTF-8");
			}
				
			$this->CopyFile($source, $destination, false, true);
			$this->DeleteFile($source, false, false);			
			return TRUE;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('MoveFile(%s,%s):%s', $source, $destination, $e->getMessage()));
		}
		return FALSE;
	}	

	/**
		MoveFolder
	 */
	public function MoveFolder($source, $destination, $shift_jis = true) {
		try {
			if($shift_jis == true)
			{
				$source = mb_convert_encoding($source, "Shift_JIS", "UTF-8");
				$destination = mb_convert_encoding($destination, "Shift_JIS", "UTF-8");
			}
			$this->CopyFolder($source, $destination, true, false);
			$this->DeleteFolder($source, false, false);
			return TRUE;
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('MoveFolder(%s,%s):%s', $source, $destination, $e->getMessage()));
		}
		return FALSE;
	}
	/**
		DeleteFile
	 */
	public function DeleteFile($filespec, $force = false, $shift_jis = true) {
		try {
			if($shift_jis == true)
			{
				$filespec = mb_convert_encoding($filespec, "Shift_JIS", "UTF-8");
			}				
			return $this->remove($filespec);
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('CreateFolder(%s):%s', $filespec, $e->getMessage()));
		}
		return FALSE;
	}


	/**
		DeleteFolder
	 */
	public function DeleteFolder($folderspec, $force = false, $shift_jis = true) {
		try {	
			if($shift_jis == true)
			{
				$folderspec = mb_convert_encoding($folderspec, "Shift_JIS", "UTF-8");
			}						
			return $this->remove($folderspec);
		} catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('DeleteFolder(%s, %d):%s', $folderspec, $force, $e->getMessage()));
		}
		return FALSE;
	}
    /**
     * Copies a file.
     *
     * This method only copies the file if the origin file is newer than the target file.
     *
     * By default, if the target already exists, it is not overridden.
     *
     * @param string  $originFile The original filename
     * @param string  $targetFile The target filename
     * @param boolean $override   Whether to override an existing file or not
     *
     * @throws FileNotFoundException    When originFile doesn't exist
     * @throws IOException              When copy fails
     */
    public function CopyFile($originFile, $targetFile, $override = false, $shift_jis = true)
    {
    	try{
			
			if($shift_jis == true)
			{
				$originFile = mb_convert_encoding($originFile, "Shift_JIS", "UTF-8");
				$targetFile = mb_convert_encoding($targetFile, "Shift_JIS", "UTF-8");
			}
			$path_end= substr($originFile,strlen($originFile)-1,1);
			if($path_end=="*") // copy all file in folder
			{
				$sub_path = substr($originFile,0,strlen($originFile)-1);
				$list_file = $this->FileList($sub_path,'',FALSE);
				foreach($list_file as $item){
					$item_file = $sub_path.$item;
					$disti_file = $targetFile.$item;
					$this->CopyFile($item_file,$disti_file,$override,FALSE);
				}
				return TRUE;
			}
	        if (stream_is_local($originFile) && !is_file($originFile)) {
	            //throw new FileNotFoundException(sprintf('Failed to copy "%s" because file does not exist.', $originFile), 0, null, $originFile);
	            return FALSE;
	        }

	        $this->CreateFolder(dirname($targetFile),0777, false);

	        if (!$override && is_file($targetFile) && null === parse_url($originFile, PHP_URL_HOST)) {
	            $doCopy = filemtime($originFile) > filemtime($targetFile);
	        } else {
	            $doCopy = true;
	        }

	        if ($doCopy) {
	            
	            $source = fopen($originFile, 'r');
	            $target = fopen($targetFile, 'w');
	            stream_copy_to_stream($source, $target);
	            fclose($source);
	            fclose($target);
	            unset($source, $target);

	            if (!is_file($targetFile)) {
	                //throw new IOException(sprintf('Failed to copy "%s" to "%s".', $originFile, $targetFile), 0, null, $originFile);
	                return FALSE;
	            }
	        }
	        return TRUE;
        } catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('CopyFile(%s,%s,%d):%s', $originFile, $targetFile, $override, $e->getMessage()));
		}
		return FALSE;
    }

    /**
     * Creates a directory recursively.
     *
     * @param string|array|\Traversable $dirs The directory path
     * @param integer                   $mode The directory mode
     *
     * @throws IOException On any directory creation failure
     */
    public function CreateFolder($dirs, $mode = 0777, $shift_jis = true)
    {
    	if($shift_jis == true)
    	{
			$dirs = mb_convert_encoding($dirs, "Shift_JIS", "UTF-8");
		}
    	try{
	        foreach ($this->toIterator($dirs) as $dir) {
	            if (is_dir($dir)) {
	                continue;
	            }

	            if (true == @mkdir($dir, $mode, true)) {
	                //throw new IOException(sprintf('Failed to create "%s".', $dir), 0, null, $dir);
	                return true;
	            }
	        }
        } catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('CreateFolder(%s):%s', $foldername, $e->getMessage()));
		}
		return false;
    }

    /**
     * Checks the existence of files or directories.
     *
     * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to check
     *
     * @return Boolean true if the file exists, false otherwise
     */
    public function FileExists($files)
    {
    	$files = mb_convert_encoding($files, "Shift_JIS", "UTF-8");
        foreach ($this->toIterator($files) as $file) {
            if (!file_exists($file)) {
                return false;
            }
        }

        return true;
    }
    /**
     * Removes files or directories.
     *
     * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to remove
     *
     * @throws IOException When removal fails
     */
    public function remove($files)
    {
        $files = iterator_to_array($this->toIterator($files));
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (!file_exists($file) && !is_link($file)) {
                continue;
            }

            if (is_dir($file) && !is_link($file)) {
                $this->remove(new \FilesystemIterator($file));

                if (true !== @rmdir($file)) {
                    //throw new ioexception(sprintf('Failed to remove directory "%s".', $file), 0, null, $file);
                    return false;
                }
            } else {
               
                if (defined('PHP_WINDOWS_VERSION_MAJOR') && is_dir($file)) {
                    if (true !== @rmdir($file)) {
                        //throw new ioexception(sprintf('Failed to remove file "%s".', $file), 0, null, $file);
                        return false;
                    }
                } else {
                    if (true !== @unlink($file)) {
                        //throw new ioexception(sprintf('Failed to remove file "%s".', $file), 0, null, $file);
                        return false;
                    }
                }
            }
        }
        return TRUE;
    }
    /**
     * Creates a symbolic link or copy a directory.
     *
     * @param string  $originDir     The origin directory path
     * @param string  $targetDir     The symbolic link name
     * @param Boolean $copyOnWindows Whether to copy files if on Windows
     *
     * @throws IOException When symlink fails
     */
    public function symlink($originDir, $targetDir, $copyOnWindows = false, $shift_jis = true)
    {
    	if($shift_jis == true)
		{
			$originDir = mb_convert_encoding($originDir, "Shift_JIS", "UTF-8");
			$targetDir = mb_convert_encoding($targetDir, "Shift_JIS", "UTF-8");
		}	
        if (!function_exists('symlink') && $copyOnWindows) {
            $this->CopyFolder($originDir, $targetDir, true, false);

            return;
        }

        $this->CreateFolder(dirname($targetDir));

        $ok = false;
        if (is_link($targetDir)) {
            if (readlink($targetDir) != $originDir) {
                $this->remove($targetDir);
            } else {
                $ok = true;
            }
        }

        if (!$ok) {
            if (true !== @symlink($originDir, $targetDir)) {
                $report = error_get_last();
                if (is_array($report)) {
                    if (defined('PHP_WINDOWS_VERSION_MAJOR') && false !== strpos($report['message'], 'error code(1314)')) {
                        //throw new ioexception('Unable to create symlink due to error code 1314: \'A required privilege is not held by the client\'. Do you have the required Administrator-rights?');
                        return false;
                    }
                }

                //throw new ioexception(sprintf('Failed to create symbolic link from "%s" to "%s".', $originDir, $targetDir), 0, null, $targetDir);
                return false;
            }
        }
    }

    /**
     * Given an existing path, convert it to a path relative to a given starting path
     *
     * @param string $endPath   Absolute path of target
     * @param string $startPath Absolute path where traversal begins
     *
     * @return string Path of target relative to starting path
     */
    public function makePathRelative($endPath, $startPath)
    {
    	$endPath = mb_convert_encoding($endPath, "Shift_JIS", "UTF-8");
		$startPath = mb_convert_encoding($startPath, "Shift_JIS", "UTF-8");
        // Normalize separators on Windows
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $endPath = strtr($endPath, '\\', '/');
            $startPath = strtr($startPath, '\\', '/');
        }

        // Split the paths into arrays
        $startPathArr = explode('/', trim($startPath, '/'));
        $endPathArr = explode('/', trim($endPath, '/'));

        // Find for which directory the common path stops
        $index = 0;
        while (isset($startPathArr[$index]) && isset($endPathArr[$index]) && $startPathArr[$index] === $endPathArr[$index]) {
            $index++;
        }

        // Determine how deep the start path is relative to the common path (ie, "web/bundles" = 2 levels)
        $depth = count($startPathArr) - $index;

        // Repeated "../" for each level need to reach the common path
        $traverser = str_repeat('../', $depth);

        $endPathRemainder = implode('/', array_slice($endPathArr, $index));

        // Construct $endPath from traversing to the common path, then to the remaining $endPath
        $relativePath = $traverser.(strlen($endPathRemainder) > 0 ? $endPathRemainder.'/' : '');

        return (strlen($relativePath) === 0) ? './' : $relativePath;
    }

    /**
     * Mirrors a directory to another.
     *
     * @param string       $originDir The origin directory
     * @param string       $targetDir The target directory
     * @param \Traversable $iterator  A Traversable instance
     * @param array        $options   An array of boolean options
     *                               Valid options are:
     *                                 - $options['override'] Whether to override an existing file on copy or not (see copy())
     *                                 - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink())
     *                                 - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
     *
     * @throws IOException When file type is unknown
     */ //CopyFolder($source, $destination, $overwrite = true) {
    public function CopyFolder($directory, $destination, $overwrite = true, $shift_jis = true)
    {
    	try{
    		if($shift_jis == true)
			{
				$directory = mb_convert_encoding($directory, "Shift_JIS", "UTF-8");
				$destination = mb_convert_encoding($destination, "Shift_JIS", "UTF-8");
			}					
	        // If no directory to copy return false
			if (!is_dir($directory)) {
				return false;
			} else {
				// Create a FilesystemIterator instance 
				$FilesystemIterator = new FilesystemIterator($directory,FilesystemIterator::SKIP_DOTS);
				if($overwrite === false)
				{
					$this->DeleteFolder($destination, false, false);
				}
				// If destination directory not exists
				if (!is_dir($destination)) {
					// Create a destination directory
					mkdir($destination,0777,true);
				}
				// Loop files and directories
				foreach ($FilesystemIterator as $Iterator) {
					// If directory
					if ($Iterator->isDir()) {
						// Copy directory or return false
						$path_source = $Iterator->getPathname();
						$fname = $Iterator->getFilename ();
						$path_copy = $destination.DIRECTORY_SEPARATOR.$fname;
						if (false === $this->CopyFolder($path_source,$path_copy))
							return false;
					} else { // Else if file
						// Copy file or return false
						$path_source = $Iterator->getPathname();
						$fname = $Iterator->getFilename();
						$path_copy = $destination.DIRECTORY_SEPARATOR.$fname;
						if (false === copy($path_source,$path_copy))
								return false;
					}
				}
				// If copying finished without any failure return true
				return true;
			}
        } catch (com_exception $e) {
			ACWLog::write_file('FSO', sprintf('CopyFolder(%s,%s,%d):%s', $source, $destination, $overwrite, $e->getMessage()));
		}
		return FALSE;
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file A file path
     *
     * @return Boolean
     */
    public function isAbsolutePath($file)
    {
    	$file = mb_convert_encoding($file, "Shift_JIS", "UTF-8");
        if (strspn($file, '/\\', 0, 1)
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && substr($file, 1, 1) === ':'
                && (strspn($file, '/\\', 2, 1))
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $files
     *
     * @return \Traversable
     */
    private function toIterator($files)
    {
        if (!$files instanceof \Traversable) {
            $files = new \ArrayObject(is_array($files) ? $files : array($files));
        }

        return $files;
    }
}
/* ファイルの終わり */
