<?php

require_once(MAIN_DIR . '/class/DataBase.php');

 final class DBAccessor extends DataBase{
 	/**
	 * �V���O���g��
	 */
	private static $singleton;

	/**
	 * �R���X�g���N�^
	 */
	public function __construct(){
		parent::__construct(DATABASE_TYPE_IMMUNIZATION);
	}
	/**
	 * �V���O���g�����擾����B
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
         * ���[���A�h���X���烆�[�U�[ID���擾����
         * @param string $mail ���[���A�h���X
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
      * ���[�U�[�h�c�ƃp�X���[�h���ƍ����ă��[�U�[���ǂ������ʂ���
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
			//���K���[�U�[
		} elseif($this->rowCount() > 1){
			//�V�X�e���G���[
		} else {
			//���[�U�[�ł͂Ȃ�
		}
	}
 }
