<?php

require_once(MAIN_DIR . '/class/DataBase.php');

 final class DBAccessor extends DataBase{
 	/**
	 * シングルトン
	 */
	private static $singleton;

	/**
	 * コンストラクタ
	 */
	public function __construct(){
		parent::__construct(DATABASE_TYPE_IMMUNIZATION);
	}
	/**
	 * シングルトンを取得する。
	 *
	 * @return object BlogSlave
	 */
	static public function getInstance() {
		if (self::$singleton == null) {
			self::$singleton = new self();
		}
		return self::$singleton;
	}

	public function query($sql) {
		return parent::query($sql);
	}
                
        /**
         * メールアドレスからユーザーIDを取得する
         * @param string $mail メールアドレス
         */
        public function getUserIdbyMailAddress($mail){
            $rows = array();
            $sql = <<<QUERY
            SELECT  id
            FROM    user
            WHERE   mail = :mail
QUERY;

            $sth = $this->prepare($sql);
            $this->bindValueWithType($sth, ':mail', $mail);
            $rows = $this->prepared_query($sth);
            return $rows[0];
        }
     /*
      * ユーザーＩＤとパスワードを照合してユーザーかどうか判別する
      */   
	public function verifyUserAccount($id, $pw){
		$sql = <<<SQL
SELECT id
FROM clinic
WHERE clinic_id = :clinic_id
AND passwd = :passwd
SQL;

		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':clinic_id', $id);
		$this->bindValueWithType($sth, ':passwd', $pw);
		$rows = $this->prepared_query($sth);
		
		if($this->rowCount() == 1){
			//正規ユーザー
		} elseif($this->rowCount() > 1){
			//システムエラー
		} else {
			//ユーザーではない
		}
	}
 }
