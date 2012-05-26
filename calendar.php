<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Immunization Calendar Page </title>
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
}
/*
$patient_attribute = array("person_id", "clinic_id", "patient_id", 
			  "family_name", "family_name_yomi", "personal_name", 
			  "personal_name_yomi", "birthday", "zipcode",
			   "location1", "location2", "tel", "email");
$patient_caption = array("人ID","病院ID", "患者ID", "氏", "氏（読み）","名", 
			"名（読み）", "生年月日", "郵便番号", "住所１", "住所２",
			"電話番号", "メールアドレス");
$patient_vars_min = array(1, 1, 1, 1, 1, 1, 1, 10, 8, 1, 1, 12 ,1);
$patient_vars_max = array(10, 10, 20, 10, 20, 10, 20, 10, 8, 255, 255, 13, 50);

$patient_vars = array();
$table_error = array();
$verify = false;
*/
/*
$posted_item_num=0;
for ($cnt = 0; $cnt < count($patient_attribute); $cnt++) {
  $table_error[] = false;
  //$patient_vars[$cnt] = "null";
  if(isset($_POST[$patient_attribute[$cnt]])){
    $posted_item_num++;
    $patient_vars[$cnt] = $_POST[$patient_attribute[$cnt]];
  }
  }*/
//print_r($patient_vars);

$clinic_id = $_SESSION["clinic_id"];
$person_id = $_SESSION["person_id"];
$birthday = $_SESSION["birthday"];
$person_name = $_SESSION["person_name"];

echo "clinic_id = " . $clinic_id . "<BR>";
echo "person_id = " . $person_id . "<BR>";
echo "person_name = " . $person_name . "<BR>";
echo "birthday = " . $birthday . "<P>";

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

//// クエリーの実行
$str = "SELECT * FROM person WHERE person_id = $person_id";
print $str."<P>";
$result = mysql_query($str);
if (!$result) {
  die('クエリーが失敗しました。'.mysql_error());
} else {  
  ////// 結果の行数を得る
  $num_rows = mysql_num_rows($result);
  echo 'total user number = ' . $num_rows . '<p>';
  if($num_rows > 1){
    echo "ERROR: Many patient was detected.<P>";
  }
  $row = mysql_fetch_assoc($result);
  $birthday = $row['birthday'];
  echo "birthday=".$birthday."<P>";
  //while ($row = mysql_fetch_assoc($result)) {
  //  $tableItem = array();
  //for ($cnt = 0; $cnt < count($patient_attribute); $cnt++) {	
  //print $row[$patient_attribute[$cnt]] . "<P>";
  //}
  
  //$tableData[] = $tableItem;
  
}

// サーバー切断
$close_flag = mysql_close($link);
if($birthday != ""){
  $_SESSION["birthday"] = $birthday;
  }*/
?>

<a href="patient_top.php">Back to Person Top Page</a><P>
</form>
</body>
</html>
