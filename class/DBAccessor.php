<?php
require_once('DataBase.php');

final class DBAccessor extends DataBase {

	/**
	 * シングルトン
	 */
	private static $singleton;

	/**
	 * コンストラクタ
	 */
	public function __construct() {
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

	/*
	 * ユーザーＩＤとパスワードを照合してユーザーかどうか判別する
	 */

	public function verifyUserAccount($id, $pw) {
		$sql = <<<SQL
SELECT clinic_id
FROM clinic
WHERE clinic_id = :clinic_id
AND passwd = :passwd
SQL;

		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':clinic_id', $id);
		$this->bindValueWithType($sth, ':passwd', $pw);
		$rows = $this->prepared_query($sth);

		if ($this->rowCount() == SUCCESS) {
			//正規ユーザー
			return SUCCESS;
		} elseif ($this->rowCount() == FAILURE) {
			//認証失敗
			return FAILURE;
		} else {
			//システムエラー
			return 0;
		}
	}

	/*
	 * 患者データを取得する
	 */

	public function getUserInfo($person_id) {
		$sql = <<<SQL
SELECT * 
FROM person 
WHERE person_id = :person_id
SQL;
		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':person_id', $person_id);
		$rows = $this->prepared_query($sth);

		if ($this->rowCount() == SUCCESS) {
			return $rows[0];
		} else {
			return FAILURE;
		}
	}

	/*
	 * 病院データの一覧を取得する
	 */

	public function getClinic() {
		$sql = <<<SQL
SELECT * 
FROM clinic
SQL;
		$sth = $this->prepare($sql);
		$rows = $this->prepared_query($sth);

		return $rows;
	}

	/*
	 * 患者データの一覧を取得する
	 */

	public function getPatinetList($clinic_id) {
		$sql = <<<SQL
SELECT * 
FROM person 
WHERE clinic_id = :clinic_id
SQL;

		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':clinic_id', $clinic_id);
		$rows = $this->prepared_query($sth);

		return $rows;
	}

	/*
	 * 最新のclinic_idを取得する
	 */

	public function getLastClinicID() {
		$sql = <<<SQL
SELECT clinic_id 
FROM clinic 
ORDER BY clinic_id DESC
SQL;

		$sth = $this->prepare($sql);
		$rows = $this->prepared_query($sth);

		return $rows[0]['clinic_id'];
	}

	/*
	 * 病院データを追加する
	 */

	public function addNewClinic($input) {
		$sql = <<<SQL
INSERT INTO clinic 
VALUES (null, :passwd, :name, :yomi, :zipcode,
		:location1, :location2, :tel, :email);
SQL;
		
		$sth = $this->prepare($sql);
		
		//$this->bindValueWithType($sth, ':clinic_id', $input[0]);
		$this->bindValueWithType($sth, ':passwd', $input[1]);
		$this->bindValueWithType($sth, ':name', $input[2]);
		$this->bindValueWithType($sth, ':yomi', $input[3]);
		$this->bindValueWithType($sth, ':zipcode', $input[4]);
		$this->bindValueWithType($sth, ':location1', $input[5]);
		$this->bindValueWithType($sth, ':location2', $input[6]);
		$this->bindValueWithType($sth, ':tel', $input[7]);
		$this->bindValueWithType($sth, ':email', $input[8]); 
		 
		//$rows = $this->execute($sth);

		return $this->execute($sth);
	}

	/*
	 * 接種期間の初期値を取得する
	 */

	public function getDefaultTerm() {
		$sql = <<<SQL
SELECT * 
FROM immunization_term 
WHERE clinic_id = '-1'
SQL;
		$sth = $this->prepare($sql);
		$rows = $this->prepared_query($sth);

		return $rows;
	}

	/*
	 * 個別の接種期間を登録する
	 */

	public function addImmunizationTerm($input) {
		$sql = <<<SQL
INSERT INTO immunization_term  
VALUES (:clinic_id, :immunization_id, :times,
		:term_start, :term_end, :is_enable)
SQL;
		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':clinic_id', $input[0]);
		$this->bindValueWithType($sth, ':immunization_id', $input[1]);
		$this->bindValueWithType($sth, ':times', $input[2]);
		$this->bindValueWithType($sth, ':term_start', $input[3]);
		$this->bindValueWithType($sth, ':term_end', $input[4]);
		$this->bindValueWithType($sth, ':is_enable', $input[5]);
		//$rows = $this->prepared_query($sth);

		return $this->execute($sth);
	}

	/*
	 * 指定のユーザーIDを持つデータが存在するか判別する
	 */

	public function verifyUserID($id) {
		$sql = <<<SQL
SELECT clinic_id
FROM clinic
WHERE clinic_id = :clinic_id
SQL;

		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':clinic_id', $id);
		$rows = $this->prepared_query($sth);

		if ($this->rowCount() == SUCCESS) {
			//正規ユーザー
			return SUCCESS;
		} elseif ($this->rowCount() == FAILURE) {
			//認証失敗
			return FAILURE;
		} else {
			//システムエラー
			return 0;
		}
	}

	/*
	 * 病院データを更新する
	 */
	public function updateClinicData($input){
		$sql = <<<SQL
UPDATE clinic 
SET passws = :passwd, name = :name, yomi = :yomi,
    zipcode = :zipcode, location1 = :location1, location2 = :location2,
    tel = :tel, email = :email
WHERE clinic_id = :clinic_id
SQL;
		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':clinic_id', $input[0]);
		$this->bindValueWithType($sth, ':passwd', $input[1]);
		$this->bindValueWithType($sth, ':name', $input[2]);
		$this->bindValueWithType($sth, ':yomi', $input[3]);
		$this->bindValueWithType($sth, ':zipcode', $input[4]);
		$this->bindValueWithType($sth, ':location1', $input[5]);
		$this->bindValueWithType($sth, ':location2', $input[6]);
		$this->bindValueWithType($sth, ':tel', $input[7]);
		$this->bindValueWithType($sth, ':email', $input[8]);
		//$rows = $this->prepared_query($sth);
		
		return $this->execute($sth);
	}
	/*
	 * 病院データを削除する
	 */
	public function deleteClinicData($input){
		$sql = <<<SQL
DELETE 
FROM clinic 
WHERE clinic_id = :clinic_id
AND passwd = :passwd;
SQL;
		
		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':clinic_id', $input[0]);
		$this->bindValueWithType($sth, ':passwd', $input[1]);
		//$rows = $this->prepared_query($sth);
		
		return $this->execute($sth);
	}
	/*
	 * 病院データを取得する
	 */
	public function getClinicData($input){
		$sql = <<<SQL
SELECT * 
FROM clinic
WHERE clinic_id = :clinic_id
AND passwd = :passwd
SQL;
		
		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':clinic_id', $input[0]);
		$this->bindValueWithType($sth, ':passwd', $input[1]);
		$rows = $this->prepared_query($sth);
		
		return $rows[0];
	}
}
