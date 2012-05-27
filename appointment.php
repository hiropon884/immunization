<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Patient Top Page </title>
</head>
<body>

<?php

require_once 'HTML/Table.php';

session_start(); 
// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["clinic_id"])) {

  // 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
  $no_login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/login.php";
  header("Location: {$no_login_url}");
  exit;
} else if(!isset($_SESSION["person_id"]) ||
	  !isset($_SESSION["birthday"]) || 
	  !isset($_SESSION["person_name"])){
  $url = "http://{$_SERVER["HTTP_HOST"]}/immunization/userTop.php";
  header("Location: {$url}");
  exit;
} 

$clinic_id = $_SESSION["clinic_id"];
$person_id = $_SESSION["person_id"];
$birthday = $_SESSION["birthday"];
$person_name = $_SESSION["person_name"];

echo "clinic_id = " . $clinic_id . "<BR>";
echo "person_id = " . $person_id . "<BR>";
echo "person_name = " . $person_name . "<BR>";
echo "birthday = " . $birthday . "<P>";

$book_attrs = array("person_id", "immunization_id", "number", "day", "lot_num", "state");
$book_caption = array("人ID","予防接種名", "回数","接種年月日", "ロットナンバー", 
		      "状態"); 
$book_vars_min = array(1, 1, 1, 10, 1, 1);
$book_vars_max = array(10, 10, 10, 10, 20, 1);

$book_vars = array();
$table_error = array();
$verify = false;
$medicine = array("インフルエンザb型(ヒブ)","肺炎球菌(PCV7)",
		  "B型肝炎(HBV)","ロタウイルス","三種混合(DPT)",
		  "BCG","ポリオ","麻しん、風しん(MR)","水痘",
		  "おたふくかぜ", "日本脳炎", "インフルエンザ",
		  "2種混合(DT)",
		  "ヒトパピローマウイルス(HPV) - 2価ワクチン",
		  "ヒトパピローマウイルス(HPV) - 4価ワクチン",
		  "A型肝炎");
$state_name = array("予約","接種済み");

for ($cnt = 1; $cnt < count($book_attrs); $cnt++) {
  $table_error[] = false;
  //$clinic_vars[$cnt] = "null";
  if(isset($_POST[$book_attrs[$cnt]])){
    $book_vars[$cnt] = $_POST[$book_attrs[$cnt]];
    echo $book_vars[$cnt]."<BR>";
  }
}
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
  for ($cnt = 0; $cnt < count($book_vars); $cnt++) {
    $book_vars[$cnt] = null;
  }
}
$book_vars[0] = $person_id;
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
  
  //// 新規予約の追加
  if($cmd == "add"){
    if ($_POST["verify"]) {
      $table_error = checkInput($book_vars, $book_vars_min, $book_vars_max, $table_error);
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
      $str = "INSERT INTO book VALUES (";
      $last_item = count($book_attrs) -1;
      for ($cnt = 0; $cnt < count($book_attrs); $cnt++) {
	$quote = "'";
	$str .= $quote . $book_vars[$cnt] . $quote;
	if($cnt == $last_item){
	  $str .= ");";
	} else {
	  $str .= ", ";
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
      if(bookVerify($book_vars, $book_attrs)){
	$table_error = checkInput($book_vars, $book_vars_min, $book_vars_max, $table_error);
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
	print "<font color=\"red\">ERROR: データが一致しません。</font>";
	$verify = false;
      }
    } 
    if ($_POST["submit"]) {
      $verify = true;
      $str = "UPDATE book SET ";
      $last_item = count($book_attrs) -1;
      for ($cnt = 0; $cnt < count($book_attrs); $cnt++) {
	$quote = "'";
	$str .= $book_attrs[$cnt] . "=" . $quote . $book_vars[$cnt] . $quote; 
	
	if($cnt == $last_item){
	  $str .= "";
	} else {
	  $str .= ", ";
	} 
      }
      //$str .= " WHERE ";// . $book_attrs[0] . "=" . $book_vars[0] . ";";
      $str .= " WHERE " . getVerifyStr($book_vars, $book_attrs);
      /*
      $limit = 3;
      $last_item = $limit - 1;
      for ($cnt = 0; $cnt < $limit; $cnt++) {
	$str .= $book_attrs[$cnt] . " = '" . $book_vars[$cnt];
	if($cnt < $last_item){
	  $str .= "' AND ";
	} else {
	  $str .= ";";
	}
	}*/
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
      if(bookVerify($book_vars, $book_attrs)){
	    $verify = true;
      } else {
	print "<font color=\"red\">ERROR: 指定されたIDをもつユーザーがいませんでした。</font>";
	$verify = false;
      }
      //print_r($table_error);
      if($verify == true){
	print "以下のデータを削除します。本当に削除しても良いか今一度確認してください。";
	$book_vars = getBookData($book_vars, $book_attrs);
      }
    }
    if ($_POST["submit"]) {
      $verify = true;
      $str = "DELETE FROM book WHERE ";
      $str .=  getVerifyStr($book_vars, $book_attrs);
      /*
      $limit = 3;
      $last_item = $limit - 1;
      for ($cnt = 0; $cnt < $limit; $cnt++) {
	$str .= $book_attrs[$cnt] . " = '" . $book_vars[$cnt];
	if($cnt < $last_item){
	  $str .= "' AND ";
	} else {
	  $str .= ";";
	}
	}*/
      //$str = "DELETE FROM book WHERE " . $book_attrs[0] . " = '" . $clinic_vars[0] . "' AND "  . $clinic_attribute[1] . " = '" . $clinic_vars[1] . "'";
      $result = mysql_query($str);
      //$result = mysql_query("DELETE FROM clinic WHERE clinic_id = '$clinic_id' AND passwd = '$passwd'");
      if (!$result) {
	//die('クエリーが失敗しました。'.mysql_error());
	print "クエリーが失敗しました。".mysql_error()."<P>";
      } else {
	print "<font color=\"red\">Success</font>: 以下のデータを削除しました。<P>";
      }
    } 
  }
  /*
  else if($cmd == "get"){
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
      }*/
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

$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

$table->setCellContents(0, 0, "");
$table->setCellContents(0, 1, "値");
for ($nc = 0; $nc < count($book_attrs); $nc++) {
  
  $table->setCellContents($nc+1, 0, $book_caption[$nc]);
  if($nc != 1 && $nc !=5){
    // 入力チェック or 入力やりなおし
    if($verify == false){  
      $err = "";
      if($table_error[$nc] == "under_flow" || $table_error[$nc] == "over_flow"){
	$err = "<font color=\"red\">" . $book_caption[$nc] . "は" . $book_vars_min[$nc] . "文字以上" . $book_vars_max[$nc] . "文字以下</font>";
      }
      $disable = "";
      if($nc == 0){
	$disable = "disabled = \"disabled\"";
      }
      $str = $err . "<input type='text' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' size=50 " . $disable ."/>";
    } else if($verify == true && $_POST["verify"]){
      // クエリー実行
      $str = htmlspecialchars($book_vars[$nc], ENT_QUOTES, "UTF-8") ."<input type='hidden' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' />";
    } else {
      $str = htmlspecialchars($book_vars[$nc], ENT_QUOTES, "UTF-8");
    }
  } else {
    $disable="";
    if($verify == true){
      $disable = " disabled";
    } 
    if($nc == 1){
      if($verify == true){
	$id = $book_vars[$nc]-1;
	$str = $medicine[$id]."<input type='hidden' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' />";
      } else {
	$str = "<select name=\"immunization_id\">";
	for ($j = 0; $j < count($medicine); $j++) {
	$selected = "";
	$id = $j+1;
	if($id == $book_vars[$nc]){
	  $selected = " selected";
	}
	$str .= "<option value=\"".$id."\"".$selected.">".$medicine[$j]."</option>";
	//echo "<option value=\"".$j."\">".$medicine[$j]."</option>";
	}
	$str .= "</select>";
      }
    } else if($nc == 5){
       if($verify == true){
	
	$str = $state_name[$book_vars[$nc]]."<input type='hidden' name='" . $book_attrs[$nc] . "' value='" . $book_vars[$nc] . "' />";
      } else {
	 $str = "<select name=\"state\">";
	 for ($j = 0; $j < count($state_name); $j++) {
	   $selected = "";
	   if($j == $book_vars[$nc]){
	     $selected = " selected";
	   }
	   $str .= "<option value=\"".$j."\"".$selected.">".$state_name[$j]."</option>";
	 }
       }
    }
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

echo "<form action=\"appointment.php\" method=\"POST\">";
echo $table->toHtml();
echo "<P>";
if($verify == false){
  echo "<input type=\"radio\" name=\"cmd\" value=\"none\" checked=\"checked\">None";
  echo "<input type=\"radio\" name=\"cmd\" value=\"add\" >新規登録";
  echo "<input type=\"radio\" name=\"cmd\" value=\"update\" >更新";
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
<a href="patient_top.php">Back to Person Top Page</a><P>
</form>
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

function bookVerify($vars, $attrs){
  $str_tmp = "SELECT * FROM book WHERE ";
  $str_tmp .= getVerifyStr($vars, $attrs);
  $result = mysql_query($str_tmp);
  if (!$result) {
    print "クエリーが失敗しました。".mysql_error()."</P>";
    return false;
  } else {
    $num_rows = mysql_num_rows($result);
    if($num_rows == 0){
      print "<font color=\"red\">Error</font>: データが一致しません。<P>";
      return false;
    } 
  }

  return true;
}

function getVerifyStr($vars, $attrs){
  $tmp = "";
  $limit = 3;
  $last_item = $limit - 1;
  for ($cnt = 0; $cnt < $limit; $cnt++) {
    $tmp .= $attrs[$cnt] . " = '" . $vars[$cnt];
    if($cnt < $last_item){
      $tmp .= "' AND ";
    } else {
      $tmp .= "';";
    }
  }
  return $tmp;
}
function getBookData($vars, $attrs){
  $str_tmp = "SELECT * FROM book WHERE ";
  $str_tmp .= getVerifyStr($vars, $attrs);
  echo $str_tmp;
  $result = mysql_query($str_tmp);
  if (!$result) {
     die('クエリーが失敗しました。'.mysql_error());
  } else {  
    ////// 結果の行数を得る
    $num_rows = mysql_num_rows($result);
    echo 'total user number = ' . $num_rows . '<p>';
    
    while ($row = mysql_fetch_assoc($result)) {
      $tableItem = array();
      for ($cnt = 0; $cnt < count($attrs); $cnt++) {
	$tableItem[] = $row[$attrs[$cnt]];
	echo  $row[$attrs[$cnt]];
      }
    }
  }
  return $tableItem;
}
?>