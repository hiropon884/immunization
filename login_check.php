<?php
// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["clinic_id"])) {

  // 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
  $no_login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/login.php";
  header("Location: {$no_login_url}");
  exit;
}
?>