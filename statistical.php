<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Statistical</title>
</head>
<body>
<?php
session_start();

// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["clinic_id"])) {

  // 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
  $no_login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/login.php";
  header("Location: {$no_login_url}");
  exit;
}

print 'session_id='.session_id().'<P>';
?>
何年何月に何の薬がどのくらいいった・いるか？<BR>
患者がどのくらいだったか？<BR>
統計値<BR>
検索<BR>

<P>
<a href="userTop.php">Back to User Top</a><P>

</body>
</html>