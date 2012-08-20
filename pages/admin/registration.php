<?php
require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start();
//$smarty->session_check();

$smarty->assign("menu_is_available", "true");
$smarty->assign("mode", "admin");
$smarty->assign("location", "registration");

$db = $smarty->getDb();
$params = $smarty->getClinicParams();
$clinic_attribute = $params['clinic']['attribute'];
$clinic_caption = $params['clinic']['caption'];
$clinic_vars_min = $params['clinic']['vars_min'];
$clinic_vars_max = $params['clinic']['vars_max'];

$clinic_vars = array();
$table_error = array();
$verify = false;

for ($cnt = 0; $cnt < count($clinic_attribute); $cnt++) {
	$table_error[] = false;
	//$clinic_vars[$cnt] = "null";
	if (isset($_POST[$clinic_attribute[$cnt]])) {
		$clinic_vars[$cnt] = $_POST[$clinic_attribute[$cnt]];
	} else {
		$clinic_vars[$cnt] = "";
	}
}
//print_r($clinic_vars);

$cmd = "";
if (isset($_POST["cmd"])) {
	//echo $_POST["cmd"] . "<P>";
	$cmd = $_POST["cmd"];
} else {
	$cmd = "none";
}
$posted_type = "";
if (isset($_POST["submit"])) {
    $posted_type = "submit";
} else if (isset($_POST["verify"])) {
    $posted_type = "verify";
} else if (isset($_POST["reset"])) {
     $posted_type = "reset";
	//echo "reset" . "<P>";
	for ($cnt = 0; $cnt < count($clinic_vars); $cnt++) {
		$clinic_vars[$cnt] = null;
	}
} elseif (isset($_POST["cancel"])){
    $posted_type = "cancel";
}

$msg = "";

//var_dump($_POST);
// エラーメッセージを格納する変数を初期化
$error_message = "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
//if ($clinic_vars[0] != "null") {
if ($posted_type == "submit" || $posted_type == "verify") {

	//// 新規ユーザーの追加
	if ($cmd == "add") {
		if ($posted_type == "verify") {
			$table_error = checkInput($clinic_vars, $clinic_vars_min, $clinic_vars_max, $table_error);
			$verify = true;
			for ($cnt = 0; $cnt < count($table_error); $cnt++) {
				if ($table_error[$cnt] == "over_flow" || $table_error[$cnt] == "under_flow") {
					$verify = false;
					break;
				}
			}
			//print_r($table_error);
			if ($verify == true) {
				$msg .= "以下の内容で登録します。内容が合っているか今一度確認してください。";
				try {
					$clinic_vars[0] = $db->getLastClinicID() + 1;
				} catch (PDOException $e) {
					echo $e->getMessage();
					die;
				}
			}
		} else if ($posted_type == "submit") {
			$verify = true;
			try {
				//echo "addNewClinic\n";
				$ret = $db->insertClinicData($clinic_vars);
                                if($ret != true){
                                    $msg .= "<font color=\"red\">Registration Fail</font>: データ登録に失敗しました<P>";
                                }
			} catch (PDOException $e) {
				echo $e->getMessage();
				//echo "addNewClinic\n";
				die;
			}
			//echo "addNewClinic\n";
			$msg .= "<font color=\"red\">Success</font>: データを登録しました<P>";
			//$msg .= "clinic_id = " . $clinic_vars[0] . "<P>";
			
			//msg .= "Success: 接種期間データを登録開始。<P>";
			try{
				$default_term = $db->getDefaultTerm();
				
				foreach($default_term as $val){
					$val['clinic_id'] = $clinic_vars[0];
					$ret = $db->insertTermData($val);
                                         if($ret != true){
                                    $msg .= "<font color=\"red\">Default Term Registration Fail</font>: データ登録に失敗しました<P>";
                                }
				}
			} catch (PDOException $e) {
				echo $e->getMessage();
				//echo "defaultterm\n";
				die;
			}
			$msg .= "Success: 接種期間データを登録完了<P>";
		}
	} else if ($cmd == "update") { // 既存ユーザーのデータ更新
		if ($posted_type == "verify") {

			if ($db->verifyClinicID($clinic_vars[0]) == SUCCESS) {
				if ($db->verifyClinicIDwithPW($clinic_vars[0], $clinic_vars[1]) == SUCCESS) {

					$table_error = checkInput($clinic_vars, $clinic_vars_min, $clinic_vars_max, $table_error);
					$verify = true;
					for ($cnt = 0; $cnt < count($table_error); $cnt++) {
						if ($table_error[$cnt] == "over_flow" || $table_error[$cnt] == "under_flow") {
							$verify = false;
							break;
						}
					}
					//print_r($table_error);
					if ($verify == true) {
						$msg .= "以下の内容でデータを更新します。更新内容が合っているか今一度確認してください。";
						//print "以下の内容でデータを更新します。更新内容が合っているか今一度確認してください。";
					}
				} else {
					//print "<font color=\"red\">ERROR: パスワードが一致しません。</font>";
					$verify = false;
				}
			} else {
				$msg .= "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
				//print "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
				$verify = false;
			}
		} else 	if ($posted_type == "submit") {
			$verify = true;
			try{
				$ret = $db->updateClinicData($clinic_vars);
                                 if($ret != true){
                                    $msg .= "<font color=\"red\">Update Fail</font>: データ更新に失敗しました<P>";
                                }
			} catch (PDOException $e) {
				echo $e->getMessage();
				die;
			}
		
			$msg .= "<font color=\"red\">Success</font>: 以下のデータ更新しました。<P>";
		}
	} else if ($cmd == "delete") { // ユーザーデータの削除
		if ($posted_type == "verify") {
			if ($db->verifyClinicID($clinic_vars[0]) == SUCCESS) {
				if ($db->verifyClinicIDwithPW($clinic_vars[0], $clinic_vars[1]) == SUCCESS) {
					$verify = true;
				} else {
					//print "<font color=\"red\">ERROR: パスワードが一致しません。</font>";
					$verify = false;
				}
			} else {
				$msg .= "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
				$verify = false;
			}
			//print_r($table_error);
			if ($verify == true) {
				//print "以下のデータを削除します。本当に削除しても良いか今一度確認してください。";
				$msg .= "以下のデータを削除します。本当に削除しても良いか今一度確認してください。";
				$clinic_vars = $db->getClinicData($clinic_vars);
			}
		} else 	if ($posted_type == "submit") {
			$verify = true;
			try{
				$ret = $db->deleteClinicData($clinic_vars);
                                  if($ret != true){
                                    $msg .= "<font color=\"red\">Delete Fail</font>: データ削除に失敗しました<P>";
                                }
			} catch (PDOException $e) {
				echo $e->getMessage();
				die;
			}
			
			$msg .= "<font color=\"red\">Success</font>: 以下のデータを削除しました。<P>";
		}
	} else if ($cmd == "get") {
		if ($posted_type == "verify") {
			if ($db->verifyClinicID($clinic_vars[0]) == SUCCESS) {
				if ($db->verifyClinicIDwithPW($clinic_vars[0], $clinic_vars[1]) == SUCCESS) {
					$verify = true;
				} else {
					//print "<font color=\"red\">ERROR: パスワードが一致しません。</font>";
					$verify = false;
				}
			} else {
				//print "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
				$msg .= "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
				$verify = false;
			}
			//print_r($table_error);
			if ($verify == true) {
				//print "以下のデータを取得しました。";
				$msg = "以下のデータを取得しました。";
				//$clinic_vars = getClinicData($clinic_vars[0], $clinic_vars[1], $clinic_attribute);
				$clinic_vars = $db->getClinicData($clinic_vars);
			}
		}
	}
	
}

$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

$table->setCellContents(0, 0, "");
$table->setCellContents(0, 1, "値");

for ($nc = 0; $nc < count($clinic_attribute); $nc++) {
	$table->setCellContents($nc + 1, 0, $clinic_caption[$nc]);
	// 入力チェック or 入力やりなおし
	if ($verify == false) {
		$err = "";
		if ($table_error[$nc] == "under_flow" || $table_error[$nc] == "over_flow") {
			$err = "<font color=\"red\">" . $clinic_caption[$nc] . "は" . $clinic_vars_min[$nc] . "文字以上" . $clinic_vars_max[$nc] . "文字以下</font>";
		}

		$str = $err . "<input type='text' name='" . $clinic_attribute[$nc] . "' value='" . $clinic_vars[$nc] . "' size=50 />";
	} else if ($verify == true && $posted_type == "verify") {
		// クエリー実行
		$str = htmlspecialchars($clinic_vars[$nc], ENT_QUOTES, "UTF-8") . "<input type='hidden' name='" . $clinic_attribute[$nc] . "' value='" . $clinic_vars[$nc] . "' />";
	} else {
		$str = htmlspecialchars($clinic_vars[$nc], ENT_QUOTES, "UTF-8");
	}
	$table->setCellContents($nc + 1, 1, $str);
	if ($nc % 2 == 1) {
		$hrAttrs = array('bgcolor' => 'WhiteSmoke');
	} else {
		$hrAttrs = array('bgcolor' => 'GhostWhite');
	}
	$table->setRowAttributes($nc + 1, $hrAttrs, true);
}
//$altRow = array('bgcolor' => 'lightgray');
//$table->altRowAttributes(0, null, $altRow);

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
if ($cmd != "none") {
	if ($verify == false && $_POST["verify"]) {
		//echo "test=". $verify ."<P>";
		$msg .= "<font color=\"red\">入力が間違っています</font>";
	}
}

//echo "<form action=\"registrationView.php\" method=\"POST\">";
$smarty->assign("table", $table->toHtml());
if ($verify == true) {
	$smarty->assign("verify", "true");
} else {
	$smarty->assign("verify", "false");
}
if ($posted_type == "submit"){
	$smarty->assign("is_submit", "true");
} else {
	$smarty->assign("is_submit", "false");
}
$smarty->assign("cmd",$cmd);
$smarty->assign("msg",$msg);
//echo $table->toHtml();

$smarty->display("tpl/registration.tpl");

function checkInput($vars, $min, $max, $err) {
	for ($cnt = 1; $cnt < count($vars); $cnt++) {
		if ($min[$cnt] > mb_strlen($vars[$cnt])) {
			//echo "under<P>";
			$err[$cnt] = "under_flow";
		} else if (mb_strlen($vars[$cnt]) > $max[$cnt]) {
			//echo "over<P>";
			$err[$cnt] = "over_flow";
		}
	}
	return $err;
}

?>