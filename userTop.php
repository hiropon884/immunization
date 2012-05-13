<?php
session_start();

// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["clinic_id"])) {

  // 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
  $no_login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/login.php";
  header("Location: {$no_login_url}");
  exit;
}
?>

<html>
<head>
<title>User Top</title>
</head>
<body>
<?php
print 'session_id='.session_id().'<P>';
?>
患者メニュー<P>
<UI>
<LI><a href="patient_reg.php">患者の登録</a><br>
<LI><a href="patient_search.php">患者の検索</a><br>
</UI><P>
病院側メニュー<P>
<UI>
<LI><a href="statistical.php">統計データ表示</a><br>
</UI>
<P>
<a href="logout.php">ログアウト</a><br>

</body>
</html>
