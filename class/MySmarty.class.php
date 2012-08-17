<?php

ini_set('display_errors', 1);

define('SMARTY_DIR', '/var/www/share/lib/Smarty-3.1.11/libs/');
define('DOC_ROOT', '/var/www/');
define('DATABASE_TYPE_IMMUNIZATION', 'immunization');
define('SUCCESS', '1');
define('FAILURE', '0');

require_once(SMARTY_DIR . "Smarty.class.php");
require_once("DBAccessor.php");

class MySmarty extends Smarty {

	private $mysql;
	private $is_db;
	private $params;

	public function __construct($db_flag = false) {
		parent::__construct();

		$this->template_dir = DOC_ROOT . "smarty/templates";
		$this->compile_dir = DOC_ROOT . "smarty/templates_c";
		$this->config_dir = DOC_ROOT . "smarty/configs";
		$this->cache_dir = DOC_ROOT . "smarty/cache";
		//$this->mysql = mysql_connect('localhost', 'db_user', '123456');
		$this->is_db = $db_flag;
		if ($this->is_db) {
			$this->mysql = DBAccessor::getInstance();
		}
		$this->setParams();
	}

	public function __destruct() {
		//$this->_db->disconnect();
	}

	public function getDb() {
		return $this->mysql;
	}

	public function session_check() {
		// ログイン済みかどうかの変数チェックを行う
		if (!isset($_SESSION["clinic_id"])) {
			// 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
			$no_login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/login.php";
			header("Location: {$no_login_url}");
			exit;
		}
	}

	public function setParams() {
		
		$this->params = array();

		//病院パラメータを設定
		$ary = array();
		$ary['attribute'] = array("clinic_id", "passwd", "name", "yomi", "zipcode",
			"location1", "location2", "tel", "email");
		$ary['caption'] = array("病院ID", "パスワード", "病院名", "病院名（読み）",
			"郵便番号", "住所１", "住所２",
			"電話番号", "メールアドレス",);
		$ary['vars_min'] = array(1, 8, 1, 1, 8, 1, 1, 12, 1);
		$ary['vars_max'] = array(10, 20, 50, 100, 8, 255, 255, 13, 50);
		$this->params['clinic'] = $ary;
		 
	}

}

?>