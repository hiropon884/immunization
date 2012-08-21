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

$db = $smarty->getDb();
$tableItem = $db->getBookInfo($person_id, "1");

$table_name = array("接種日", "予防接種名", "回目", "ロットナンバー");
$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

for ($i = 0; $i < count($table_name); $i++) {
    $table->setCellContents(0, $i, $table_name[$i]);
}
$nc = 0;
foreach ($tableItem as $record) {
    for ($i = 1; $i < count($table_name); $i++) {
        $table->setCellContents($nc + 1, $i-1, $record[$i]);
        // $table->setCellContents($nc+1, 1, $val->getName());
        // $table->setCellContents($nc+1, 2, $val->getTimes());
        // $table->setCellContents($nc+1, 3, $val->getLot());
        //$str = "<button type=\"submit\" name=\"book_params\" value=\"" . $val->getId() . "_" . $val->getTimes() . "_" . $val->getDate() . "_" . $val->getLot() . "\">変更</button>";
        //$table->setCellContents($nc+1, 4, $str); 
    }
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
$smarty->assign("table",$table->toHtml());

$smarty->display(TPL_BASE."patient_history.tpl");

?>

