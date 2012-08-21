<?php
require_once("../../class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start();
$smarty->session_check();

$smarty->assign("menu_is_available", "true");
$smarty->assign("mode", "patient");
$smarty->assign("location", "appointment");
$function_name = "book";
//必須パラメータチェック
if (!isset($_SESSION["person_id"]) ||
        !isset($_SESSION["birthday"]) ||
        !isset($_SESSION["person_name"])) {
    $url = "http://{$_SERVER["HTTP_HOST"]}/immunization/userTop.php";
    header("Location: {$url}");
    exit;
}
$db = $smarty->getDb();
$msg = "";
$clinic_id = $_SESSION["clinic_id"];
$person_id = $_SESSION["person_id"];
$birthday = $_SESSION["birthday"];
$person_name = $_SESSION["person_name"];
//$params = $_POST["medicine_params"];
//echo "params = " . $params . "<BR>";
//echo "clinic_id = " . $clinic_id . "<BR>";
//echo "person_id = " . $person_id . "<BR>";
//echo "person_name = " . $person_name . "<BR>";
//echo "birthday = " . $birthday . "<P>";

$params['book'] = $smarty->getBookParams();
$book_attrs = $params['book']['attribute'];
$book_caption = $params['book']['caption'];
$book_vars_min = $params['book']['vars_min'];
$book_vars_max = $params['book']['vars_max'];
$params['$medicine'] = $smarty->getMedicineName();
$medicine = $params['$medicine']['caption'];
$book_vars = array();
$table_error = array();
$verify = false;

$state_name = array("予約", "接種済み");

for ($cnt = 1; $cnt < count($book_attrs); $cnt++) {
    $table_error[] = false;
    //$clinic_vars[$cnt] = "null";
    if (isset($_POST[$book_attrs[$cnt]])) {
        $book_vars[$cnt] = $_POST[$book_attrs[$cnt]];
        //echo $book_vars[$cnt]."<BR>";
    } else {
		$book_vars[$cnt] = "";
	}
}



$cmd = "";
if (isset($_POST["cmd"])) {
    //echo $_POST["cmd"]."<P>";
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
    for ($cnt = 0; $cnt < count($book_vars); $cnt++) {
        $book_vars[$cnt] = null;
    }
}

if (isset($_POST["medicine_params"])) {
    $tmp_vars = explode("_", $_POST["medicine_params"]);
    $book_vars[1] = $tmp_vars[0];
    $book_vars[2] = $tmp_vars[1];
}
if (isset($_POST["book_params"])) {
    $tmp_vars = explode("_", $_POST["book_params"]);
    $book_vars[1] = $tmp_vars[0];
    $book_vars[2] = $tmp_vars[1];
    $book_vars[3] = $tmp_vars[2];
    $book_vars[4] = $tmp_vars[3];
}

$book_vars[0] = $person_id;

if ($posted_type == "submit" || $posted_type == "verify") {
   
    //// 新規予約の追加
    if ($cmd == "add") {
        if ($posted_type == "verify") {
            $table_error = $smarty->checkInput($book_vars, $book_vars_min, $book_vars_max, $table_error, $function_name);
            $verify = true;
            for ($cnt = 0; $cnt < count($table_error); $cnt++) {
                if ($table_error[$cnt] == "over_flow" || $table_error[$cnt] == "under_flow") {
                    $verify = false;
                    break;
                }
            }
            //print_r($table_error);

            if ($db->verifyBookData($book_vars, "new") == SUCCESS) {
                $verify = false;
                $msg .= "すでに同じ種類の薬で同じ接種回の予約が存在するため予約できません。";
            }
            if ($verify == true) {
                $msg .= "以下の内容で登録します。内容が合っているか今一度確認してください。";
            }
        } else if ($posted_type == "submit") {
			$verify = true;
            
            $ret = $db->insertBookData($book_vars);
            if ($ret) {
                $msg .= "<font color=\"red\">Success</font>: データを登録しました。<P>";
                //print "クエリーが失敗しました。".mysql_error()."<P>";
                //die('クエリーが失敗しました。'.mysql_error());
            } else {
                $msg .= "登録に失敗しました<P>";
                //print "<font color=\"red\">Success</font>: データを登録しました。<P>";
            }
        }
    } else if ($cmd == "update") { // 既存ユーザーのデータ更新
        if ($posted_type == "verify") {
            if ($db->verifyBookData($book_vars, "update") == SUCCESS) {
                $table_error = $smarty->checkInput($book_vars, $book_vars_min, $book_vars_max, $table_error, $function_name);
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
                }
            } else {
                $msg .= "<font color=\"red\">ERROR: データが一致しません。</font>";
                $verify = false;
            }
        } else if ($posted_type == "submit") {
            $verify = true;
           
            $ret = $db->updateBookData($book_vars);
            if ($ret) {
                $msg .= "<font color=\"red\">Success</font>: 以下のデータ更新しました。<P>";
                //print "クエリーが失敗しました。" . mysql_error() . "<P>";
                //die('クエリーが失敗しました。'.mysql_error());
            } else {
                $msg .= "クエリーが失敗しました。" . mysql_error() . "<P>";
                //print "<font color=\"red\">Success</font>: 以下のデータ更新しました。<P>";
            }
        }
    } else if ($cmd == "delete") { // ユーザーデータの削除
        if ($posted_type == "verify") {
            if ($db->verifyBookData($book_vars, "delete") == SUCCESS) {
                $verify = true;
            } else {
                $msg .= "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
                $verify = false;
            }
            if ($verify == true) {
                $msg .= "以下のデータを削除します。本当に削除しても良いか今一度確認してください。";
                $book_vars = $db->getBookData($book_vars);
            }
        }
        if ($posted_type == "submit") {
            $verify = true;

            $ret = $db->deleteBookData($book_vars);
            if ($ret) {
                $msg .=  "<font color=\"red\">Success</font>: 以下のデータを削除しました。<P>";
            } else {
                $msg .= "クエリーが失敗しました。" . mysql_error() . "<P>";
            }
        }
    }
  
}

$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

$table->setCellContents(0, 0, "");
$table->setCellContents(0, 1, "値");
for ($nc = 0; $nc < count($book_attrs); $nc++) {

    $table->setCellContents($nc + 1, 0, $book_caption[$nc]);
    if ($nc != 1 && $nc != 5) {
        // 入力チェック or 入力やりなおし
        if ($verify == false) {
            $err = "";
            if ($table_error[$nc] == "under_flow" || $table_error[$nc] == "over_flow") {
                $err = "<font color=\"red\">" . $book_caption[$nc] . "は" . $book_vars_min[$nc] . "文字以上" . $book_vars_max[$nc] . "文字以下</font>";
            }
            $disable = "";
            if ($nc == 0) {
                $disable = "disabled = \"disabled\"";
            }
            if ($nc != 3) {
                $str = $err . "<input type='text' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' size=50 " . $disable . "/>";
            } else {
                $str = $err . "<input type='text' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' size=50 " . $disable . " onClick=\"cal1.write();\" onChange=\"cal1.getFormValue(); cal1.hide();\"/><div id=\"calid\"></div>";
                //$str = $err . "<input type='text' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' size=50 " . $disable ."/>";
            }
        } else if ($verify == true && $posted_type == "verify") {
            // クエリー実行
            $str = htmlspecialchars($book_vars[$nc], ENT_QUOTES, "UTF-8") . "<input type='hidden' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' />";
        } else {
            $str = htmlspecialchars($book_vars[$nc], ENT_QUOTES, "UTF-8");
        }
    } else {
        $disable = "";
        if ($verify == true) {
            $disable = " disabled";
        }
        if ($nc == 1) {
            if ($verify == true) {
                $id = $book_vars[$nc] - 1;
                $str = $medicine[$id] . "<input type='hidden' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' />";
            } else {
                $str = "<select name=\"immunization_id\">";
                for ($j = 0; $j < count($medicine); $j++) {
                    $selected = "";
                    $id = $j + 1;
                    if ($id == $book_vars[$nc]) {
                        $selected = " selected";
                    }
                    $str .= "<option value=\"" . $id . "\"" . $selected . ">" . $medicine[$j] . "</option>";
                    //echo "<option value=\"".$j."\">".$medicine[$j]."</option>";
                }
                $str .= "</select>";
            }
        } else if ($nc == 5) {
            if ($verify == true) {

                $str = $state_name[$book_vars[$nc]] . "<input type='hidden' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' />";
            } else {
                $str = "<select name=\"state\">";
                for ($j = 0; $j < count($state_name); $j++) {
                    $selected = "";
                    if ($j == $book_vars[$nc]) {
                        $selected = " selected";
                    }
                    $str .= "<option value=\"" . $j . "\"" . $selected . ">" . $state_name[$j] . "</option>";
                }
            }
        }
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
    if ($verify == false && $posted_type == "verify") {
        //echo "test=". $verify ."<P>";
        $msg .= "<font color=\"red\">入力が間違っています</font>";
    }
}

//echo "<form id=\"formid\" action=\"appointment.php\" method=\"POST\">";
//echo $table->toHtml();
//echo "<P>";
$none_check = "";
$add_check = "";
$update_check = "";
$delete_check = "";
if ($verify == false) {
    if (isset($_POST["medicine_params"]) || $cmd == "add") {
        //$none_check = "";
        $add_check = "checked=\"checked\"";
        //$update_check = "";
    } else if (isset($_POST["book_params"]) || $cmd == "update") {
        //$none_check = "";
        //$add_check = "";
        $update_check = "checked=\"checked\"";
	} else if ( $cmd == "delete") {
		$delete_check = "checked=\"checked\"";
    } else {
        $none_check = "checked=\"checked\"";
        //$add_check = "";
        //$update_check = "";
    }
    $check = array();
    $check['none'] = $none_check;
    $check['add'] = $add_check;
    $check['update'] = $update_check;
	$check['delete'] = $delete_check;
    $smarty->assign("check", $check);
	//print_r($check);
/*
    //echo "<input type=\"radio\" name=\"cmd\" value=\"none\" " . $none_check . ">None";
    //echo "<input type=\"radio\" name=\"cmd\" value=\"add\" " . $add_check . " >新規登録";
    //echo "<input type=\"radio\" name=\"cmd\" value=\"update\" " . $update_check . " >更新";
    echo "<input type=\"radio\" name=\"cmd\" value=\"delete\" >削除
";
  */  
}
/*
echo "<P>";
if ($posted_type == "submit") {
    
} else if ($verify == true) {
    if ($cmd == "get") {
        echo "<input type=\"submit\" name=\"cancel\" value=\"戻る\" />
";
    } else {
        echo "<input type=\"hidden\" name=\"cmd\" value=\"" . $cmd . "\" />";
        echo "<input type=\"submit\" name=\"submit\" value=\"実行\" />
";
        echo "<input type=\"submit\" name=\"cancel\" value=\"キャンセル\" />";
    }
} else {
    echo "<input type=\"submit\" name=\"verify\" value=\"確認\" />";
    echo "<input type=\"submit\" name=\"reset\" value=\"リセット\" />";
}
echo "</form>";
*/
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

$smarty->display(TPL_BASE."appointment.tpl");
?>

<?php
/*
function checkInput($vars, $min, $max, $err) {
    for ($cnt = 1; $cnt < count($vars); $cnt++) {
        if ($cnt == 4) {//lot number
            continue;
        }
        if ($min[$cnt] > mb_strlen($vars[$cnt])) {
            //echo "under<P>";
            $err[$cnt] = "under_flow";
        } else if (mb_strlen($vars[$cnt]) > $max[$cnt]) {
            //echo "over<P>";
            $err[$cnt] = "over_flow";
        }
    }
    //print_r($err);
    return $err;
}*/
/*
function bookVerify($vars, $attrs) {
    $str_tmp = "SELECT * FROM book WHERE ";
    $str_tmp .= getVerifyStr($vars, $attrs);
    $result = mysql_query($str_tmp);
    if (!$result) {
        print "クエリーが失敗しました。" . mysql_error() . "</P>";
        return false;
    } else {
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 0) {
            print "<font color=\"red\">Error</font>: データが一致しません。<P>";
            return false;
        }
    }

    return true;
}

function getVerifyStr($vars, $attrs) {
    $tmp = "";
    $limit = 3;
    $last_item = $limit - 1;
    for ($cnt = 0; $cnt < $limit; $cnt++) {
        $tmp .= $attrs[$cnt] . " = '" . $vars[$cnt];
        if ($cnt < $last_item) {
            $tmp .= "' AND ";
        } else {
            $tmp .= "';";
        }
    }
    return $tmp;
}

function getBookData($vars, $attrs) {
    $str_tmp = "SELECT * FROM book WHERE ";
    $str_tmp .= getVerifyStr($vars, $attrs);
    echo $str_tmp;
    $result = mysql_query($str_tmp);
    if (!$result) {
        die('クエリーが失敗しました。' . mysql_error());
    } else {
        ////// 結果の行数を得る
        $num_rows = mysql_num_rows($result);
        echo 'total user number = ' . $num_rows . '<p>';

        while ($row = mysql_fetch_assoc($result)) {
            $tableItem = array();
            for ($cnt = 0; $cnt < count($attrs); $cnt++) {
                $tableItem[] = $row[$attrs[$cnt]];
                echo $row[$attrs[$cnt]];
            }
        }
    }
    return $tableItem;
}*/
?>