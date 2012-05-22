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

$patient_vars = array();
$table_error = array();
$verify = false;

$posted_item_num=0;
for ($cnt = 0; $cnt < count($patient_attribute); $cnt++) {
  $table_error[] = false;
  //$patient_vars[$cnt] = "null";
  if(isset($_POST[$patient_attribute[$cnt]])){
    $posted_item_num++;
    $patient_vars[$cnt] = $_POST[$patient_attribute[$cnt]];
  }
}
//print_r($patient_vars);

$clinic_id = $_SESSION["clinic_id"];
$patient_id = $_POST["patient_id"];
echo "clinic_id = " . $clinic_id . "<P>";
echo "patient_id = " . $patient_id . "<P>";

?>

<a href="calendar">予防接種カレンダー</a><P>
<a href="patient_past.php">接種履歴詳細</a><BR>
<a href="patient_booklist">予約一覧</a><BR>

<a href="userTop.php">Back to User Top Page</a><P>
</form>
</body>
</html>
