<?php

require_once('SQLException.php');

/**
 * DataBase のアクセスラッパークラス
 */
class DataBase {

	/**
	 * 継承クラスのシングルトン一覧を管理
	 */
	private static $singleton = array();

	/**
	 * PDO オブジェクト
	 */
	protected $pdo;

	/**
	 * 更新行の保存
	 */
	private $row_count = 0;
	//dbname
	private $dbname;

	/**
	 * 継承クラスのシングルトンを返す
	 * （※ シングルトンは使いたかったら使ってください）
	 * 
	 * @param string $class 継承クラス名
	 * @return class クラス
	 */
	public static function getInstance($class) {
		if (!array_key_exists($class, self::$singleton)) {
			// 継承クラスのインスタンスを生成
			self::$singleton[$class] = new $class();
		}
		return self::$singleton[$class];
	}

	/**
	 * コンストラクタ
	 *
	 * @param string $dbname 接続先ホスト名
	 */
	protected function __construct($dbname) {

		$this->dbname = $dbname;
		$hostname = "localhost";

		$database_username = "db_user";
		$database_password = "123456";
		try {
			$dsn = 'mysql:dbname=' . $dbname . ';host=' . $hostname;
			$this->pdo = new PDO($dsn, $database_username, $database_password);
			$this->pdo->query('set names utf8');
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			print "エラー!: " . $e->getMessage() . "<br/>";
			die();
		}
		return;
	}

	/**
	 * 参照系クエリを実行する。
	 *
	 * @param string $sql MySQL クエリ
	 * @return array 実行結果の配列
	 */
	protected function query($sql) {
		try {
			$result = $this->pdo->query($sql);
			if ($result === false) {
				throw new SQLException(print_r($this->pdo->errorInfo(), true));
			}
			return $result->fetchAll();
		} catch (PDOException $e) {
			throw new SQLException($e->getMessage());
		}
	}

	/**
	 * トランザクションを開始する。
	 *
	 * @param なし
	 * @return boolean 処理の成否(true:成功/false:失敗)
	 */
	protected function beginTransaction() {
		try {
			$result = $this->pdo->beginTransaction();
			if ($result === false) {
				throw new SQLException(print_r($this->pdo->errorInfo(), true));
			}
			return $result;
		} catch (PDOException $e) {
			throw new SQLException($e->getMessage());
		}
	}

	/**
	 * (トランザクションの)コミットを実行する。
	 *
	 * @param なし
	 * @return boolean 処理の成否(true:成功/false:失敗)
	 */
	protected function commit() {
		try {
			$result = $this->pdo->commit();
			if ($result === false) {
				throw new SQLException(print_r($this->pdo->errorInfo(), true));
			}
			return $result;
		} catch (PDOException $e) {
			throw new SQLException($e->getMessage());
		}
	}

	/**
	 * (トランザクションの)ロールバックを実行する。
	 *
	 * @param なし
	 * @return boolean 処理の成否(true:成功/false:失敗)
	 */
	protected function rollBack() {
		try {
			$result = $this->pdo->rollBack();
			if ($result === false) {
				throw new SQLException(print_r($this->pdo->errorInfo(), true));
			}
			return $result;
		} catch (PDOException $e) {
			throw new SQLException($e->getMessage());
		}
	}

	/**
	 * プリペアドステートメントを生成する。
	 *
	 *
	 * @param String $sql クエリ文字列（プレースホルダ使用）
	 * @return PDOStatement プリペアドステートメント
	 * @throws SQLException
	 */
	protected function prepare($sql) {
		try {
			//SqlExplainer::getInstance()->appendSql($sql, $this->dbname);
			$result = $this->pdo->prepare($sql);
			if ($result === false) {
				throw new SQLException(print_r($this->pdo->errorInfo(), true));
			}
			return $result;
		} catch (PDOException $e) {
			throw new SQLException($e->getMessage());
		}
	}

	/**
	 * 参照系クエリを実行する。（プリペアドステートメント使用）
	 *
	 * @param string $sql MySQL クエリ
	 * @return array 実行結果の配列
	 */
	protected function prepared_query($sth) {
		try {
			$result = $this->execute($sth);
			if ($result === false) {
				throw new SQLException(print_r($this->pdo->errorInfo(), true));
			}
			return $sth->fetchAll();
		} catch (PDOException $e) {
			throw new SQLException($e->getMessage());
		}
	}

	/**
	 * 参照系クエリを実行する。（プリペアドステートメント使用）
	 * 指定したEntityとArrayListクラスに格納して返す。
	 * @param PDOStatement $sth
	 * @param Entity $entity
	 * @param ArrayList $list
	 * @return type
	 * @throws SQLException 
	 */
	protected function prepared_query_fetch_class(PDOStatement $sth, Entity $entity, ArrayList $list) {
		$entityName = get_class($entity);
		unset($entity);
		try {
			$sth->setFetchMode(PDO::FETCH_CLASS, $entityName);
			$result = $this->execute($sth);
			if ($result === FALSE) {
				throw new SQLException(print_r($this->pdo->errorInfo(), TRUE));
			}
			while ($row = $sth->fetch()) {
				$list->append($row);
			}
		} catch (PDOException $e) {
			throw new SQLException($e->getMessage());
		}
		return $list;
	}

	/**
	 * 更新系クエリを実行する。（プリペアドステートメント使用）
	 *
	 *
	 * @param PDOStatement $sth プリペアドステートメント
	 * @return bool true:成功
	 * @throws Exception
	 */
	protected function execute($sth) {
		try {
			$result = $sth->execute();
			if ($result === false) {
				throw new SQLException(print_r($this->pdo->errorInfo(), true));
			}
			$this->row_count = $sth->rowCount();
			return $result;
		} catch (PDOException $e) {
			throw new SQLException($e->getMessage());
		}
	}

	/**
	 * 暗黙の型指定bindValue
	 *
	 * phpの変数型に対応したPDO型でbindする
	 *
	 * @param PDOStatement $sth プリペアドステートメント
	 * @param string $param bind先パラメータ
	 * @param mixed $val bindする値
	 * @return bool true:成功 false:失敗
	 */
	protected function bindValueWithType($sth, $param, $val) {
		//SqlExplainer::getInstance()->appendBindParam($param, $val);
		return $sth->bindValue($param, $val, $this->detectValueType($val));
	}

	/**
	 * 値の型検出を行い、PDOの型定数を返す
	 *
	 * @param mixed $val 対象の値
	 * @return int PDO型定数
	 */
	protected function detectValueType($val) {
		if (is_null($val)) {
			return PDO::PARAM_NULL;
		} else if (is_bool($val)) {
			return PDO::PARAM_BOOL;
		} else if (is_float($val)) {
			return PDO::PARAM_INT;
		} else if (is_int($val)) {
			return PDO::PARAM_INT;
		} else {
			return PDO::PARAM_STR;
		}
	}

	/**
	 * 最後に挿入された行の ID あるいはシーケンスの値を返す
	 * 
	 * @param string IDが返されるべきシーケンスオブジェクト名
	 * @return string $nameがnullの場合はDBに挿入された最後のID
	 *                $nameが指定された場合はシーケンスオブジェクトから取得した最後の値
	 */
	protected function lastInsertId($name = null) {
		return $this->pdo->lastInsertId($name);
	}

	/**
	 * 最後の操作で変更された行数を返す
	 * 
	 * @return int 行数
	 */
	protected function rowCount() {
		return $this->row_count;
	}

	/**
	 * 値を整数型の日時として評価したときのYYYYMMDDHHMISS文字列を返す
	 *
	 * @param int $val
	 * @return string YYYYMMDDHHMISS形式の文字列
	 */
	protected function dateVal($val) {
		return date('YmdHis', intval($val));
	}

}

?>
