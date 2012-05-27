<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Registration View</title>
</head>
<body>

<?php
require_once 'HTML/Table.php';

$clinic_attribute = array("clinic_id", "passwd", "name", "yomi", "zipcode" ,"location1", "location2", "tel", "email");
$clinic_caption = array("病院ID", "パスワード", "病院名", "病院名（読み）",
			"郵便番号", "住所１", "住所２", "電話番号", 
			"メールアドレス");
$clinic_vars_min = array(1, 8, 1, 1, 8, 1, 1, 12, 1);
$clinic_vars_max = array(10, 20, 50, 100, 8, 255, 255, 13, 50);

$clinic_vars = array();
$table_error = array();
$verify = false;
session_start(); 

for ($cnt = 0; $cnt < count($clinic_attribute); $cnt++) {
  $table_error[] = false;
  //$clinic_vars[$cnt] = "null";
  if(isset($_POST[$clinic_attribute[$cnt]])){
    $clinic_vars[$cnt] = $_POST[$clinic_attribute[$cnt]];
  }
}
//print_r($clinic_vars);

$cmd = "";
if (isset($_POST["cmd"])) {
  echo $_POST["cmd"]."<P>";
  $cmd = $_POST["cmd"];
} else {
  $cmd = "none";
}

if ($_POST["submit"]) {
  echo "submit"."<P>";
}
if ($_POST["verify"]) {
  echo "verify"."<P>";
}
if ($_POST["reset"]) {
  echo "reset"."<P>";
  for ($cnt = 0; $cnt < count($clinic_vars); $cnt++) {
    $clinic_vars[$cnt] = null;
  }
}
//var_dump($_POST);
// エラーメッセージを格納する変数を初期化
$error_message = "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
//if ($clinic_vars[0] != "null") {
if ($_POST["submit"] ||$_POST["verify"] ) {
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

    //// 新規ユーザーの追加
    if($cmd == "add"){
      if ($_POST["verify"]) {
	$table_error = checkInput($clinic_vars, $clinic_vars_min, $clinic_vars_max, $table_error);
	$verify = true;
	for ($cnt = 0; $cnt < count($table_error); $cnt++) {
	  if($table_error[$cnt] == "over_flow" || $table_error[$cnt] == "under_flow"){
	    $verify = false;
	    break;
	  }
	}
	//print_r($table_error);
	if($verify == true){
	  print "以下の内容で登録します。内容が合っているか今一度確認してください。";
	}
      }
      if ($_POST["submit"]) {
	$verify = true;
	$str = "INSERT INTO clinic VALUES (null, '";
	$last_item = count($clinic_attribute) -1;
	for ($cnt = 1; $cnt < count($clinic_attribute); $cnt++) {
	  if($cnt == $last_item){
	    $str .= $clinic_vars[$cnt] . "');";
	  } else {
	    $str .= $clinic_vars[$cnt] . "','";
	  } 
	}
	//$str = "INSERT INTO clinic VALUES (null, '" . $passwd . "','" . $name . "','" . $yomi . "','" . $email . "','" . $zipcode . "','" . $location1 . "','" . $location2 . "','" . $tel . "');";
	//print $str."<P>";
	$result = mysql_query($str);
	if (!$result) {
	  print "クエリーが失敗しました。".mysql_error()."<P>";
	  //die('クエリーが失敗しました。'.mysql_error());
	} else {
	  print "<font color=\"red\">Success</font>: データを登録しました。<P>";
	}
      }
      
    } else if($cmd == "update"){ // 既存ユーザーのデータ更新
      if ($_POST["verify"]) {
	
	if(userIdVerify($clinic_vars[0])){
	  if(passwordVerify($clinic_vars[0], $clinic_vars[1])){
	    
	    $table_error = checkInput($clinic_vars, $clinic_vars_min, $clinic_vars_max, $table_error);
	    $verify = true;
	    for ($cnt = 0; $cnt < count($table_error); $cnt++) {
	      if($table_error[$cnt] == "over_flow" || $table_error[$cnt] == "under_flow"){
		$verify = false;
		break;
	      }
	    }
	    //print_r($table_error);
	    if($verify == true){
	      print "以下の内容でデータを更新します。更新内容が合っているか今一度確認してください。";
	    }
	    
	  } else {
	    //print "<font color=\"red\">ERROR: パスワードが一致しません。</font>";
	    $verify = false;
	  }
	} else {
	  print "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
	  $verify = false;
	}
	
      } 
      if ($_POST["submit"]) {
	$verify = true;
	$str = "UPDATE clinic SET ";
	$last_item = count($clinic_attribute) -1;
	for ($cnt = 1; $cnt < count($clinic_attribute); $cnt++) {
	  if($cnt == $last_item){
	    $str .= $clinic_attribute[$cnt] . "='" . $clinic_vars[$cnt] . "'";
	  } else {
	    $str .= $clinic_attribute[$cnt] . "='" . $clinic_vars[$cnt] . "', ";
	  } 
	}
	$str .= " WHERE " . $clinic_attribute[0] . "=" . $clinic_vars[0] . ";";
	//$str = "UPDATE clinic SET passwd='" . $passwd . "', name='" . $name . "', yomi='" . $yomi . "', email='" . $email . "', zipcode='" . $zipcode . "', location1='" . $location1 . "', location2='" . $location2 . "', tel='" . $tel . "' WHERE clinic_id=" . $clinic_id . ";";
	print $str."<P>";
	$result = mysql_query($str);
	if (!$result) {
	  print "クエリーが失敗しました。".mysql_error()."<P>";
	  //die('クエリーが失敗しました。'.mysql_error());
	} else {
	  print "<font color=\"red\">Success</font>: 以下のデータ更新しました。<P>";
	}
      }  
    } else if($cmd == "delete"){ // ユーザーデータの削除
      if ($_POST["verify"]) {
	if(userIdVerify($clinic_vars[0])){
	  if(passwordVerify($clinic_vars[0], $clinic_vars[1])){
	    $verify = true;
	  } else {
	    //print "<font color=\"red\">ERROR: パスワードが一致しません。</font>";
	    $verify = false;
	  }
	} else {
	  print "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
	  $verify = false;
	}
	//print_r($table_error);
	if($verify == true){
	  print "以下のデータを削除します。本当に削除しても良いか今一度確認してください。";
	  $clinic_vars = getClinicData($clinic_vars[0], $clinic_vars[1], $clinic_attribute);
	  
	}
      }
      if ($_POST["submit"]) {
	$verify = true;
	$str = "DELETE FROM clinic WHERE " . $clinic_attribute[0] . " = '" . $clinic_vars[0] . "' AND "  . $clinic_attribute[1] . " = '" . $clinic_vars[1] . "';";
	$result = mysql_query($str);
	//$result = mysql_query("DELETE FROM clinic WHERE clinic_id = '$clinic_id' AND passwd = '$passwd'");
	if (!$result) {
	  //die('クエリーが失敗しました。'.mysql_error());
	  print "クエリーが失敗しました。".mysql_error()."<P>";
	} else {
	  print "<font color=\"red\">Success</font>: 以下のデータを削除しました。<P>";
	}
      } 
    } else if($cmd == "get"){
      if ($_POST["verify"]) {
	if(userIdVerify($clinic_vars[0])){
	  if(passwordVerify($clinic_vars[0], $clinic_vars[1])){
	    $verify = true;
	  } else {
	    //print "<font color=\"red\">ERROR: パスワードが一致しません。</font>";
	    $verify = false;
	  }
	} else {
	  print "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
	  $verify = false;
	}
	//print_r($table_error);
	if($verify == true){
	  print "以下のデータを取得しました。";
	  $clinic_vars = getClinicData($clinic_vars[0], $clinic_vars[1], $clinic_attribute);
	  
	}
      }
    }
    /*
    //// 表の中身をダンプする
    //$str = "SELECT * FROM user WHERE clinic_id = '$clinic_id' AND passwd = '$passwd'";
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
	 print('clinic_id='.$row['clinic_id']);
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
    // サーバー切断
    $close_flag = mysql_close($link);
    
    if ($close_flag){
      print('<p>切断に成功しました。</p>');
    }
    //}
} 



print 'session_id='.session_id().'<P>';

if ($error_message) {
  print '<font color="red">'.$error_message.'</font>';
}
if ($error_db) {
  print $error_db;
}

$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

$table->setCellContents(0, 0, "");
$table->setCellContents(0, 1, "値");

for ($nc = 0; $nc < count($clinic_attribute); $nc++) {
  $table->setCellContents($nc+1, 0, $clinic_caption[$nc]);
  // 入力チェック or 入力やりなおし
  if($verify == false){  
    $err = "";
    if($table_error[$nc] == "under_flow" || $table_error[$nc] == "over_flow"){
      $err = "<font color=\"red\">" . $clinic_caption[$nc] . "は" . $clinic_vars_min[$nc] . "文字以上" . $clinic_vars_max[$nc] . "文字以下</font>";
    }
    
    $str = $err . "<input type='text' name='" . $clinic_attribute[$nc] . "' value='" . $clinic_vars[$nc] . "' size=50 />";
  } else if($verify == true && $_POST["verify"]){
    // クエリー実行
    $str = htmlspecialchars($clinic_vars[$nc], ENT_QUOTES, "UTF-8") ."<input type='hidden' name='" . $clinic_attribute[$nc] . "' value='" . $clinic_vars[$nc] . "' />";
  } else {
    $str = htmlspecialchars($clinic_vars[$nc], ENT_QUOTES, "UTF-8");
  }
  $table->setCellContents($nc+1, 1, $str);
}
$altRow = array('bgcolor' => 'lightgray');
$table->altRowAttributes(0, null, $altRow);

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
if($cmd != "none"){
  if($verify == false && $_POST["verify"]){
    //echo "test=". $verify ."<P>";
    print "<font color=\"red\">入力が間違っています</font>";
  }
}

echo "<form action=\"registrationView.php\" method=\"POST\">";
echo $table->toHtml();
echo "<P>";
if($verify == false){
  echo "<input type=\"radio\" name=\"cmd\" value=\"none\" checked=\"checked\">None";
  echo "<input type=\"radio\" name=\"cmd\" value=\"add\" >新規登録";
  echo "<input type=\"radio\" name=\"cmd\" value=\"update\" >更新";
  echo "<input type=\"radio\" name=\"cmd\" value=\"get\" >データ取得";
  echo "<input type=\"radio\" name=\"cmd\" value=\"delete\" >削除
";
}
echo "<P>";
if($_POST["submit"]){
} else if($verify == true){
  if($cmd == "get"){
    echo "<input type=\"submit\" name=\"cancel\" value=\"戻る\" />
";
  } else {
    echo "<input type=\"hidden\" name=\"cmd\" value=\"" . $cmd ."\" />";
    echo "<input type=\"submit\" name=\"submit\" value=\"実行\" />
";
    echo "<input type=\"submit\" name=\"cancel\" value=\"キャンセル\" />";
  }
} else {
  echo "<input type=\"submit\" name=\"verify\" value=\"確認\" />";
  echo "<input type=\"submit\" name=\"reset\" value=\"リセット\" />";
}
echo "</form>";
?>


<P>
<a href="admin.php">Back</a><P>

</body>
</html>

<?php
function checkInput($vars, $min, $max, $err){
  for ($cnt = 1; $cnt < count($vars); $cnt++) {
    if($min[$cnt] > mb_strlen($vars[$cnt])){
      //echo "under<P>";
      $err[$cnt] = "under_flow";
    } else if(mb_strlen($vars[$cnt]) > $max[$cnt]) {
      //echo "over<P>";
      $err[$cnt] = "over_flow";
    }
  }
  //print_r($err);
  return $err;
}

function inputCheck($id, $pwd){
  /*
   if($id == ""){
     print "<font color=\"red\">Error</font>: ユーザー名を入力してください<P>";
     return false;
   } else if($pwd == ""){
     print "<font color=\"red\">Error</font>: パスワードを入力してください。<P>";
     return false;
     }*/ 
   return true;
}
function clinic_idVerify($id){
  return false;
}
function userIdVerify($id){

  $result = mysql_query("SELECT * FROM clinic WHERE clinic_id = '$id'");
  if (!$result) {
    //die('クエリーが失敗しました。'.mysql_error());
    print "クエリーが失敗しました。".mysql_error()."</P>";
    return false;
  } else {
    $num_rows = mysql_num_rows($result);
    if($num_rows == 0){
      return false;
    }
  }

  return true;
}

function passwordVerify($id, $pwd){

  $result = mysql_query("SELECT * FROM clinic WHERE clinic_id = '$id' AND passwd = '$pwd'");
  if (!$result) {
    print "クエリーが失敗しました。".mysql_error()."</P>";
    return false;
  } else {
    $num_rows = mysql_num_rows($result);
    if($num_rows == 0){
      print "<font color=\"red\">Error</font>: パスワードが一致しません。<P>";
      return false;
    } 
  }

  return true;
}
function getClinicData($id, $pwd, $attr){
  $result = mysql_query("SELECT * FROM clinic WHERE clinic_id = '$id' AND passwd = '$pwd'");
  if (!$result) {
     die('クエリーが失敗しました。'.mysql_error());
  } else {  
    ////// 結果の行数を得る
    $num_rows = mysql_num_rows($result);
    echo 'total user number = ' . $num_rows . '<p>';
    
    while ($row = mysql_fetch_assoc($result)) {
      $tableItem = array();
      for ($cnt = 0; $cnt < count($attr); $cnt++) {
	$tableItem[] = $row[$attr[$cnt]];
      }
    }
  }
  return $tableItem;
}
?>