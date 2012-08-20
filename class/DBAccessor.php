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
	 * 病院データの一覧を取得する
	 */

	public function getClinicList() {
		$sql = <<<SQL
SELECT * 
FROM clinic
SQL;
		$sth = $this->prepare($sql);
		$rows = $this->prepared_query($sth);

		return $rows;
	}

	/*
	 * 指定病院IDを持つデータが存在するか判別する
	 */

	public function verifyClinicID($id) {
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
	 * 病院ＩＤとパスワードを照合して登録ユーザーかどうか判別する
	 */

	public function verifyClinicIDwithPW($id, $pw) {
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

	public function insertClinicData($input) {
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
	 * 個別の接種期間を登録する
	 */

	public function insertTermData($input) {
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
	 * 病院データを更新する
	 */

	public function updateClinicData($input) {
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

	public function deleteClinicData($input) {
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

	public function getClinicData($input) {
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
	 * 患者データを取得する
	 */

	public function getPatientData($person_id) {
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
	 * 患者データの追加を行う
	 */

	public function insertPatientData($input) {
		$sql = <<<SQL
INSERT INTO person
VALUES (null, :clinic_id, :patient_id, :family_name, 
        :family_name_yomi, :personal_name, :personal_name_yomi, 
        :birthday, :zipcode, :location1, :location2, :tel, :email)
SQL;

		$sth = $this->prepare($sql);

		//$this->bindValueWithType($sth, ':clinic_id', $input[0]);
		$this->bindValueWithType($sth, ':clinic_id', $input[1]);
		$this->bindValueWithType($sth, ':patient_id', $input[2]);
		$this->bindValueWithType($sth, ':family_name', $input[3]);
		$this->bindValueWithType($sth, ':family_name_yomi', $input[4]);
		$this->bindValueWithType($sth, ':personal_name', $input[5]);
		$this->bindValueWithType($sth, ':personal_name_yomi', $input[6]);
		$this->bindValueWithType($sth, ':birthday', $input[7]);
		$this->bindValueWithType($sth, ':zipcode', $input[8]);
		$this->bindValueWithType($sth, ':location1', $input[9]);
		$this->bindValueWithType($sth, ':location2', $input[10]);
		$this->bindValueWithType($sth, ':tel', $input[11]);
		$this->bindValueWithType($sth, ':email', $input[12]);
		//$rows = $this->execute($sth);

		return $this->execute($sth);
	}

	/*
	 * 患者データの更新を行う
	 */

	public function updatePatientData($input) {
		$sql = <<<SQL
UPDATE person
SET patient_id = :patient_id, 
    family_name = :family_name, family_name_yomi = :family_name_yomi, 
    personal_name = :personal_name, personal_name_yomi = :personal_name_yomi, 
    birthday = :birthday, zipcode = :zipcode, location1 = :location1, 
    location2 = :location2, tel = :tel, email = :email
WHERE person_id = :person_id
SQL;

		$sth = $this->prepare($sql);

		$this->bindValueWithType($sth, ':person_id', $input[0]);
		//$this->bindValueWithType($sth, ':clinic_id', $input[1]);
		$this->bindValueWithType($sth, ':patient_id', $input[2]);
		$this->bindValueWithType($sth, ':family_name', $input[3]);
		$this->bindValueWithType($sth, ':family_name_yomi', $input[4]);
		$this->bindValueWithType($sth, ':personal_name', $input[5]);
		$this->bindValueWithType($sth, ':personal_name_yomi', $input[6]);
		$this->bindValueWithType($sth, ':birthday', $input[7]);
		$this->bindValueWithType($sth, ':zipcode', $input[8]);
		$this->bindValueWithType($sth, ':location1', $input[9]);
		$this->bindValueWithType($sth, ':location2', $input[10]);
		$this->bindValueWithType($sth, ':tel', $input[11]);
		$this->bindValueWithType($sth, ':email', $input[12]);
		//$rows = $this->execute($sth);

		return $this->execute($sth);
	}

	/*
	 * 患者データの削除
	 */

	public function deletePatientData($input) {
		$sql = <<<SQL
DELETE 
FROM person 
WHERE person_id = :person_id
SQL;

		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':person_id', $input[0]);
		//$rows = $this->prepared_query($sth);

		return $this->execute($sth);
	}

	/*
	 * 患者データの検索
	 */

	public function searchPatientData($attr, $var) {
		$item = 0;
		$where = "";
		for ($cnt = 0; $cnt < count($attr); $cnt++) {
			if ($var[$cnt] != "") {
				if ($item == 0) {
					$where .= " WHERE ";
				} else {
					$where .= " AND ";
				}
				$where .= $attr[$cnt] . " = :" . $attr[$cnt];

				$item++;
			}
		}
		$sql = <<<SQL
SELECT * 
FROM person
{$where}
SQL;

		$sth = $this->prepare($sql);
		for ($cnt = 0; $cnt < count($attr); $cnt++) {
			if ($var[$cnt] != "") {
				$name = ":" . $attr[$cnt];
				$this->bindValueWithType($sth, $name, $var[$cnt]);
			}
		}
		$rows = $this->prepared_query($sth);

		return $rows;
	}

	/*
	 * 指定ＩＤの患者データが存在するか確認する
	 */

	public function verifyPatientID($input) {
		$sql = <<<SQL
SELECT *
FROM person 
WHERE person_id = :person_id
AND clinic_id = :clinic_id
SQL;

		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':person_id', $input[0]);
		$this->bindValueWithType($sth, ':clinic_id', $input[1]);
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
	 * 予防接種IDと回数のリストを取得する
	 */

	public function getImmunizationIDList() {
		$sql = <<<SQL
SELECT immunization_id, frequency 
FROM immunization
order by getImmunizationIDList
SQL;

		$sth = $this->prepare($sql);
		$rows = $this->prepared_query($sth);

		return $rows;
	}

	/*
	 * 指定IDの期間設定を取得する
	 */

	public function getTermSetting($id) {
		$sql = <<<SQL
SELECT *
FROM immunization_term 
WHERE clinic_id = :clinic_id
SQL;

		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':clinic_id', $id);
		$rows = $this->prepared_query($sth);

		return $rows;
	}

	/*
	 * 接種期間を更新する
	 */

	public function updateTermSetting($start, $enable, $clinic_id, $immunization_id, $times) {
		$sql = <<<SQL
UPDATE immunization_term 
SET term_start = :term_start, is_enable = :is_enable
WHERE clinic_id = :clinic_id
AND immunization_id = :immunization_id
AND times = :times
SQL;

		$sth = $this->prepare($sql);
		$this->bindValueWithType($sth, ':term_start', $start);
		$this->bindValueWithType($sth, ':is_enable', $enable);
		$this->bindValueWithType($sth, ':clinic_id', $clinic_id);
		$this->bindValueWithType($sth, ':immunization_id', $immunization_id);
		$this->bindValueWithType($sth, ':times', $times);

		return $this->execute($sth);
	}

}
