<?php

class Project extends RestControllerBase {

	public function get(){
		// プロジェクトの一覧を返す
		$dirs = array();
		$basedir = dirname(FrameworkManagerConfigure::PROJECT_ROOT_PATH);
		debug($basedir);
		if ($handle = opendir($basedir)) {
			while (false !== ($file = readdir($handle))) {
				debug($basedir."/".$file);
				if("." !== $file && ".." !== $file && FALSE !== is_dir($basedir."/".$file)){
					$dirs[] = $file;
				}
			}
			closedir($handle);
		}
		return $dirs;
	}

	public function post(){
		// このRESTは実行出来ない
		throw new RESTException(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__, 405);
	}

	public function put(){
		// このRESTは実行出来ない
		throw new RESTException(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__, 405);
	}

	public function delete(){
		// このRESTは実行出来ない
		throw new RESTException(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__, 405);
	}
}

?>