<?php
require_once 'HTML/Table.php';

$data = array(
 '0' => array('Bakken', 'Stig', '', 'stig@example.com'),
 '1' => array('Merz', 'Alexander', 'alex.example.com', 'alex@example.com'),
 '2' => array('Daniel', 'Adam', '', '')
);

$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);
$table->setAutoFill('n/a');

for ($nr = 0; $nr < count($data); $nr++) {
  $table->setHeaderContents($nr+1, 0, (string)$nr);
  for ($i = 0; $i < 4; $i++) {
    if ('' != $data[$nr][$i]) {
      $table->setCellContents($nr+1, $i+1, $data[$nr][$i]);
    }
  }
}
$altRow = array('bgcolor' => 'lightgray');
$table->altRowAttributes(1, null, $altRow);

$table->setHeaderContents(0, 0, '病院ID');
$table->setHeaderContents(0, 1, 'パスワード');
$table->setHeaderContents(0, 2, '病院名');
$table->setHeaderContents(0, 3, '病院名（読み）');
$table->setHeaderContents(0, 4, 'メールアドレス');

$table->setHeaderContents(0, 5, '郵便番号');
$table->setHeaderContents(0, 6, '住所１');
$table->setHeaderContents(0, 7, '住所２');
$table->setHeaderContents(0, 8, '電話番号');

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
$table->setColAttributes(0, $hrAttrs);

echo $table->toHtml();

?>