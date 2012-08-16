<?php

/**
 * 実行したSQLを保存して、exeExplain()でExplainしてくれるクラス
 */
class SqlExplainer {

    static private $singleton;
    private $sqlList;
    private $currentSqlNo;
    private $isEnable;
    private $pdo;

    public function __construct() {
        $this->sqlList = array();
        $this->currentSqlNo = 0;

        $host = php_uname('n');
        if (isset($_COOKIE['dev_debug']) && $_COOKIE['dev_debug'] === "true") {
            $debug = TRUE;
        } else {
            $debug = FALSE;
        }
        if ($host == "ch-tool-dev" && $debug === TRUE) {
            //dev環境かつdebugモードでしか実行しない。本番環境DBでEXPLAINを実行するのはやめておく。
            $this->isEnable = TRUE;
        } else {
            $this->isEnable = FALSE;
        }
    }

    public static function getInstance() {
        if (self::$singleton == null) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    /**
     * SQLを追加
     * SQLを追加した時点でそのSQLに対してBindを実行する。
     * @param string $sqlString 実行するSQL
     * @param string $dbname 接続先DB
     */
    public function appendSql($sqlString, $dbname) {
        if ($this->isEnable) {
            $this->currentSqlNo++;
            //予め先頭の空白を取り除き、SELECT文という判定をしやすくする。
            $sqlString = ltrim($sqlString);
            $sql = new Sql($sqlString, $dbname);
            $this->sqlList[$this->currentSqlNo] = $sql;
        }
    }

    /**
     * SQLにBindするパラメータを追加
     * @param string $bindParam Bind対象の文字列
     * @param mixed $value Bindする値
     */
    public function appendBindParam($bindParam, $value) {
        if ($this->isEnable) {
            $this->sqlList[$this->currentSqlNo]->appendBindParam($bindParam, $value);
        }
    }

    /**
     * Explainを実行する。
     */
    public function exeExplain() {
        if ($this->isEnable) {
            $sqlListWithDbname = $this->getSqllistAssociatedWithDbname($this->sqlList);
            foreach ($sqlListWithDbname as $dbname => $sqlList) {
                $this->createPdo($dbname);
                foreach ($sqlList as $sql) {
                    //SELECT以外にEXPLAINはできない
                    if (stripos($sql->getSqlString(), 'SELECT') !== 0) {
                        continue;
                    }

                    $sth = $this->pdo->prepare('EXPLAIN ' . $sql->getSqlString());
                    foreach ($sql->getBindParam() as $bindParam => $value) {
                        $this->bindValueWithType($sth, $bindParam, $value);
                    }
                    $sth->setFetchMode(PDO::FETCH_ASSOC);
                    $sth->execute();
                    $result = $sth->fetchAll();
                    if (isset($result[0])) {
                        $sql->setExplainResult($result[0]);
                    }
                }
                $this->pdo = NULL;
            }
        }
        return $this;
    }

    /**
     * PDOでDBに接続
     * @param string $dbname DB名
     */
    private function createPdo($dbname) {
        global $nvdb_param;
        global $cht_database_name;
        global $cht_database_username;
        global $cht_database_password;

        $hostname = $nvdb_param[$dbname][php_uname('n')];

        $dsn = 'mysql:dbname=' . $cht_database_name . ';host=' . $hostname;
        $this->pdo = new PDO($dsn, $cht_database_username, $cht_database_password);
        $this->pdo->query('set names utf8');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * DBごとにクエリを整理する関数
     * @param array $sqlList
     * @return array 
     */
    private function getSqllistAssociatedWithDbname(array $sqlList) {
        $resultSqlList = array();
        foreach ($sqlList as $sql) {
            $dbname = $sql->getDbname();
            if (isset($resultSqlList[$dbname]) === FALSE) {
                $resultSqlList[$dbname] = array();
            }
            $resultSqlList[$dbname][] = $sql;
        }
        return $resultSqlList;
    }

    /**
     * 結果をHTMLの表形式で取得
     * @return string 
     */
    public function getResultHtml() {
        $html = '';
        if ($this->isEnable) {
            $html .= '<table>';

            foreach ($this->sqlList as $key => $sql) {
                if ($key % 5 === 1) {
                    $html .= '<tr><th>' . implode('</th><th>', array_keys($sql->getExplainResult())) . '</th></tr>';
                }
                $html .= '<tr><td>' . implode('</td><td>', $sql->getExplainResult()) . '</td></tr>';
                $html .= '<tr><td colspan="' . count($sql->getExplainResult()) . '">' . $sql->getSqlString() . '</td></tr>';
            }

            $html .= '</table>';
        }
        return $html;
    }

    /**
     * Explain結果と元のSQLを含めて取得。
     * @return array 
     */
    public function getExplainResult() {
        $result = NULL;
        if ($this->isEnable) {
            $result = array();
            foreach ($this->sqlList as $sql) {
                $explain = $sql->getExplainResult();
                $explain['SQL'] = $sql->getSqlString();
                $result[] = $explain;
            }
        }
        return $result;
    }

    /**
     * 値をPDOでバインドする関数
     * @param PDOStatement $sth
     * @param type $param
     * @param type $val
     * @return type 
     */
    protected function bindValueWithType(PDOStatement $sth, $param, $val) {
        return $sth->bindValue($param, $val, $this->detectValueType($val));
    }

    /**
     * 値の型検出を行い、PDOの型定数を返す関数
     * @param mixed $val 対象の値
     * @return int PDO型定数
     */
    private function detectValueType($val) {
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

}

/**
 * SQL文格納クラス 
 */
class Sql {

    private $sqlString;
    private $dbname;
    private $bindParam;
    private $explainResult;

    /**
     * SQLを保存する。
     * @param string $sqlString SQL文
     * @param string $dbname DB名
     */
    public function __construct($sqlString, $dbname) {
        $this->sqlString = 'SELECT 1';
        if (is_string($sqlString)) {
            $this->sqlString = $sqlString;
        }
        if (is_string($dbname)) {
            $this->dbname = $dbname;
        }
        $this->bindParam = array();
        $this->explainResult = array();
    }

    /**
     * Bindするパラメータを追加。
     * @param string $bindParam
     * @param mixed $value 
     */
    public function appendBindParam($bindParam, $value) {
        $this->bindParam[$bindParam] = $value;
    }

    public function getSqlString() {
        return $this->sqlString;
    }

    public function getBindParam() {
        return $this->bindParam;
    }

    public function getDbname() {
        return $this->dbname;
    }

    public function setExplainResult(array $result) {
        $this->explainResult = $result;
    }

    public function getExplainResult() {
        return $this->explainResult;
    }

}
