<?php 
session_start();
//ログインしてなければログインページへリダイレクト
if(isset($_SESSION["login"])){
  if ($_SESSION["login"] != true) {
    unset($_SESSION["username"]);
    header("location:login.php");
    exit;
  }
}else{
  header("location:login.php");
    exit;
}


if($_SERVER["REQUEST_METHOD"] ==="GET"){
  //ログアウトボタンが押下された場合にセッションを破棄してログインページへ遷移
  if(isset($_GET["yes"])){
    $_SESSION = []; 
    $params = session_get_cookie_params();
    setcookie(session_name(), "", time() - 36000, 
    $params["path"], $params["domain"], $params["secure"], $params["httponly"] 
    ); 
    session_destroy();
    header("location:login.php");
  }
  //戻るが押下された場合の処理
  if(isset($_GET["no"])){
    header("location:mypage.php");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <title>ログアウト || twitter-demo</title>
  <link href="css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/mypage.css">
</head>

<body class="logout">
  <header>
  <h1><a href="mypage.php"><i class="fab fa-twitter"></i></a> Twitter</h1>
    <i id="nav-show" class="fas fa-bars"></i>
        <div id="nav" class="nav">
          <nav>
            <span id="nav-close" class="nav-close_button">×</span>
              <ul>
                <li><a href="logout.php">ログアウト</a></li>
                <li><a href="signout.php">退会</a></li>
                <li><a href="contact.php">お問い合わせ</a></li>
              </ul>
          </nav>
        </div>
  </header>
  <h2>ログアウトしますか？</h2>
  <form action="" method="get">
    <p><input type="submit" name="yes" value="ログアウト"></p>
    <p><input type="submit" name="no" value="戻る"></p>
  </form>
  <footer>
    <p>
      <span><a href="mypage.php"><i class="fas fa-user"></i></a></span>
      <span><a href="others.php"><i class="fas fa-users"></i></a></span>
      <span><a href="followlist.php"><i class="fas fa-handshake"></i></a></span>
    </p>
  </footer>
  <script src="js/logout.js" async defer></script>
</body>
</html>