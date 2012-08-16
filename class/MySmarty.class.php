<?php
//require_once("DB.php");
define('SMARTY_DIR', '/var/www/share/lib/Smarty-3.1.11/libs/');
define('DOC_ROOT','/var/www/');
define('DATABASE_TYPE_IMMUNIZATION','immunization');
define('SUCCESS','1');
define('FAILURE','0');

require_once(SMARTY_DIR . "Smarty.class.php");
require_once("DBAccessor.php");

class MySmarty extends Smarty{
  private $mysql;
  private $is_db;
  public function __construct($db_flag = false){
    parent::__construct();

    $this->template_dir = DOC_ROOT."smarty/templates";
    $this->compile_dir = DOC_ROOT."smarty/templates_c";
    $this->config_dir = DOC_ROOT."smarty/configs";
    $this->cache_dir = DOC_ROOT."smarty/cache";
    //$this->mysql = mysql_connect('localhost', 'db_user', '123456');
	$this->is_db = $db_flag;
	if($this->is_db){
		$this->mysql = DBAccessor::getInstance();
	}
  }
   
  public function __destruct(){
    //$this->_db->disconnect();
  }
  public function getDb(){
    return $this->mysql;
  }
}
?>