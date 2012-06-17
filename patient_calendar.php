<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Immunization Calendar Page </title>
</head>
<body>

<?php
   
ini_set('error_reporting', E_ALL);

require_once 'HTML/Table.php';
require_once "medicine.php";

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

$medAry = array();
$tmp = explode("_", $birthday);
$year = $tmp[0];
$month = 10;//$tmp[1];
$day = 30;//$tmp[2];

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
$str = "SELECT * FROM immunization";
//print $str."<P>";
$result = mysql_query($str);
if (!$result) {
  die('クエリーが失敗しました。'.mysql_error());
} else {  
  ////// 結果の行数を得る
  $num_rows = mysql_num_rows($result);
  echo 'total item number = ' . $num_rows . '<p>';

  while ($row = mysql_fetch_assoc($result)) {
    $tmpMed = new medicine();
    $tmpMed->setId($row['immunization_id']);
    $tmpMed->setName($row['immunization_name']);
    $tmpMed->setRegular($row['regular']);
    $tmpMed->setKinds($row['kinds']);
    $tmpMed->setFrequency($row['frequency']);
    $tmpMed->setComment($row['comment']);
    $tmpMed->setTimestamp($row['timestamp']);
    $medAry[] = $tmpMed;
  }
  
  $str = "SELECT * FROM immunization_term WHERE clinic_id = '" . $clinic_id ."';";
  $result = mysql_query($str);
  if (!$result) {
    die('クエリーが失敗しました。'.mysql_error());
  } else { 
    
     $num_rows = mysql_num_rows($result);
     echo 'total index number = ' . $num_rows . '<p>';
     while ($row = mysql_fetch_assoc($result)) {
       
       //$tmp_str = explode("_", $row['detail_id']);
       //$id = $tmp_str[0] - 1;
       $id = $row['immunization_id']-1;
       $times = $row['times'];
       $tmpMed = $medAry[$id];

       
       $frequency = $tmpMed->getFrequency();
       if($tmpMed->getFrequency() >= $times){
	 $tmpMed->pushTerm($row['term_start'], $row['term_end']);
       }
     }
  }
  $tmp = array();
  $tmp2 = array();
  for ($cnt = 0; $cnt < count($medAry); $cnt++) {
    $tmpMed = $medAry[$cnt];
    $tmp2[] = $medAry[$cnt]; 
     //echo $tmpMed->getId() . " ";
    echo $tmpMed->getName() . " ";
    echo $tmpMed->getRegular() ." ";
    echo $tmpMed->getKinds() ." ";
    echo $tmpMed->getFrequency() . " ";
    //echo $tmpMed->getComment() . " ";
    //echo $tmpMed->getTimestamp() . " ";
    echo "<BR>";

    $baseSec = mktime(0,0,0,$month,$day,$year);
    for($i=0;$i<$tmpMed->getFrequency();$i++){
      
      $num = $i+1;
      echo  $num . "回目 : ";
      //echo $i . " ";
      
      $baseSec = mktime(0,0,0,$month + $tmpMed->getHeadPeriod($i),$day,$year);
      if($tmp[$baseSec] == ""){
	$tmp[$baseSec] = $tmpMed;
      } else {
	echo "depricated!!!";
      }
      $dt = date("Y-m-d", $baseSec);
      echo $dt;
      /*
      while($tmp_month > 12){
	$tmp_year++;
	$tmp_month -= 12;
      }
      echo $tmp_year . "/" . $tmp_month ."/" . $tmp_day;*/
      
      echo " - ";
      $baseSec = mktime(0,0,0,$month + $tmpMed->getTailPeriod($i),$day,$year);
      $dt = date("Y-m-d", $baseSec);
      echo $dt."<BR>";
      
    }
    echo "<P>"; 
  }
  echo "aaaaaaaaaaaa";
  //arsort($tmp2);
  //print_r($tmp2);
  for($i=0;$i<count($tmp2);$i++){
    $val = $tmp2[$i];
    echo $val->getId() . " ";
    echo $val->getName() . " ";
    echo $val->getRegular() ." ";
    echo $val->getKinds() ." ";
    echo $val->getFrequency() . " ";
    echo $val->getComment() . " ";
    echo $val->getTimestamp() . " ";
    echo "<BR>";
  }
  ksort($tmp);
  //print_r($tmp);
  /*
  foreach($tmp as $key => $val){
    echo $val->getId() . " ";
    echo $val->getName() . " ";
    echo $val->getRegular() ." ";
    echo $val->getKinds() ." ";
    echo $val->getFrequency() . " ";
    echo $val->getComment() . " ";
    echo $val->getTimestamp() . " ";
    echo "<BR>";
    }*/
}

// サーバー切断
$close_flag = mysql_close($link); 
if ($close_flag){
  print('<p>切断に成功しました。</p>');
}
?>
<?php

?>
<P>
<a href="patient_top.php">Back to Person Top Page</a><P>
  </form>
</body>
</html>
