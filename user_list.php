<?php

require_once("class/MySmarty.class.php");
require_once 'HTML/Table.php';

$smarty = new MySmarty(true);

//session_start();
//$smarty->session_check();

$smarty->assign("menu_state", "0");
$smarty->assign("menu_flag", "1");
$params = $smarty->getParams();

$clinic_caption = $params['clinic']['caption'];

$db = $smarty->getDb();
$tableData = $db->getClinic();

// create html table with all user information
$attrs = array('width' => '800');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);
$table->setAutoFill('n/a');

for ($nr = 0; $nr < count($tableData); $nr++) {
  $table->setHeaderContents($nr+1, 0, $tableData[$nr][0]);
  for ($i = 1; $i < count($clinic_caption); $i++) {
    if ('' != $tableData[$nr][$i]) {
      $table->setCellContents($nr+1, $i, htmlspecialchars($tableData[$nr][$i], ENT_QUOTES, 'UTF-8'));
    }
  }
  if($nr%2 == 1){
    $hrAttrs = array('bgcolor' => 'WhiteSmoke');
  } else {
    $hrAttrs = array('bgcolor' => 'GhostWhite');
  }
  $table->setRowAttributes($nr+1, $hrAttrs, true);
}
//$altRow = array('bgcolor' => 'lightgray');
//$table->altRowAttributes(1, null, $altRow);

for ($cnt = 0; $cnt < count($clinic_caption); $cnt++) {
  $table->setHeaderContents(0, $cnt, $clinic_caption[$cnt]);
}

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
//$table->setColAttributes(0, $hrAttrs);

//echo $table->toHtml();

$smarty->assign("table",$table->toHtml());
$smarty->display("tpl/user_list.tpl");
?>
