<?php

require_once("../../class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start();
$smarty->session_check();

$smarty->assign("menu_is_available", "true");
$smarty->assign("mode", "patient");
$smarty->assign("location", "appointment");

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
  } */
//print_r($patient_vars);

$clinic_id = $_SESSION["clinic_id"];
$person_id = $_SESSION["person_id"];
$birthday = $_SESSION["birthday"];
$person_name = $_SESSION["person_name"];

//echo "clinic_id = " . $clinic_id . "<BR>";
//echo "person_id = " . $person_id . "<BR>";
//echo "person_name = " . $person_name . "<BR>";
//echo "birthday = " . $birthday . "<P>";
//$medAry = array();
$tmp = explode("_", $birthday);
$year = $tmp[0];
$month = 10; //$tmp[1];
$day = 30; //$tmp[2];
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
$db = $smarty->getDb();
$tableItem = $db->getBookInfo($person_id, "0");
/*
  //// クエリーの実行
  $str = "SELECT body.immunization_id, body.immunization_name, book.day, book.number as times, book.lot_num FROM immunization as body INNER JOIN book ON book.immunization_id = body.immunization_id WHERE book.person_id = " . $person_id . " AND book.state = 0;";

  //print $str."<P>";
  $result = mysql_query($str);
  if (!$result) {
  die('クエリーが失敗しました。'.mysql_error());
  } else {
  ////// 結果の行数を得る
  $num_rows = mysql_num_rows($result);
  echo 'total item number = ' . $num_rows . '<p>';
  //$res = array();
  $date_buf = array();
  while ($row = mysql_fetch_assoc($result)) {
  $tmpMed = new medicine();
  $tmpMed->setId($row['immunization_id']);
  $tmpMed->setName($row['immunization_name']);
  $tmpMed->setTimes($row['times']);
  $tmpMed->setDate($row['day']);
  $tmpMed->setLot($row['lot_num']);
  $medAry[$baseSec+$date_buf[$baseSec]] = $tmpMed;
  $date_buf[$baseSec] += 1;
  }

  ksort($medAry);
 */

echo "<form action=\"appointment.php\" method=\"POST\">";

$table_name = array("接種予定日", "予防接種名", "回目", "ロットナンバー");
$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

for ($i = 0; $i < count($table_name); $i++) {
    $table->setCellContents(0, $i, $table_name[$i]);
}
$nc = 0;
foreach ($tableItem as $record) {
     //for ($i = 0; $i < count($record); $i++) {
        $table->setCellContents($nc + 1, $i, $record[$i]);
     //}
//foreach ($medAry as $key => $val) {
    $table->setCellContents($nc + 1, 0, $record['day']);
    $table->setCellContents($nc + 1, 1, $row['immunization_name']);
    $table->setCellContents($nc + 1, 2, $row['times']);
    $table->setCellContents($nc + 1, 3, $row['lot_num']);
    $str = "<button type=\"submit\" name=\"book_params\" value=\""
     . $record['immunization_id'] . "_" . $record['times'] . "_" 
     . $record['day'] . "_" . $record['lot_num'] . "\">変更</button>";
    $table->setCellContents($nc + 1, 4, $str);
    $nc++;
    if ($nc % 2 == 1) {
        $hrAttrs = array('bgcolor' => 'WhiteSmoke');
    } else {
        $hrAttrs = array('bgcolor' => 'GhostWhite');
    }
    $table->setRowAttributes($nc, $hrAttrs, true);
}
//array('bgcolor' => 'lightgray');
//$table->altRowAttributes(0, null, $altRow);

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
//echo $table->toHtml();
$smarty->assign("table", $table->toHtml());
//echo "</form>";
//}
/*
  // サーバー切断
  $close_flag = mysql_close($link);
  if ($close_flag){
  print('<p>切断に成功しました。</p>');
  } */
?>