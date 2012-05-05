<?php
session_start();

// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["user_name"])) {

  // 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
  $no_login_url = "http://{$_SERVER["HTTP_HOST"]}/login/login.php";
  header("Location: {$no_login_url}");
  exit;
} 
?>

<html>
<head>
<title>test page2</title>
</head>
<body>
<?php
print 'session_id='.session_id().'<P>';
?>
This is a test page.
<a href="logout.php">ログアウト</a><br>
</body>
</html>
