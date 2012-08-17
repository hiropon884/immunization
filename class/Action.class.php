<?php

/**
 * アクションクラス
 */
require_once(dirname(__FILE__) . '/../../conf/main_conf.php');
require_once(dirname(__FILE__) . '/../../conf/include_conf.php');
require_once(dirname(__FILE__) . '/../../conf/ppv_conf.php');

require_once(MAIN_DIR . '/class/util.php');
require_once(MAIN_DIR . '/class/ppv_util.php');

require_once(COMMON_DIR . '/lib/niwavide.env.php');
require_once(COMMON_DIR . '/lib/niwavide.status.php');
require_once(COMMON_DIR . '/lib/niwavide_cht.util.php');

require_once(MAIN_DIR . '/class/base/exception/SessionNotFoundException.class.php');
require_once(MAIN_DIR . '/class/base/SqlExplainer.class.php');

require_once(SMARTY_PATH);

abstract class Action {

	/**
	 * 継承クラスのシングルトンを管理
	 */
	private static $singleton;

	/**
	 * Smarty インスタンス
	 */
	protected $smarty;

	/**
	 * コンフィグファイル名
	 */
	private $config;

	/**
	 * テンプレートファイル名
	 */
	private $template;

	/**
	 * チャンネル ID
	 */
	protected $channel_id;

	/**
	 * セッション情報
	 */
	protected $session = array();

	/**
	 * dev環境か否か
	 */
	protected $is_dev = false;

	/**
	 * test環境か否か
	 */
	protected $is_test = false;

	/**
	 * 継承クラスのシングルトンを返す
	 * （※ シングルトンは使いたかったら使ってください）
	 * 
	 * 
	 * @param string $class 継承クラス名
	 * @return class クラス
	 */
	public static function getInstance($class) {
		if (self::$singleton == null) {
			// 継承クラスのインスタンスを生成
			self::$singleton = new $class();
		}
		return self::$singleton;
	}

	/**
	 * ページの初期化
	 */
	public function initialize() {
		// Smarty 初期化
		$this->smarty = new Smarty();
		$this->smarty->config_overwrite = false;
		$this->smarty->template_dir = CHT_SMARTY_TEMPLATE_DIR;
		$this->smarty->compile_dir = CHT_SMARTY_COMPILE_DIR;
		$this->smarty->plugins_dir[] = CHT_SMARTY_PLUGIN_DIR;
		/*
		 * 課金系のメンテナンス中の場合はその旨のページを表示して終了
		 * (メンテナンス時は内部で exit が呼ばれるために返ってこない)
		 */
		$util = new util();
		$util->checkPaymentMaintenance($this->smarty, 'channel/pay_maintenance.tpl');

		// セッション情報取得
		$session = cht_get_session($_REQUEST['channel_id']);
		if (!$session || !isset($session)) {
			throw new SessionNotFoundException();
		}
		cht_assign_smarty_default_parameter($this->smarty, $session, $_REQUEST['channel_id']);

		// dev環境か否か
		$host = php_uname('n');
		if ($host == "ch-tool-dev") {
			$this->is_dev = true;
		}
		// test環境か否か
		if ($host == "ch-tool-test") {
			$this->is_test = true;
		}

		// チャンネルID、セッションを格納
		$this->channel_id = $_REQUEST['channel_id'];
		$this->session = $session;
	}

	/**
	 * 各ページ固有の設定ファイルを指定する。
	 * 
	 * @param string $config 設定ファイル名
	 */
	public function setConfig($config) {
		$this->config = TPL_CONF_DIR . $config;
	}

	/**
	 * 各ページ固有のテンプレートを指定する。
	 *
	 * @param string $template テンプレートファイル名
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}

	/**
	 * Smarty::assign() のショートカット
	 *
	 * @param string $key テンプレート変数名 arrayで渡した場合は配列のキー名と値がペアでアサインされる。
	 * @param mixed	$value テンプレート変数の値
	 */
	public function assign($key, $value = null) {
		if (is_array($key)) {
			$this->smarty->assign($this->escape($key));
		} else {
			$this->smarty->assign($key, $this->escape($value));
		}
	}

	/**
	 * Smarty::assign() のショートカット（エスケープなし）
	 *
	 * @param string $varname テンプレート変数名
	 * @param mixed	$var テンプレート変数の値
	 */
	public function assignWithoutEscape($key, $value = null) {
		if (is_array($key)) {
			$this->smarty->assign($key);
		} else {
			$this->smarty->assign($key, $value);
		}
	}

	/**
	 * エスケープ処理を行う
	 */
	private function escape($param) {
		if (is_array($param)) {
			return array_map(array($this, 'escape'), $param);
		} else {
			return htmlspecialchars($param, ENT_QUOTES);
		}
	}

	/**
	 * ページ表示
	 */
	public function start() {
		try {
			// 初期化
			$this->initialize();
			// 処理
			$this->run();
			$this->assign('explain_sql', SqlExplainer::getInstance()->exeExplain()->getExplainResult());
		} catch (Exception $e) {
			// エラー
			$this->error($e);
		}
		// ページ表示
		$this->display();
	}

	/**
	 * ページ表示(chbase.tplのdisplayなし)
	 */
	public function startWithFetch() {
		try {
			$this->initialize();
			$this->run();
			$this->assign('explain_sql', SqlExplainer::getInstance()->exeExplain()->getExplainResult());
		} catch (Exception $e) {
			$this->error($e);
		}
		echo $this->fetch();
	}

	/**
	 * 各ページの処理を実装
	 */
	abstract public function run();

	/**
	 * エラー設定
	 */
	public function error($e) {
		// エラーページを設定
		$this->setConfig('/error/error.conf');
		$this->setTemplate('error/error.tpl');
		if ($e instanceof IllegalParameterException) {
			$this->assign('error_type', 'PARAMETER_WRONG');
		} else if ($e instanceof SessionNotFoundException) {
			$this->assign('error_type', 'SESSION_MISSING');
		} else if ($e instanceof SQLException) {
			$this->assign('error_type', 'DB_FAILED');
		}
	}

	/**
	 * CHツールページ表示
	 */
	public final function display($template = false) {
		// CONFファイルの読み込み
		if (isset($this->config)) {
			$this->smarty->config_load($this->config);
			// CONFファイル内の変数をアサイン
			foreach ($this->smarty->get_config_vars() as $key => $value) {
				$this->assign($key, $value);
			}
		}
		$this->assign('content_session', $this->session);
		$this->assign('content_channel_id', $this->channel_id);
		$this->assign('content_tpl', $template ? $template : $this->template);
		$this->smarty->display('ctbase.tpl');
		die;
	}

	/**
	 * 指定のテンプレートを出力
	 */
	public final function fetch($template = false) {
		// CONFファイルの読み込み
		if (isset($this->config)) {
			$this->smarty->config_load($this->config);
			// CONFファイル内の変数をアサイン
			foreach ($this->smarty->get_config_vars() as $key => $value) {
				$this->assign($key, $value);
			}
		}
		$this->assign('content_session', $this->session);
		$this->assign('content_channel_id', $this->channel_id);
		return $this->smarty->fetch($template ? $template : $this->template);
	}

}

?>
