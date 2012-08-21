<?php

require_once("../../class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start();
$smarty->session_check();

$smarty->assign("menu_is_available", "true");
$smarty->assign("mode", "patient");
$smarty->assign("location", "appointment");

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

$db = $smarty->getDb();
$tableItem = $db->getBookInfo($person_id, "0");

echo "<form action=\"appointment.php\" method=\"POST\">";

$table_name = array("接種予定日", "予防接種名", "回目", "ロットナンバー");
$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

for ($i = 0; $i < count($table_name); $i++) {
    $table->setCellContents(0, $i, $table_name[$i]);
}
$nc = 0;
foreach ($tableItem as $row) {
     //for ($i = 0; $i < count($record); $i++) {
        $table->setCellContents($nc + 1, $i, $row[$i]);
     //}
//foreach ($medAry as $key => $val) {
    $table->setCellContents($nc + 1, 0, $row['day']);
    $table->setCellContents($nc + 1, 1, $row['immunization_name']);
    $table->setCellContents($nc + 1, 2, $row['times']);
    $table->setCellContents($nc + 1, 3, $row['lot_num']);
    $str = "<button type=\"submit\" name=\"book_params\" value=\""
     . $row['immunization_id'] . "_" . $row['times'] . "_" 
     . $row['day'] . "_" . $row['lot_num'] . "\">変更</button>";
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

$smarty->display(TPL_BASE."patient_booklist.tpl");

?>