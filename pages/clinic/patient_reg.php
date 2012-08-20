<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start();
$smarty->session_check();

$smarty->assign("menu_is_available", "true");
$smarty->assign("mode", "clinic");
$smarty->assign("location", "patient_reg");

$db = $smarty->getDb();
$params = $smarty->getParams();
$patient_attribute = $params['patient']['attribute'];
$patient_caption = $params['patient']['caption'];
$patient_vars_min = $params['patient']['vars_min'];
$patient_vars_max = $params['patient']['vars_max'];
/*
  // ログイン済みかどうかの変数チェックを行う
  if (!isset($_SESSION["clinic_id"])) {

  // 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
  $no_login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/login.php";
  header("Location: {$no_login_url}");
  exit;
  }

  $patient_attribute = array("person_id", "clinic_id", "patient_id",
  "family_name", "family_name_yomi", "personal_name",
  "personal_name_yomi", "birthday", "zipcode",
  "location1", "location2", "tel", "email");
  $patient_caption = array("人ID","病院ID", "患者ID", "氏", "氏（読み）","名",
  "名（読み）", "生年月日", "郵便番号", "住所１", "住所２",
  "電話番号", "メールアドレス");
  $patient_vars_min = array(1, 1, 1, 1, 1, 1, 1, 10, 8, 1, 1, 12 ,1);
  $patient_vars_max = array(10, 10, 20, 10, 20, 10, 20, 10, 8, 255, 255, 13, 50);
 */
$patient_vars = array();
$table_error = array();
$verify = false;

$posted_item_num = 0;
for ($cnt = 0; $cnt < count($patient_attribute); $cnt++) {
    $table_error[] = false;
    //$patient_vars[$cnt] = "null";
    if (isset($_POST[$patient_attribute[$cnt]])) {
        $posted_item_num++;
        $patient_vars[$cnt] = $_POST[$patient_attribute[$cnt]];
    } else {
        $patient_vars[$cnt] = "";
    }
}
//print_r($patient_vars);
$patient_vars[1] = $_SESSION["clinic_id"];

$cmd = "";
if (isset($_POST["cmd"])) {
    $cmd = $_POST["cmd"];
} else {
    $cmd = "none";
}
//echo "cmd = " . $_POST["cmd"]."<P>";
/*
  if (isset($_POST["submit"])) {
  echo "submit"."<P>";
  }
  if (isset($_POST["verify"])) {
  echo "verify"."<P>";
  }
  if (isset($_POST["reset"])) {
  echo "reset"."<P>";
  for ($cnt = 0; $cnt < count($patient_vars); $cnt++) {
  $patient_vars[$cnt] = null;
  }
  } */

$posted_type = "";
if (isset($_POST["submit"])) {
    $posted_type = "submit";
} else if (isset($_POST["verify"])) {
    $posted_type = "verify";
} else if (isset($_POST["reset"])) {
    $posted_type = "reset";
    //echo "reset" . "<P>";
    for ($cnt = 0; $cnt < count($patient_vars); $cnt++) {
        $patient_vars[$cnt] = null;
    }
} elseif (isset($_POST["cancel"])) {
    $posted_type = "cancel";
}
$msg = "";
//var_dump($_POST);
// エラーメッセージを格納する変数を初期化
$error_message = "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
//if ($patient_vars[0] != "null") {
//if ($_POST["submit"] ||$_POST["verify"] ) {
if ($posted_type == "submit" || $posted_type == "verify") {
    /*
      //SQLサーバーへ接続
      //$link = mysql_connect('localhost', 'root', 'admin');
      $link = mysql_connect('localhost', 'db_user', '123456');
      if (!$link) {
      die('接続失敗です。'.mysql_error());
      }
      print('<p>接続に成功しました。</p>');

      // MySQLに対する処理
      //// テーブルへ接続
      $db_selected = mysql_select_db('immunization', $link);
      if (!$db_selected){
      die('データベース選択失敗です。'.mysql_error());
      }
      print('<p>immunization データベースを選択しました。</p>');

      //// 文字コード設定
      mysql_set_charset('utf8');
     */
    //// 新規ユーザーの追加
    if ($cmd == "add") {
        if ($posted_type == "verify") {
            $table_error = checkInput($patient_vars, $patient_vars_min, $patient_vars_max, $table_error);
            $verify = true;
            for ($cnt = 0; $cnt < count($table_error); $cnt++) {
                if ($table_error[$cnt] == "over_flow" || $table_error[$cnt] == "under_flow") {
                    $verify = false;
                    break;
                }
            }
            //print_r($table_error);
            if ($verify == true) {
                //print "以下の内容で登録します。内容が合っているか今一度確認してください。";
                $msg .= "以下の内容で登録します。内容が合っているか今一度確認してください。";
            }
        } else if ($posted_type == "submit") {
            $verify = true;
            /*
              $str = "INSERT INTO person VALUES (null, '";
              $last_item = count($patient_attribute) -1;
              for ($cnt = 1; $cnt < count($patient_attribute); $cnt++) {
              if($cnt == $last_item){
              $str .= $patient_vars[$cnt] . "');";
              } else {
              $str .= $patient_vars[$cnt] . "','";
              }
              }
              //$str = "INSERT INTO person VALUES (null, '" . $passwd . "','" . $name . "','" . $yomi . "','" . $email . "','" . $zipcode . "','" . $location1 . "','" . $location2 . "','" . $tel . "');";
              print $str."<P>";
              $result = mysql_query($str);
              if (!$result) {
              print "クエリーが失敗しました。".mysql_error()."<P>";
              //die('クエリーが失敗しました。'.mysql_error());
              } else {
              print "<font color=\"red\">Success</font>: データを登録しました。<P>";
              }
             * 
             */
            try {
                $ret = $db->insertPatientData($patient_vars);
                if ($ret != true) {
                    $msg .= "<font color=\"red\">Registration Fail</font>: データ登録に失敗しました<P>";
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
                die;
            }
        }
    } else if ($cmd == "update") { // 既存ユーザーのデータ更新
        if ($posted_type == "verify") {

            // if (userIdVerify($patient_vars[0])) {
            //    if (passwordVerify($patient_vars[0], $patient_vars[1])) {
            if ($db->verifyClinicID($patient_vars[0]) == SUCCESS) {
                if ($db->verifyClinicIDwithPW($patient_vars[0], $patient_vars[1]) == SUCCESS) {
                    $table_error = checkInput($patient_vars, $patient_vars_min, $patient_vars_max, $table_error);
                    $verify = true;
                    for ($cnt = 0; $cnt < count($table_error); $cnt++) {
                        if ($table_error[$cnt] == "over_flow" || $table_error[$cnt] == "under_flow") {
                            $verify = false;
                            break;
                        }
                    }
                    //print_r($table_error);
                    if ($verify == true) {
                        // print "以下の内容でデータを更新します。更新内容が合っているか今一度確認してください。";
                        $msg .= "以下の内容でデータを更新します。更新内容が合っているか今一度確認してください。";
                    }
                } else {
                    //print "<font color=\"red\">ERROR: パスワードが一致しません。</font>";
                    $verify = false;
                }
            } else {
                //print "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
                $msg .= "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
                $verify = false;
            }
        } else if ($posted_type == "submit") {
            $verify = true;
            /*
              $str = "UPDATE person SET ";
              $last_item = count($patient_attribute) - 1;
              for ($cnt = 1; $cnt < count($patient_attribute); $cnt++) {
              if ($cnt == $last_item) {
              $str .= $patient_attribute[$cnt] . "='" . $patient_vars[$cnt] . "'";
              } else {
              $str .= $patient_attribute[$cnt] . "='" . $patient_vars[$cnt] . "', ";
              }
              }
              $str .= " WHERE " . $patient_attribute[0] . "=" . $patient_vars[0] . ";";
              //$str = "UPDATE person SET passwd='" . $passwd . "', name='" . $name . "', yomi='" . $yomi . "', email='" . $email . "', zipcode='" . $zipcode . "', location1='" . $location1 . "', location2='" . $location2 . "', tel='" . $tel . "' WHERE patient_id=" . $patient_id . ";";
              print $str . "<P>";
              $result = mysql_query($str);
              if (!$result) {
              print "クエリーが失敗しました。" . mysql_error() . "<P>";
              //die('クエリーが失敗しました。'.mysql_error());
              } else {
              print "<font color=\"red\">Success</font>: 以下のデータ更新しました。<P>";
              } */
            try {
                $ret = $db->updatePatientData($patient_vars);
                if ($ret != true) {
                    $msg .= "<font color=\"red\">Update Fail</font>: データ更新に失敗しました<P>";
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
                die;
            }
        }
    } else if ($cmd == "delete") { // ユーザーデータの削除
        if ($posted_type == "verify") {
            //if (userIdVerify($patient_vars[0])) {
            //  if (passwordVerify($patient_vars[0], $patient_vars[1])) {
            if ($db->verifyClinicID($patient_vars[0]) == SUCCESS) {
                if ($db->verifyClinicIDwithPW($patient_vars[0], $patient_vars[1]) == SUCCESS) {
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
                //print "以下のデータを削除します。本当に削除しても良いか今一度確認してください。";
                $msg .= "以下のデータを削除します。本当に削除しても良いか今一度確認してください。";
                $patient_vars = getPatientData($patient_vars[0]);
            }
        }
        if ($posted_type == "submit") {
            $verify = true;
            /*
              $str = "DELETE FROM person WHERE " . $patient_attribute[0] . " = '" . $patient_vars[0] . "' AND " . $patient_attribute[1] . " = '" . $patient_vars[1] . "'";
              $result = mysql_query($str);
              //$result = mysql_query("DELETE FROM person WHERE patient_id = '$patient_id' AND passwd = '$passwd'");
              if (!$result) {
              //die('クエリーが失敗しました。'.mysql_error());
              print "クエリーが失敗しました。" . mysql_error() . "<P>";
              } else {
              print "<font color=\"red\">Success</font>: 以下のデータを削除しました。<P>";
              } */
            try {
                $ret = $db->deletePatientData($patient_vars);
                if ($ret != true) {
                    $msg .= "<font color=\"red\">Update Fail</font>: データ削除に失敗しました<P>";
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
                die;
            }
        }
    } else if ($cmd == "get") { // ユーザーデータの取得
        if ($posted_type == "verify") {
            //if (userIdVerify($patient_vars[0])) {
            //  if (passwordVerify($patient_vars[0], $patient_vars[1])) {
            if ($db->verifyClinicID($patient_vars[0]) == SUCCESS) {
                if ($db->verifyClinicIDwithPW($patient_vars[0], $patient_vars[1]) == SUCCESS) {
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
                $msg .= "以下のデータを取得しました。";
                $patient_vars = getPatientData($patient_vars[0]);
            }
        }
    } else if ($cmd == "search") { // ユーザーデータの検索
        if ($posted_item_num > 0) {
            /*
              $str = "SELECT * FROM person";
              $write_num = 0;
              for ($cnt = 0; $cnt < count($patient_attribute); $cnt++) {
              if ($patient_vars[$cnt] != "") {
              if ($write_num == 0) {
              $str .= " WHERE ";
              } else {
              $str .= " AND ";
              }
              if ($cnt > 1) {
              $str .= $patient_attribute[$cnt] . " = \"" . $patient_vars[$cnt] . "\"";
              } else {
              $str .= $patient_attribute[$cnt] . " = " . $patient_vars[$cnt];
              }
              $write_num++;
              }
              }
              //echo $str . "<P>";
              $result = mysql_query($str);
              if (!$result) {
              die('クエリーが失敗しました。' . mysql_error());
              } else {
              ////// 結果の行数を得る
              $num_rows = mysql_num_rows($result);
              echo 'total user numbera = ' . $num_rows . '<p>';
              if ($num_rows > 0) {
              $tableData = array();

              while ($row = mysql_fetch_assoc($result)) {
              $tableItem = array();
              for ($cnt = 0; $cnt < count($patient_attribute); $cnt++) {
              $tableItem[] = $row[$patient_attribute[$cnt]];
              }

              $tableData[] = $tableItem;
              }
             */
            $tableData = $db->searchPatientData($patient_attribute, $patient_vars);

            if (count($tableData) > 0) {
                $attrs = array('width' => '800');
                $table = new HTML_Table($attrs);
                $table->setAutoGrow(true);
                //$table->setAutoFill('n/a');
                //echo "<form action=\"patient_top.php\" method=\"POST\">";

                for ($nr = 0; $nr < count($tableData); $nr++) {
                    //echo $tableData[$nr][1]."<P>";
                    //$table->setHeaderContents($nr+1, 1, $tableData[$nr][0]);
                    $str = "<button type=\"submit\" name=\"person_id\" value=\"" . $tableData[$nr][0] . "\">選択</button>";
                    //$str = "<input type=\"submit\" name=\"patient_id\" value=\"" . $tableData[$nr][0] ."\">";
                    //$str = "<A href=patient_top.php>aaaa</a>";
                    $table->setCellContents($nr + 1, 0, $str);
                    for ($i = 0; $i < count($tableData[$nr]); $i++) {
                        //echo $tableData[$nr][$i]." <P>";
                        if ('' != $tableData[$nr][$i]) {
                            $table->setCellContents($nr + 1, $i + 1, htmlspecialchars($tableData[$nr][$i], ENT_QUOTES, 'UTF-8'));
                        }
                    }
                }

                $altRow = array('bgcolor' => 'lightgray');
                $table->altRowAttributes(1, null, $altRow);

                for ($cnt = 0; $cnt < count($patient_caption); $cnt++) {
                    $table->setHeaderContents(0, $cnt + 1, $patient_caption[$cnt]);
                }

                $hrAttrs = array('bgcolor' => 'silver');
                $table->setRowAttributes(0, $hrAttrs, true);
                $table->setColAttributes(0, $hrAttrs);

                //echo $table->toHtml();
                //echo "</form>";

                $smarty->assign("search", $table->toHtml());
            } else {
                $msg .= "検索条件に一致するデータがありませんでした。";
            }
        }
    }

    /*
      //// 表の中身をダンプする
      //$str = "SELECT * FROM user WHERE patient_id = '$patient_id' AND passwd = '$passwd'";
      //print $str."<P>";
      $result = mysql_query("SELECT * FROM clinic");
      if (!$result) {
      die('クエリーが失敗しました。'.mysql_error());
      } else {
      ////// 結果の行数を得る
      $num_rows = mysql_num_rows($result);
      echo 'total user numbera = ' . $num_rows . '<p>';

      while ($row = mysql_fetch_assoc($result)) {
      print('<p>');
      print('patient_id='.$row['patient_id']);
      print(',password='.$row['passwd']);
      print(',name='.$row['name']);
      print(',yomi='.$row['yomi']);
      print(',email='.$row['email']);
      print(',zipcode='.$row['zipcode']);
      print(',location1='.$row['location1']);
      print(',location2='.$row['location2']);
      print(',tel='.$row['tel']);
      print('</p>');
      }
      }
     */
    /*
      // サーバー切断
      $close_flag = mysql_close($link);

      if ($close_flag) {
      print('<p>切断に成功しました。</p>');
      } */
    //}
}


/*
  print 'session_id=' . session_id() . '<P>';

  if ($error_message) {
  print '<font color="red">' . $error_message . '</font>';
  }
  if ($error_db) {
  print $error_db;
  } */

$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

$table->setCellContents(0, 0, "属性");
$table->setCellContents(0, 1, "値");

for ($nc = 0; $nc < count($patient_attribute); $nc++) {
    $table->setCellContents($nc + 1, 0, $patient_caption[$nc]);
    // 入力チェック or 入力やりなおし
    if ($verify == false) {  // 最初のページ
        $err = "";
        if ($table_error[$nc] == "under_flow" || $table_error[$nc] == "over_flow") {
            $err = "<font color=\"red\">" . $patient_caption[$nc] . "は" . $patient_vars_min[$nc] . "文字以上" . $patient_vars_max[$nc] . "文字以下</font>";
        }
        $disable = "";
        if ($nc == 1) {
            $disable = "disabled = \"disabled\"";
        }
        $str = $err . "<input type='text' name='" . $patient_attribute[$nc] . "' value='" . $patient_vars[$nc] . "' size=50 " . $disable . "/>";
    } else if ($verify == true && $posted_type == "verify") {// 確認のページ
        // クエリー実行
        $str = htmlspecialchars($patient_vars[$nc], ENT_QUOTES, "UTF-8") . "<input type='hidden' name='" . $patient_attribute[$nc] . "' value='" . $patient_vars[$nc] . "' />";
    } else {// 結果表示ページ
        $str = htmlspecialchars($patient_vars[$nc], ENT_QUOTES, "UTF-8");
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
if ($cmd != "none" && $cmd != "search") {
    if ($verify == false && $_POST["verify"]) {
        //echo "test=". $verify ."<P>";
        print "<font color=\"red\">入力が間違っています</font>";
    }
}

$smarty->assign("table", $table->toHtml());
if ($verify == true) {
    $smarty->assign("verify", "true");
} else {
    $smarty->assign("verify", "false");
}
if ($posted_type == "submit") {
    $smarty->assign("is_submit", "true");
} else {
    $smarty->assign("is_submit", "false");
}
$smarty->assign("cmd", $cmd);
$smarty->assign("msg", $msg);
//echo $table->toHtml();

$smarty->display("tpl/patient_reg.tpl");
/*

  <form action="patient_reg.php" method="POST">
  <?php echo $table->toHtml(); ?>
  <P>
  <?php
  if ($verify == false) {
  print "
  <input type=\"radio\" name=\"cmd\" value=\"none\" checked=\"checked\">None
  <input type=\"radio\" name=\"cmd\" value=\"add\" >新規登録
  <input type=\"radio\" name=\"cmd\" value=\"update\" >更新
  <input type=\"radio\" name=\"cmd\" value=\"get\" >データ取得
  <input type=\"radio\" name=\"cmd\" value=\"delete\" >削除
  <input type=\"radio\" name=\"cmd\" value=\"search\" >検索
  ";
  }
  ?>
  <P>
  <?php
  if ($posted_type == "submit") {

  } else if ($verify == true) {
  if ($cmd == "get") {
  print "
  <input type=\"submit\" name=\"cancel\" value=\"戻る\" />
  ";
  } else {
  print "
  <input type=\"hidden\" name=\"cmd\" value=\"" . $cmd . "\" />
  <input type=\"submit\" name=\"submit\" value=\"実行\" />
  <input type=\"submit\" name=\"cancel\" value=\"キャンセル\" />
  ";
  }
  } else {
  print "
  <input type=\"submit\" name=\"verify\" value=\"実行\" />
  <input type=\"submit\" name=\"reset\" value=\"リセット\" />
  ";
  }
  ?>
  <P>
  <a href="userTop.php">Back to User Top Page</a><P>
  </form>
  </body>
  </html>
 */

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
    //print_r($err);
    return $err;
}

/*
  function inputCheck($id, $pwd) {

  if($id == ""){
  print "<font color=\"red\">Error</font>: ユーザー名を入力してください<P>";
  return false;
  } else if($pwd == ""){
  print "<font color=\"red\">Error</font>: パスワードを入力してください。<P>";
  return false;
  }
  return true;
  }

  function patient_idVerify($id) {
  return false;
  } */
/*
  function userIdVerify($id) {

  $result = mysql_query("SELECT * FROM person WHERE patient_id = '$id'");
  if (!$result) {
  //die('クエリーが失敗しました。'.mysql_error());
  print "クエリーが失敗しました。" . mysql_error() . "</P>";
  return false;
  } else {
  $num_rows = mysql_num_rows($result);
  if ($num_rows == 0) {
  return false;
  }
  }

  return true;
  }

  function passwordVerify($id, $pwd) {

  $result = mysql_query("SELECT * FROM person WHERE patient_id = '$id' AND passwd = '$pwd'");
  if (!$result) {
  print "クエリーが失敗しました。" . mysql_error() . "</P>";
  return false;
  } else {
  $num_rows = mysql_num_rows($result);
  if ($num_rows == 0) {
  print "<font color=\"red\">Error</font>: パスワードが一致しません。<P>";
  return false;
  }
  }

  return true;
  }

  function getClinicData($id, $pwd, $attr) {
  $result = mysql_query("SELECT * FROM person WHERE patient_id = '$id' AND passwd = '$pwd'");
  if (!$result) {
  die('クエリーが失敗しました。' . mysql_error());
  } else {
  ////// 結果の行数を得る
  $num_rows = mysql_num_rows($result);
  echo 'total user numbera = ' . $num_rows . '<p>';

  while ($row = mysql_fetch_assoc($result)) {
  $tableItem = array();
  for ($cnt = 0; $cnt < count($attr); $cnt++) {
  $tableItem[] = $row[$attr[$cnt]];
  }
  }
  }
  return $tableItem;
  } */
?>